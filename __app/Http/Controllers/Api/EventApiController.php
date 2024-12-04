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
use App\Models\UserInterest;
use App\Models\Event;

class EventApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'club_add_event',
                'club_edit_event',
                'club_delete_event',
                'event_interest',
                'event_inquiry',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'club_add_event': {
                        return $this->club_add_event($request, $User);
                    }
                    break;

                case 'club_edit_event': {
                        return $this->club_edit_event($request, $User);
                    }
                    break;

                case 'club_delete_event': {
                        return $this->club_delete_event($request, $User);
                    }
                    break;

                case 'club_events': {
                        return $this->club_events($request, $User);
                    }
                    break;

                case 'event_details': {
                        return $this->event_details($request, $User);
                    }
                    break;

                case 'upcoming_events': {
                        return $this->upcoming_events($request, $User);
                    }
                    break;

                case 'club_event_attendees_details': {
                        return $this->club_event_attendees_details($request, $User);
                    }
                    break;

                case 'user_event_interests': {
                        return $this->user_event_interests($request, $User);
                    }
                    break;

                case 'event_interest': {
                        return $this->event_interest($request, $User);
                    }
                    break;

                case 'event_inquiry': {
                        return $this->event_inquiry($request, $User);
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

    private function club_add_event(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {

            if (isset($request->title) && ltrim(rtrim($request->title)) != '' && isset($request->description) && ltrim(rtrim($request->description)) != '' && isset($request->age_groups) && ltrim(rtrim($request->age_groups)) != '' && isset($request->start_date_time) && ltrim(rtrim($request->start_date_time)) != '' && isset($request->inquiring) && ltrim(rtrim($request->inquiring)) != '') {
                $title = $request->title;
                $meeting_link = $request->meeting_link;
                $description = $request->description;
                $age_groups = $request->age_groups;

                $start_date_time = strtotime($request->start_date_time);

                $status = 1;
                $inquiring = $request->inquiring;

                $file_banner = "default_image";
                $file_video = "default_image";
                $images_attachment = "default_image";
                $documents = "default_image";
                $videos = "default_image";

                $Event = new Event;
                $Event->user_id = $user_id;
                $Event->title = $title;
                $Event->description = $description;
                $Event->meeting_link = $meeting_link;
                $Event->age_group = $age_groups;
                $Event->start_date_time = $start_date_time;
                $Event->status = 1;
                $Event->inquiry_status = $inquiring;
                $Event->banner = $file_banner;
                $Event->video = $file_video;
                $Event->images = $images_attachment;
                $Event->documents = $documents;
                $Event->videos = $videos;
                $Event->save();
                $event_id = $Event->id;

                $uploadsPath = $this->uploads_events . '/' . $event_id;
                $file_banner = "default_image";
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_banner = $fileName;
                }

                $file_video = "default_image";
                if ($request->hasFile('video')) {
                    $file = $request->file('video');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_video = $fileName;
                }

                $images_attachment = "default_image";
                if ($request->hasFile('attachment')) {
                    $images = $request->attachment;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($images_attachment == '' && $images_attachment == 'default_image')
                            $images_attachment = $fileName;
                        else
                            $images_attachment .= ',' . $fileName;
                    }
                }

                $documents = "default_image";
                if ($request->hasFile('documents')) {
                    $images = $request->documents;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($documents == '' && $documents == 'default_image')
                            $documents = $fileName;
                        else
                            $documents .= ',' . $fileName;
                    }
                }

                $videos = "default_image";
                if ($request->hasFile('videos')) {
                    $images = $request->videos;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($videos == '' && $videos == 'default_image')
                            $videos = $fileName;
                        else
                            $videos .= ',' . $fileName;
                    }
                }

                $Event = Event::find($event_id);
                $Event->banner = $file_banner;
                $Event->video = $file_video;
                $Event->images = $images_attachment;
                $Event->documents = $documents;
                $Event->videos = $videos;
                $Event->save();

                return $this->sendSuccess("Successfully Created Event");
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_edit_event(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_CLUB_USER) {

            if (isset($request->event_id) && ltrim(rtrim($request->event_id)) != '' && isset($request->title) && ltrim(rtrim($request->title)) != '' && isset($request->description) && ltrim(rtrim($request->description)) != '' && isset($request->age_groups) && ltrim(rtrim($request->age_groups)) != '' && isset($request->start_date_time) && ltrim(rtrim($request->start_date_time)) != '' && isset($request->inquiring) && ltrim(rtrim($request->inquiring)) != '') {
                $id = $request->event_id;

                $event = Event::find($id);
                if ($event == null || $event->user_id != $User->id) {
                    return $this->sendError("Event not found");
                }
                $event_id = $id;

                $event->title = $request->title;
                $event->meeting_link = $request->meeting_link;
                $event->description = $request->description;
                $event->age_group = $request->age_groups;
                $event->start_date_time = strtotime($request->start_date_time);
                $event->status = 1;
                $event->inquiry_status = $request->inquiring;

                $uploadsPath = $this->uploads_events . '/' . $event_id;
                $file_banner = $event->banner;
                $old_file = $event->banner;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $file_banner = $fileName;

                    if ($old_file != "" && $old_file != "default_image") {
                        $old_file_path = $uploadsPath . '/' . $old_file;
                        if (file_exists($old_file_path)) {
                            unlink($old_file_path);
                        }
                    }
                }

                $file_video = $event->video;
                $old_file = $event->video;
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

                $images_attachment = $event->images;
                if ($request->hasFile('attachment')) {
                    $images = $request->attachment;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($images_attachment == '' && $images_attachment == 'default_image')
                            $images_attachment = $fileName;
                        else
                            $images_attachment .= ',' . $fileName;
                    }
                }

                $documents = $event->documents;
                if ($request->hasFile('documents')) {
                    $images = $request->documents;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($documents == '' && $documents == 'default_image')
                            $documents = $fileName;
                        else
                            $documents .= ',' . $fileName;
                    }
                }

                $videos = $event->videos;
                if ($request->hasFile('videos')) {
                    $images = $request->videos;
                    foreach ($images as $file) {
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        if ($videos == '' && $videos == 'default_image')
                            $videos = $fileName;
                        else
                            $videos .= ',' . $fileName;
                    }
                }

                $event->banner = $file_banner;
                $event->video = $file_video;
                $event->images = $images_attachment;
                $event->documents = $documents;
                $event->videos = $videos;

                $event->save();

                return $this->sendSuccess("Successfully Updated Event");
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function get_event_array($event, $type = 'full') {
        $event_id = $event->id;
        $user_name = get_user_name($event->user_id);
        $SITE_URL = env('APP_URL');
        $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/event.png";
        $uploadsPath = $SITE_URL . "/" . $this->uploads_events . '/' . $event_id;

        $array = array();
        $array["event_id"] = $event->id;
        $array["event_user_id"] = $event->user_id;
        $array["event_user_name"] = $user_name;
        $array["title"] = $event->title;
        $array["age_group"] = $event->age_group;
        $array["start_date_time"] = date('m/d/Y H:i:s', $event->start_date_time);
        $array["status"] = $event->status;
        $array["inquiry_status"] = $event->inquiry_status;
        $array["description"] = $event->description;
        $banner = NULL;
        if (!empty($event->banner) && $event->banner != 'default_image') {
            $banner = $uploadsPath . "/" . $event->banner;
        }
        $array["banner"] = $banner;

        $attendees = UserInterest::where('event_id', $event_id)->count();

        if ($attendees < 1) {
            $array["attendees"] = 0;
        }
        else {
            $array["attendees"] = 1;
        }

        if ($type == 'full') {
            $array["meeting_link"] = $event->meeting_link;
            $array["video"] = NULL;
            if ($event->video != "" && $event->video != "default_image" && !empty($event->video)) {
                $array["video"] = $uploadsPath . "/" . $event->video;
            }

            $attachments = $event->images;
            $attachments = explode(",", $attachments);

            $event_attachments = array();
            foreach ($attachments as $attachment) {
                if ($attachment != "" && $attachment != 'default_image' && !empty($attachment)) {
                    $event_attachments[] = $uploadsPath . "/" . $attachment;
                }
            }
            $array["attachments"] = $event_attachments;

            $attachments = $event->documents;
            $attachments = explode(",", $attachments);

            $event_attachments = array();
            foreach ($attachments as $attachment) {
                if ($attachment != "" && $attachment != 'default_image' && !empty($attachment)) {
                    $event_attachments[] = $uploadsPath . "/" . $attachment;
                }
            }
            $array["documents"] = $event_attachments;

            $attachments = $event->videos;
            $attachments = explode(",", $attachments);

            $event_attachments = array();
            foreach ($attachments as $attachment) {
                if ($attachment != "" && $attachment != 'default_image' && !empty($attachment)) {
                    $event_attachments[] = $uploadsPath . "/" . $attachment;
                }
            }
            $array["videos"] = $event_attachments;
        }

        return $array;
    }

    private function club_delete_event(Request $request, $User) {
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            if (isset($request->event_id) && ltrim(rtrim($request->event_id)) != '') {
                $id = $request->event_id;
                $model = Event::find($id);
                if ($model == null || $model->user_id != $User->id) {
                    return $this->sendError("Event not found");
                }
                $model->delete();
                return $this->sendSuccess('Successfully Deleted Event');
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_events(Request $request, $User) {
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            $user_id = $User->id;

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

            $result_count = Event::where('user_id', $user_id)->count();
            $total_records = $result_count;
            $total_no_of_pages = ceil($total_records / $limit);

            $count = 0;

            $events = Event::where('user_id', $user_id)
                    ->orderBy('id', 'DESC')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $events_array = [];

            foreach ($events as $event) {
                $count++;
                $events_array[] = $this->get_event_array($event, 'short');
            }

            $data = [
                'page_no'           => $page_no,
                'limit'             => $limit,
                'total_records'     => $total_records,
                'current_count'     => $count,
                'total_no_of_pages' => $total_no_of_pages,
                'events_details'    => $events_array,
            ];
            return $this->sendResponse($data, 'Successfully Retrieved Events');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function event_details(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->event_id) && ltrim(rtrim($request->event_id)) != '') {
            $event_id = $request->event_id;
            $event = Event::find($event_id);
            if (empty($event)) {
                return $this->sendError("Event not found");
            }
            $event_array = $this->get_event_array($event, 'full');
            $data = [
                'event_details' => $event_array,
            ];
            return $this->sendResponse($data, 'Successfully Retrieved Event Details');
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function upcoming_events(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

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

        $current_date_time = time();

        $result_count = Event::where('status', 1)
                ->where('start_date_time', '>', $current_date_time)
                ->count();

        $total_records = $result_count;
        $total_no_of_pages = ceil($total_records / $limit);

        $count = 0;

        $events = Event::where('status', 1)
                ->where('start_date_time', '>', $current_date_time)
                ->orderBy('id', 'DESC')
                ->skip($offset)
                ->take($limit)
                ->get();

        $events_array = [];

        foreach ($events as $event) {
            $count++;
            $events_array[] = $this->get_event_array($event, 'short');
        }

        $data = [
            'page_no'           => $page_no,
            'limit'             => $limit,
            'total_records'     => $total_records,
            'current_count'     => $count,
            'total_no_of_pages' => $total_no_of_pages,
            'events_details'    => $events_array,
        ];
        return $this->sendResponse($data, 'Successfully Retrieved Events');
    }

    private function club_event_attendees_details(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_CLUB_USER) {
            if (isset($request->event_id) && ltrim(rtrim($request->event_id)) != '') {

                $event_id = $request->event_id;

                $users_array = array();
                $user_rows = array();

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

                $model = Event::find($event_id);

                if ($model == null || $model->user_id != $User->id) {
                    return $this->sendError("Event not found");
                }

                $result_count = UserInterest::where('event_id', $event_id)->count();

                $total_records = $result_count;
                $total_no_of_pages = ceil($total_records / $limit);

                $events = UserInterest::select('user_id')
                        ->where('event_id', $event_id)
                        ->orderBy('id', 'DESC')
                        ->skip($offset)
                        ->take($limit)
                        ->get();

                $users_array = [];
                $count = 0;
                foreach ($events as $event) {
                    $count++;
                    $event_user_id = $event->user_id;

                    $user_rows["event_user_id"] = $event_user_id;
                    $user_rows["event_user_name"] = User::find($event_user_id)->name;

                    $user_type = User::find($event_user_id)->user_type;
                    if ($user_type == $this->_COACH_USER) {
                        $user_rows["event_user_type"] = "Coach";
                    }
                    elseif ($user_type == $this->_PLAYER_USER) {
                        $user_rows["event_user_type"] = "Player";
                    }
                    else {
                        $user_rows["event_user_type"] = "Unknown";
                    }

                    // $user_rows["event_user_zip_code"] = get_user_profile_data('zip_code', $event_user_id);
                    $user_rows["event_user_zip_code"] = '676868';

                    // $image = get_user_profile_data('coachpic', $event_user_id);
                    $image = 'user.png';
                    if ($image != "") {
                        $user_rows["event_user_image"] = $SITE_URL . "/uploads/images/" . $image;
                    }

                    $users_array[] = $user_rows;
                }

                $data = [
                    'page_no'              => $page_no,
                    'limit'                => $limit,
                    'total_records'        => $total_records,
                    'current_count'        => $count,
                    'total_no_of_pages'    => $total_no_of_pages,
                    'events_users_details' => $users_array,
                ];

                return $this->sendResponse($data, 'Successfully Retrieved Events Users Data');
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function user_event_interests(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {

            $events_array = array();
            $event_rows = array();

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

            $result_count = UserInterest::join('events', 'events.id', '=', 'user_interests.event_id')
                    ->where('user_interests.user_id', $user_id)
                    ->selectRaw('COUNT(*) AS total_records')
                    ->first();

            $total_records = $result_count->total_records;
            $total_no_of_pages = ceil($total_records / $limit);

            $count = 0;
            $events = UserInterest::join('events', 'events.id', '=', 'user_interests.event_id')
                    ->where('user_interests.user_id', $user_id)
                    ->select('events.*', 'user_interests.id AS interest_id')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $events_array = [];

            foreach ($events as $event) {
                $count++;
                $row = $this->get_event_array($event, 'short');
                $row['interest_id'] = $event->interest_id;
                $events_array[] = $row;
            }

            $data = [
                'page_no'                  => $page_no,
                'limit'                    => $limit,
                'total_records'            => $total_records,
                'current_count'            => $count,
                'total_no_of_pages'        => $total_no_of_pages,
                'events_interests_details' => $events_array,
            ];

            return $this->sendResponse($data, 'Successfully Retrieved Events Users Data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function event_interest(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
            if (isset($request->event_id) && ltrim(rtrim($request->event_id)) != '' && isset($request->interest) && ltrim(rtrim($request->interest)) != '') {
                if ($request->interest == 0 || $request->interest == 1) {
                    $event_id = $request->event_id;
                    $model = Event::find($event_id);
                    if ($model == null) {
                        return $this->sendError("Event not found");
                    }
                    $interest = $request->interest;

                    if ($interest == 1) {
                        $userEventInterest = new UserInterest();
                        $userEventInterest->user_id = $user_id;
                        $userEventInterest->event_id = $event_id;
                        $userEventInterest->save();

                        return $this->sendSuccess("Interest Inserted Successfully");
                    }
                    else {
                        UserInterest::where('user_id', $user_id)
                                ->where('event_id', $event_id)
                                ->delete();

                        return $this->sendSuccess("Interest Removed Successfully");
                    }
                }
                else {
                    return $this->sendError('Incorrect Interest Value');
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

    private function event_inquiry(Request $request, $User) {
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
            if (isset($request->event_id) && ltrim(rtrim($request->event_id)) != '' && isset($request->inquiry) && ltrim(rtrim($request->inquiry)) != '') {
                $event_id = $request->event_id;
                $response = $this->send_inquiry_email($request, $User, $event_id);

                if ($response['responseStatus'] === FALSE) {
                    return $this->sendError($response['responseText']);
                }
                else {
                    return $this->sendSuccess($response['responseText']);
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

}
