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
use App\Models\TrainingPlan;
use App\Models\TrainingPlanDetail;
use Carbon\Carbon;
use PDF;

class TrainingPlanApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'create_training_plan',
                'delete_training_plan',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'create_training_plan': {
                        return $this->create_training_plan($request, $User);
                    }
                    break;

                case 'coach_training_plans': {
                        return $this->coach_training_plans($request, $User);
                    }
                    break;

                case 'coach_training_plan_details': {
                        return $this->coach_training_plan_details($request, $User);
                    }
                    break;

                case 'coach_training_plan_videos': {
                        return $this->coach_training_plan_videos($request, $User);
                    }
                    break;

                case 'delete_training_plan': {
                        return $this->delete_training_plan($request, $User);
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

    private function create_training_plan(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_COACH_USER) {

            if (isset($request->video_ids) && ltrim(rtrim($request->video_ids)) != '' && isset($request->title) && ltrim(rtrim($request->title)) != '') {

                $slots = $request->video_ids;
                $ids = explode(",", $slots);

                $name = $request->title;
                $pdf_name_store = 'default_image';

                $plan = new TrainingPlan();
                $plan->user_id = $user_id;
                $plan->plan_name = $name;
                $plan->status = 1;
                $plan->pdf_file = $pdf_name_store;
                $plan->created_by = $user_id;
                $plan->save();

                $plan_id = $plan->id;

                foreach ($ids as $row) {
                    $planDetail = new TrainingPlanDetail();
                    $planDetail->plan_id = $plan_id;
                    $planDetail->video_id = $row;
                    $planDetail->created_by = $user_id;
                    $planDetail->save();
                }

                $this->create_training_plan_pdf($plan_id);

                return $this->sendSuccess("Successfully Created Training Plan");
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function create_training_plan_pdf($plan_id) {
        $SITE_URL = env('APP_URL');
        $uploadsPath = $this->uploads_plans . '/' . $plan_id;
        $this->create_uploads_directory($uploadsPath);
        $plan = TrainingPlan::find($plan_id);
        $old_file = $plan->pdf_file;
        if ($old_file != "" && $old_file != "default_image") {
            $old_file_path = $uploadsPath . '/' . $old_file;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }
        $video_uploadsPath = $SITE_URL . "/" . $this->uploads_videos;

        $planDetails = TrainingPlanDetail::leftJoin('videos', 'videos.id', '=', 'training_plan_details.video_id')
                        ->where('training_plan_details.plan_id', $plan_id)
                        ->select('videos.*')->get();
        $data = ['plan' => $plan, 'planDetails' => $planDetails, 'uploadsPath' => $SITE_URL . "/" . $uploadsPath, 'video_uploadsPath' => $video_uploadsPath];

        $code = rand(1000, 9999);
        $fileName = $code . '-' . time() . '.pdf';
        $plan->pdf_file = $fileName;
        $plan->save();
        $pdf = PDF::loadView('backend.pdf_training_plan', $data)->save($uploadsPath . '/' . $fileName);
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
            foreach ($images as $image) {
                $video_image = $defaultImage;
                if (!empty($image) && $image != 'default_image') {
                    $video_image = $uploadsPath . "/" . $image;
                }
                $images_array[] = $video_image;
            }
            $array["images"] = $images_array;
        }

        return $array;
    }

    private function coach_training_plans(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_COACH_USER) {

            $training_plans_array = array();
            $training_plan_rows = array();
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

            $total_records = TrainingPlan::where('user_id', $user_id)->count();
            $total_no_of_pages = ceil($total_records / $limit);

            $training_plans = TrainingPlan::select('id', 'plan_name', 'status', 'pdf_file')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'desc')
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
            $count = $training_plans->count();
            $training_plans_array = $training_plans->map(function ($plan) use ($SITE_URL) {
                        $uploadsPath = $SITE_URL . "/" . $this->uploads_plans . '/' . $plan->id;
                        return [
                    'id'        => $plan->id,
                    'plan_name' => $plan->plan_name,
                    'status'    => $plan->status,
                    'pdf_file'  => $uploadsPath . '/' . $plan->pdf_file,
                        ];
                    })->all();

            $data = [
                'page_no'           => $page_no,
                'limit'             => $limit,
                'total_records'     => $total_records,
                'current_count'     => $count,
                'total_no_of_pages' => $total_no_of_pages,
                'training_plans'    => $training_plans_array,
            ];

            return $this->sendResponse($data, 'Successfully Retrieved Training Plans');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function coach_training_plan_details(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_COACH_USER) {

            if (isset($request->plan_id) && $request->plan_id != "" && $request->plan_id != 0) {
                $plan_id = $request->plan_id;
                $plan = TrainingPlan::find($plan_id);
                if (empty($plan)) {
                    return $this->sendError("Training Plan Not Found");
                }
                $SITE_URL = env('APP_URL');
                $uploadsPath = $SITE_URL . "/" . $this->uploads_plans . '/' . $plan->id;

                $plan_array = array();
                $plan_array['id'] = $plan->id;
                $plan_array['plan_name'] = $plan->plan_name;
                $plan_array['pdf_file'] = $uploadsPath . '/' . $plan->pdf_file;
                $plan_details = array();
                $planDetails = TrainingPlanDetail::leftJoin('videos', 'videos.id', '=', 'training_plan_details.video_id')
                                ->where('training_plan_details.plan_id', $plan_id)
                                ->select('training_plan_details.id as plan_detail_id', 'videos.id', 'videos.user_id', 'videos.author', 'videos.category', 'videos.title', 'videos.duration', 'videos.date_of_creation', 'videos.image', 'videos.description', 'videos.status', 'videos.recipients', 'videos.video')->get();
                foreach ($planDetails as $planDetail) {
                    $row = $this->get_video_array($planDetail, 'short');
                    $row['plan_detail_id'] = $planDetail->plan_detail_id;
                    $plan_details[] = $row;
                }
                $plan_array['plan_details'] = $plan_details;

                $data = [
                    'plan' => $plan_array,
                ];

                return $this->sendResponse($data, 'Successfully Retrieved Training Plan Details');
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function coach_training_plan_videos(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if ($user_type == $this->_COACH_USER) {

            $videos_array = array();
            $video_rows = array();

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

            $club_id = get_club_id($user_id);

            $offset = ($page_no - 1) * $limit;

            $total_records = Video::where('user_id', $club_id)
                    ->whereIn('recipients', ['3', '1'])
                    ->orWhere('user_id', $user_id)
                    ->where('status', '1')
                    ->when($request->filled('category'), function ($query) use ($request) {
                        return $query->where('category', $request->category);
                    })
                    ->count();

            $total_no_of_pages = ceil($total_records / $limit);

            $count = 0;

            $videos = Video::where('user_id', $club_id)
                    ->whereIn('recipients', ['3', '1'])
                    ->orWhere('user_id', $user_id)
                    ->where('status', '1')
                    ->when($request->filled('category'), function ($query) use ($request) {
                        return $query->where('category', $request->category);
                    })
                    ->orderBy('id', 'desc')
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

            $videos_array = [];
            foreach ($videos as $video) {
                $count++;
                $videos_array[] = $this->get_video_array($video, 'full');
            }

            $data = [
                'page_no'           => $page_no,
                'limit'             => $limit,
                'total_records'     => $total_records,
                'current_count'     => $count,
                'total_no_of_pages' => $total_no_of_pages,
                'videos'            => $videos_array,
            ];

            return $this->sendResponse($data, 'Successfully Retrieved Videos');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function delete_training_plan(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_COACH_USER) {

            if (isset($request->plan_id) && ltrim(rtrim($request->plan_id)) != '') {

                $id = $request->plan_id;

                TrainingPlan::where('id', $id)->delete();
                TrainingPlanDetail::where('plan_id', $id)->delete();
                return $this->sendSuccess("Successfully Deleted Training Plan");
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
