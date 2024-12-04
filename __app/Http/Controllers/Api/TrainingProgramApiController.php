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
use App\Models\TrainingProgram;
use App\Models\TrainingProgramDetail;
use PDF;

class TrainingProgramApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'club_add_training_program',
                'club_training_program_delete',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'club_add_training_program': {
                        return $this->club_add_training_program($request, $User);
                    }
                    break;

                case 'coach_player_training_programs': {
                        return $this->coach_player_training_programs($request, $User);
                    }
                    break;

                case 'club_training_programs': {
                        return $this->club_training_programs($request, $User);
                    }
                    break;

                case 'club_training_program_view': {
                        return $this->club_training_program_view($request, $User);
                    }
                    break;

                case 'club_training_program_delete': {
                        return $this->club_training_program_delete($request, $User);
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

    private function club_add_training_program(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');
        if ($user_type == $this->_CLUB_USER) {

            if (isset($request->program_title) && ltrim(rtrim($request->program_title)) != '' && isset($request->variant_title) && isset($request->duration) && isset($request->start_date_time)) {

                $program = new TrainingProgram();
                $program->user_id = $user_id;
                $program->title = $request->program_title;
                $program->status = 1;
                $program->created_by = $user_id;
                $program->save();

                $program_id = $program->id;
                $uploadsPath = $this->uploads_trainings . '/' . $program_id;

                $variant_title = $request->variant_title;
                $description = $request->description;
                $duration = $request->duration;
                $start_date_time = $request->start_date_time;

                $length = count($variant_title);
                for ($i = 0; $i < $length; $i++) {

                    $images = "default_image";
                    if (isset($request->attachment[$i]) && !empty($request->attachment[$i])) {
                        $file = $request->file('attachment')[$i];
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        $images = $fileName;
                    }

                    $documents = "default_image";
                    if (isset($request->documents[$i]) && !empty($request->documents[$i])) {
                        $file = $request->file('documents')[$i];
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        $documents = $fileName;
                    }

                    $videos = "default_image";
                    if (isset($request->videos[$i]) && !empty($request->videos[$i])) {
                        $file = $request->file('videos')[$i];
                        $fileName = $this->upload_file_to_path($file, $uploadsPath);
                        $videos = $fileName;
                    }

                    $programDetail = new TrainingProgramDetail();
                    $programDetail->program_id = $program_id;
                    $programDetail->title = $variant_title[$i];
                    $programDetail->description = $description[$i];
                    $programDetail->duration = $duration[$i];
                    $programDetail->start_date_time = $start_date_time[$i];
                    $programDetail->images = $images;
                    $programDetail->documents = $documents;
                    $programDetail->videos = $videos;
                    $programDetail->save();
                }

                $this->create_training_program_pdf($program_id);

                return $this->sendSuccess("Successfully Created Training Program");
            }
            else {
                return $this->sendError("Missing fields");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function create_training_program_pdf($program_id) {
        $SITE_URL = env('APP_URL');
        $uploadsPath = $this->uploads_trainings . '/' . $program_id;
        $program = TrainingProgram::find($program_id);
        $old_file = $program->pdf_file;
        if ($old_file != "" && $old_file != "default_image") {
            $old_file_path = $uploadsPath . '/' . $old_file;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }
        $programDetails = TrainingProgramDetail::where('program_id', $program_id)->get();
        $data = ['program' => $program, 'programDetails' => $programDetails, 'uploadsPath' => $SITE_URL . "/" . $uploadsPath];

        $code = rand(1000, 9999);
        $fileName = $code . '-' . time() . '.pdf';
        $program->pdf_file = $fileName;
        $program->save();
        $pdf = PDF::loadView('backend.pdf_training_program', $data)->save($uploadsPath . '/' . $fileName);
    }

    private function get_program_array($program, $type = 'full') {
        $program_id = $program->id;
        $SITE_URL = env('APP_URL');
        $uploadsPath = $SITE_URL . "/" . $this->uploads_trainings . '/' . $program_id;
        $user_name = get_user_name($program->user_id);

        $array = array();
        $array["id"] = $program->id;
        $array["program_id"] = $program->id;
        $array["user_id"] = $program->user_id;
        $array["user_name"] = $user_name;
        $array["title"] = $program->title;
        $array["status"] = $program->status;
        $array["pdf_file"] = $uploadsPath . "/" . $program->pdf_file;

        if ($type == 'short') {
            $array["variants"] = TrainingProgramDetail::where('program_id', $program->id)->count();
        }
        else {
            $programDetails = TrainingProgramDetail::where('program_id', $program_id)->get();
            $array['no_of_variants'] = $programDetails->count();
            $variants_array = array();
            foreach ($programDetails as $detail) {

                $attachments = $detail->images;
                if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
                    $attachments = $uploadsPath . "/" . $attachments;
                }
                else {
                    $attachments = null;
                }

                $documents = $detail->documents;
                if ($documents != "" && $documents != 'default_image' && !empty($documents)) {
                    $documents = $uploadsPath . "/" . $documents;
                }
                else {
                    $documents = null;
                }

                $videos = $detail->videos;
                if ($videos != "" && $videos != 'default_image' && !empty($videos)) {
                    $videos = $uploadsPath . "/" . $videos;
                }
                else {
                    $videos = null;
                }

                $variants_rows = [
                    'id'              => $detail->id,
                    'title'           => $detail->title,
                    'description'     => $detail->description,
                    'duration'        => $detail->duration,
                    'start_date_time' => $detail->start_date_time,
                    'attachment'      => $attachments,
                    'documents'       => $documents,
                    'videos'          => $videos,
                ];

                $variants_array[] = $variants_rows;
            }

            $array["variants"] = $variants_array;
        }

        return $array;
    }

    private function coach_player_training_programs(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {

            $club_id = get_club_id($user_id);

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

            $total_records = TrainingProgram::where('user_id', $club_id)
                    ->where('status', 1)
                    ->count();

            $total_no_of_pages = ceil($total_records / $limit);

            $count = 0;
            $training_programs_array = array();

            $programs = TrainingProgram::where('user_id', $club_id)
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
            foreach ($programs as $program) {
                $count++;
                $training_programs_array[] = $this->get_program_array($program, 'short');
            }

            $data = [
                'page_no'           => $page_no,
                'limit'             => $limit,
                'total_records'     => $total_records,
                'current_count'     => $count,
                'total_no_of_pages' => $total_no_of_pages,
                'training_programs' => $training_programs_array,
            ];

            return $this->sendResponse($data, 'Successfully Retrieved Training Programs');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_training_programs(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_CLUB_USER) {

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

            $total_records = TrainingProgram::where('user_id', $user_id)->count();
            $total_no_of_pages = ceil($total_records / $limit);

            $count = 0;
            $training_programs_array = array();
            $training_programs = TrainingProgram::select('id', 'title', 'status', 'pdf_file')
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'desc')
                    ->offset($offset)
                    ->limit($limit)
                    ->get();
            foreach ($training_programs as $program) {
                $count++;
                $training_programs_array[] = $this->get_program_array($program, 'short');
            }

            $data = [
                'page_no'           => $page_no,
                'limit'             => $limit,
                'total_records'     => $total_records,
                'current_count'     => $count,
                'total_no_of_pages' => $total_no_of_pages,
                'training_programs' => $training_programs_array,
            ];

            return $this->sendResponse($data, 'Successfully Retrieved Training Programs');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_training_program_view(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->program_id) && ltrim(rtrim($request->program_id)) != '') {
            $program_id = $request->program_id;
            $programs = TrainingProgram::find($program_id);
            if (empty($programs)) {
                return $this->sendError("Training Program not found");
            }
            $program_array = $this->get_program_array($programs, 'full');
            $data = [
                'training_program_details' => $program_array,
            ];
            return $this->sendResponse($data, 'Successfully Retrieved Training Program Details');
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function club_training_program_delete(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_CLUB_USER) {

            if (isset($request->program_id) && ltrim(rtrim($request->program_id)) != '') {

                $id = $request->program_id;

                TrainingProgramDetail::where('program_id', $id)->delete();
                TrainingProgram::where('id', $id)->delete();
                return $this->sendSuccess("Successfully Deleted Training Program");
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
