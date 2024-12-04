<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use DateTime;
use File;
use App\Models\AuthKey;
use App\Models\User;
use App\Models\Video;
use App\Models\Category;
use Carbon\Carbon;
use App\Traits\ImageTrait;

class VideoApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'video_add',
                'video_edit',
                'video_delete',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'video_add': {
                        return $this->video_add($request, $User);
                    }
                    break;

                case 'video_edit': {
                        return $this->video_edit($request, $User);
                    }
                    break;

                case 'video_delete': {
                        return $this->video_delete($request, $User);
                    }
                    break;

                case 'video_details': {
                        return $this->video_details($request, $User);
                    }
                    break;

                case 'related_videos': {
                        return $this->related_videos($request, $User);
                    }
                    break;

                case 'player_videos': {
                        return $this->player_videos($request, $User);
                    }
                    break;

                case 'coach_videos': {
                        return $this->coach_videos($request, $User);
                    }
                    break;

                case 'club_videos': {
                        return $this->club_videos($request, $User);
                    }
                    break;

                default: {
                        return $this->sendError('Invalid Request');
                    }
                    break;
            }
        }
        else{
            return $this->sendError($result['message']);
        }
    }

    private function video_add(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_COACH_USER || $user_type == $this->_CLUB_USER) {

            if (isset($request->category) && ltrim(rtrim($request->category)) != '' && isset($request->recipients) && ltrim(rtrim($request->recipients)) != '' && isset($request->status) && ltrim(rtrim($request->status)) != '' && isset($request->duration) && ltrim(rtrim($request->duration)) != '') {

                $date_of_creation = date('Y-m-d');
                $created_at = date("Y-m-d h:i:sa");
                $file_print = "default_image";
                $file_video = "default_image";
                $file_images = "default_image";

                $video = new Video();
                $video->user_id = $user_id;
                $video->title = $request->title;
                $video->print_title = $request->print_title;
                $video->category = $request->category;
                $video->date_of_creation = $date_of_creation;
                $video->duration = $request->duration;
                $video->description = $request->description;
                $video->print_description = $request->print_description;
                $video->print_image = $file_print;
                $video->video = $file_video;
                $video->image = $file_images;
                $video->author = $user_id;
                $video->recipients = $request->recipients;
                $video->status = $request->status;
                $video->created_by = $user_id;
                $video->created_at = $created_at;
                $video->save();

                $video_id = $video->id;

                $uploadsPath = $this->uploads_videos . '/' . $video_id;
                $file_print = "default_image";
                if ($request->hasFile('print_image')) {
                    $file = $request->file('print_image');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_print = $fileName;
                }

                $file_video = "default_image";
                if ($request->hasFile('video')) {
                    $file = $request->file('video');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_video = $fileName;
                }

                $file_images = "default_image";
                if ($request->hasFile('image')) {
                    $images = $request->image;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($file_images == '' && $file_images == 'default_image')
                            $file_images = $fileName;
                        else
                            $file_images .= ',' . $fileName;
                    }
                }

                $Video = Video::find($video_id);
                $Video->print_image = $file_print;
                $Video->video = $file_video;
                $Video->image = $file_images;
                $Video->save();

                return $this->sendSuccess("Successfully Added Video");
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function video_edit(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_COACH_USER || $user_type == $this->_CLUB_USER) {

            if (isset($request->video_id) && ltrim(rtrim($request->video_id)) != '' && isset($request->category) && ltrim(rtrim($request->category)) != '' && isset($request->recipients) && ltrim(rtrim($request->recipients)) != '' && isset($request->status) && ltrim(rtrim($request->status)) != '' && isset($request->duration) && ltrim(rtrim($request->duration)) != '') {

                $video_id = $request->video_id;
                $video = Video::find($video_id);
                if ($video == null || $video->user_id != $User->id) {
                    return $this->sendError("Video not found");
                }


                $uploadsPath = $this->uploads_videos . '/' . $video_id;
                $file_print = $video->print_image;
                $old_file = $video->print_image;
                if ($request->hasFile('print_image')) {
                    $file = $request->file('print_image');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_print = $fileName;

                    if ($old_file != "" && $old_file != "default_image") {
                        $old_file_path = $uploadsPath . '/' . $old_file;
                        if (file_exists($old_file_path)) {
                            unlink($old_file_path);
                        }
                    }
                }

                $file_video = $video->video;
                $old_file = $video->video;
                if ($request->hasFile('video')) {
                    $file = $request->file('video');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_video = $fileName;

                    if ($old_file != "" && $old_file != "default_image") {
                        $old_file_path = $uploadsPath . '/' . $old_file;
                        if (file_exists($old_file_path)) {
                            unlink($old_file_path);
                        }
                    }
                }

                $file_images = $video->image;
                if ($request->hasFile('image')) {
                    $images = $request->image;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($file_images == '' && $file_images == 'default_image')
                            $file_images = $fileName;
                        else
                            $file_images .= ',' . $fileName;
                    }
                }

                $date_of_creation = date('Y-m-d');

                $created_at = date("Y-m-d h:i:sa");

                $updated_at = date("Y-m-d h:i:sa");

                $video->title = $request->title;
                $video->category = $request->category;
                $video->date_of_creation = $date_of_creation;
                $video->duration = $request->duration;
                $video->description = $request->description;
                $video->recipients = $request->recipients;
                $video->print_image = $file_print;
                $video->video = $file_video;
                $video->image = $file_images;
                $video->status = $request->status;
                $video->updated_by = $user_id;
                $video->updated_at = $updated_at;
                $video->save();

                return $this->sendSuccess("Successfully Updated Video");
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function video_delete(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_COACH_USER || $user_type == $this->_CLUB_USER) {

            if (isset($request->video_id) && ltrim(rtrim($request->video_id)) != '') {

                $id = $request->video_id;
                $video = Video::find($id);

                if ($video == null || $video->user_id != $User->id) {
                    return $this->sendError("Video not found");
                }
                $video->delete();

                return $this->sendSuccess("Successfully Deleted Video");
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function get_video_array($video, $type = 'full') {
        $video_id = $video->id;
        $SITE_URL = env('APP_URL');
        $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/video.png";
        $uploadsPath = $SITE_URL . "/" . $this->uploads_videos . '/' . $video_id;
        $user_name = get_user_name($video->user_id);
        $author_name = get_user_name($video->author);
        $category_name = get_category($video->category);
        $array = array();
        $array["id"] = $video->id;
        $array["user_id"] = $video->user_id;
        $array["user_name"] = $user_name;
        $array["category_id"] = $video->category;
        $array["category"] = $category_name;
        $array["category_name"] = $category_name;
        $array["title"] = $video->title;
        $array["duration"] = $video->duration;
        $array["date_of_creation"] = Carbon::parse($video->date_of_creation)->format('Y-m-d');

        if ($type == 'short') {
            $video_image = $defaultImage;
            if (!empty($video->image) && $video->image != 'default_image') {
                $video->image = trim(str_replace('default_image,', '', $video->image));
                $video->image = trim(str_replace(',default_image', '', $video->image));
                $video->image = trim(str_replace('default_image', '', $video->image));
                $image = $video->image;
                $arr = explode(",", $image);
                $video_image = $uploadsPath . "/" . $arr[0];
            }
            $array["image"] = $video_image;
        }
        else {
            $array["description"] = $video->description;
            $array["recipients"] = $video->recipients;
            $array["author"] = $author_name;
            $array["status"] = $video->status;

            $video_url = NULL;
            if (!empty($video->video) && $video->video != 'default_image') {
                $video_url = $uploadsPath . "/" . $video->video;
            }
            $array["video"] = $video_url;

            $images_array = [];
            $images = explode(",", $video->image);
            $count = count($images);
            foreach ($images as $image) {
                if ((empty($image) || $image == 'default_image') && $count > 1) {
                    
                }
                else {
                    $video_image = $defaultImage;
                    if (!empty($image) && $image != 'default_image') {
                        $video_image = $uploadsPath . "/" . $image;
                    }
                    $images_array[] = $video_image;
                }
            }
            $array["images"] = $images_array;
        }

        return $array;
    }

    private function video_details(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->video_id) && ltrim(rtrim($request->video_id)) != '') {
            $video_id = $request->video_id;
            $video = Video::find($video_id);
            if (empty($video)) {
                return $this->sendError("Video not found");
            }
            $video_array = $this->get_video_array($video, 'full');

            $data = [
                'video_details' => $video_array,
            ];
            return $this->sendResponse($data, 'Successfully Retrieved Video Details');
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function related_videos(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if (isset($request->video_id) && ltrim(rtrim($request->video_id)) != '' && isset($request->user_id) && ltrim(rtrim($request->user_id)) != '' && isset($request->category) && ltrim(rtrim($request->category)) != '') {

            $video_id = $request->video_id;
            $video_user_id = $request->user_id;
            $category_id = $request->category;
            $user_type = get_user_type($user_id);

            $videosQuery = Video::where('category', $category_id)
                    ->where('user_id', $video_user_id)
                    ->where('status', 1)
                    ->where('id', '!=', $video_id);

            if ($user_type == $this->_COACH_USER) {
                $videosQuery->where(function ($query) {
                    $query->where('recipients', 1)
                            ->orWhere('recipients', 2);
                });
            }
            elseif ($user_type == $this->_PLAYER_USER) {
                $videosQuery->where(function ($query) {
                    $query->where('recipients', 1)
                            ->orWhere('recipients', 3);
                });
            }

            $videosQuery->orderBy('id', 'desc')->limit(10);
            $videos = $videosQuery->get();

            $videos_array = array();
            foreach ($videos as $video) {
                $videos_array[] = $this->get_video_array($video, 'short');
            }

            $data = [
                'video_details' => $videos_array,
            ];
            return $this->sendResponse($data, 'Successfully Retrieved Related Details');
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function player_videos(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_PLAYER_USER) {

            if (isset($request->category) && $request->category != "") {

                $category = $request->category;

                if (isset($request->listing) && $request->listing != "") {
                    $listing = $request->listing;
                }
                else {
                    $listing = "both";
                }

                $coach_listing = 0;
                $club_listing = 0;

                $current_time = (time() - (24 * 60 * 60));

                if (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) {
                    $page_no = $request->page_no;
                }
                else {
                    $page_no = 1;
                }

                if (isset($request->limit) && $request->limit != "" && $request->limit != 0) {
                    $limit = $request->limit;
                }
                else {
                    $limit = 5;
                }

                $offset = ($page_no - 1) * $limit;

                $coach_videos_array = array();
                $coach_video_rows = array();

                $club_videos_array = array();
                $club_video_rows = array();

                if ($listing == "coach" || $listing == "both") {
                    $coach_listing = 1;

                    $coachTotalRecords = Video::join('bookings', function ($join) use ($user_id) {
                                $join->on('videos.user_id', '=', 'bookings.user_id')
                                ->where(function ($query) use ($user_id) {
                                    $query->where('bookings.status', '=', '2')
                                    ->orWhere('bookings.status', '=', '7')
                                    ->orWhere('bookings.status', '=', '8');
                                })
                                ->where('bookings.req_user_id', '=', $user_id);
                            })
                            ->where('videos.status', '=', '1')
                            ->where(function ($query) {
                        $query->where('videos.recipients', '=', '1')
                        ->orWhere('videos.recipients', '=', '2');
                    });

                    if ($category != 0) {
                        $coachTotalRecords->where('videos.category', '=', $category);
                    }

                    $coach_total_records = $coachTotalRecords->count();
                    $coach_total_no_of_pages = ceil($coach_total_records / $limit);
                    $coach_count = 0;

                    $coachVideos = Video::join('bookings', function ($join) use ($user_id) {
                                $join->on('videos.user_id', '=', 'bookings.user_id')
                                ->where(function ($query) use ($user_id) {
                                    $query->where('bookings.status', '=', '2')
                                    ->orWhere('bookings.status', '=', '7')
                                    ->orWhere('bookings.status', '=', '8');
                                })
                                ->where('bookings.req_user_id', '=', $user_id);
                            })
                            ->where('videos.status', '=', '1')
                            ->where(function ($query) {
                        $query->where('videos.recipients', '=', '1')
                        ->orWhere('videos.recipients', '=', '2');
                    });

                    if ($category != 0) {
                        $coachVideos->where('videos.category', '=', $category);
                    }

                    $coachVideos = $coachVideos->orderBy('videos.id', 'DESC')
                            ->limit($limit)
                            ->offset($offset)
                            ->get();

                    foreach ($coachVideos as $videos) {
                        $coach_count++;
                        $coach_videos_array[] = $this->get_video_array($videos, 'short');
                    }
                }



                if ($listing == "club" || $listing == "both") {
                    $club_listing = 1;

                    $club_id = get_club_id($user_id);

                    $clubTotalRecords = Video::where('user_id', '=', $club_id)
                            ->where('status', '=', '1')
                            ->where(function ($query) {
                        $query->where('recipients', '=', '2')
                        ->orWhere('recipients', '=', '1');
                    });

                    if ($category != 0) {
                        $clubTotalRecords->where('category', '=', $category);
                    }

                    $club_total_records = $clubTotalRecords->count();
                    $club_total_no_of_pages = ceil($club_total_records / $limit);
                    $club_count = 0;

                    $clubVideos = Video::where('user_id', '=', $club_id)
                            ->where('status', '=', '1')
                            ->where(function ($query) {
                        $query->where('recipients', '=', '2')
                        ->orWhere('recipients', '=', '1');
                    });

                    if ($category != 0) {
                        $clubVideos->where('category', '=', $category);
                    }

                    $clubVideos = $clubVideos->orderBy('id', 'DESC')
                            ->limit($limit)
                            ->offset($offset)
                            ->get();

                    foreach ($clubVideos as $videos) {
                        $club_count++;
                        $club_videos_array[] = $this->get_video_array($videos, 'short');
                    }
                }



                if ($coach_listing == 1 || $club_listing == 1) {
                    $data = [
                        'page_no' => $page_no,
                        'limit'   => $limit,
                        'listing' => $listing,
                    ];

                    if ($coach_listing == 1) {
                        $data['coach_total_records'] = $coach_total_records;
                        $data['coach_current_count'] = $coach_count;
                        $data['coach_total_no_of_pages'] = $coach_total_no_of_pages;
                        $data['coach_videos_details'] = $coach_videos_array;
                    }

                    if ($club_listing == 1) {
                        $data['club_total_records'] = $club_total_records;
                        $data['club_current_count'] = $club_count;
                        $data['club_total_no_of_pages'] = $club_total_no_of_pages;
                        $data['club_videos_details'] = $club_videos_array;
                    }
                    return $this->sendResponse($data, 'Successfully returned Videos data');
                }
                else {
                    return $this->sendError("Incorrect Listing value");
                }
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function coach_videos(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER) {
            $category = 0;
            if (isset($request->category) && $request->category != "") {
                $category = $request->category;
            }

            if (isset($request->listing) && $request->listing != "") {
                $listing = strtolower($request->listing);
            }
            else {
                $listing = "both";
            }

            $coach_listing = 0;
            $club_listing = 0;

            $current_time = (time() - (24 * 60 * 60));

            if (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) {
                $page_no = $request->page_no;
            }
            else {
                $page_no = 1;
            }

            if (isset($request->limit) && $request->limit != "" && $request->limit != 0) {
                $limit = $request->limit;
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $coach_videos_array = array();
            $club_videos_array = array();

            if ($listing == "coach" || $listing == "both") {
                $coach_listing = 1;

                $coachVideosQuery = Video::where('user_id', $user_id);

                if ($category != 0) {
                    $coachVideosQuery->where('category', $category);
                }
                $coachVideosQuery->where('status', 1);

                $coach_total_records = $coachVideosQuery->count();
                $coach_total_no_of_pages = ceil($coach_total_records / $limit);
                $coach_count = 0;

                $coachVideos = $coachVideosQuery->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();

                foreach ($coachVideos as $videos) {
                    $coach_count++;
                    $coach_videos_array[] = $this->get_video_array($videos, 'short');
                }
            }

            if ($listing == "club" || $listing == "both") {
                $club_listing = 1;

                $club_id = get_club_id($user_id);

                $clubVideosQuery = Video::where('user_id', $club_id)
                        ->where('status', 1);
                //->whereIn('recipients', [1, 3]);

                if ($category != 0) {
                    $clubVideosQuery->where('category', $category);
                }

                $club_total_records = $clubVideosQuery->count();
                $club_total_no_of_pages = ceil($club_total_records / $limit);
                $club_count = 0;

                $clubVideos = $clubVideosQuery->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();

                foreach ($clubVideos as $videos) {
                    $club_count++;
                    $club_videos_array[] = $this->get_video_array($videos, 'short');
                }
            }

            if ($coach_listing == 1 || $club_listing == 1) {
                $data = [
                    'page_no'  => $page_no,
                    'limit'    => $limit,
                    'listing'  => $listing,
                    'category' => $category,
                ];

                if ($coach_listing == 1) {
                    $data['coach_total_records'] = $coach_total_records;
                    $data['coach_current_count'] = $coach_count;
                    $data['coach_total_no_of_pages'] = $coach_total_no_of_pages;
                    $data['coach_videos_details'] = $coach_videos_array;
                }

                if ($club_listing == 1) {
                    $data['club_total_records'] = $club_total_records;
                    $data['club_current_count'] = $club_count;
                    $data['club_total_no_of_pages'] = $club_total_no_of_pages;
                    $data['club_videos_details'] = $club_videos_array;
                }
                return $this->sendResponse($data, 'Successfully returned Videos data');
            }
            else {
                return $this->sendError("Incorrect Listing value");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_videos(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            $category = 0;
            if (isset($request->category) && $request->category != "") {
                $category = $request->category;
            }

            if (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) {
                $page_no = $request->page_no;
            }
            else {
                $page_no = 1;
            }

            if (isset($request->limit) && $request->limit != "" && $request->limit != 0) {
                $limit = $request->limit;
            }
            else {
                $limit = 5;
            }

            $offset = ($page_no - 1) * $limit;

            $club_videos_array = array();
            $club_video_rows = array();

            $sql = Video::where('user_id', $user_id);

            if ($category != 0) {
                $sql->where('category', $category);
            }
            $sql->where('status', 1);

            $club_total_records = $sql->count();
            $club_total_no_of_pages = ceil($club_total_records / $limit);
            $club_count = 0;

            $clubVideos = $sql->orderBy('id', 'DESC')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();

            foreach ($clubVideos as $videos) {
                $club_count++;
                $club_videos_array[] = $this->get_video_array($videos, 'short');
            }

            $data = [
                'page_no'                => $page_no,
                'limit'                  => $limit,
                'category'               => $category,
                'club_total_records'     => $club_total_records,
                'club_current_count'     => $club_count,
                'club_total_no_of_pages' => $club_total_no_of_pages,
                'club_videos_details'    => $club_videos_array,
            ];
            return $this->sendResponse($data, 'Successfully returned Videos data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

}
