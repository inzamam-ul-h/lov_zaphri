<?php

namespace App\Http\Controllers\Backend;

use PDF;
use Auth;
use File;
use Flash;
use DateTime;
use Response;
use Attribute;
use Datatables;
use DateTimeZone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController as MainController;
use App\Models\User;
use App\Models\UserProfessional;
use App\Models\TrainingProgram;
use App\Models\TrainingProgramDetail;

class TrainingProgramController extends MainController {

    private $views_path = "backend.training_programs";
    private $home_route = "training-programs.index";
    private $create_route = "training-programs.create";
    private $edit_route = "training-programs.edit";
    private $view_route = "training-programs.show";
    private $delete_route = "training-programs.destroy";
    private $active_route = "training-programs.activate";
    private $inactive_route = "training-programs.deactivate";
    private $msg_created = "Training program added successfully.";
    private $msg_updated = "Training program updated successfully.";
    private $msg_deleted = "Training program deleted successfully.";
    private $msg_not_found = "Training program not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Training program name";
    private $list_permission = "training-programs-listing";
    private $add_permission = "training-programs-add";
    private $edit_permission = "training-programs-edit";
    private $view_permission = "training-programs-view";
    private $status_permission = "training-programs-status";
    private $delete_permission = "training-programs-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Training programs. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Training program. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Training program. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Training program. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Training program. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Training program. Please Contact Administrator.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 1;
            return view($this->views_path . '.listing', compact("records_exists"));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function datatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {

            $Records = TrainingProgram::leftJoin('users', 'training_programs.user_id', '=', 'users.id')
                            ->select(['training_programs.id', 'training_programs.title', 'training_programs.status', 'training_programs.created_at', 'training_programs.user_id', 'users.name as user_name', 'users.user_type'])->orderBy('training_programs.id', 'DESC');
            if ($Auth_User->user_type == $this->_CLUB_USER) {
                $Records = $Records->where('training_programs.user_id', '=', $Auth_User->id);
            }
            elseif ($Auth_User->user_type == $this->_COACH_USER) {
                $club = get_club_id($Auth_User->id);
                $Records = $Records->where('training_programs.user_id', '=', $club);
            }
            elseif ($Auth_User->user_type == $this->_ADMIN_USER) {
                $Records = $Records->where('training_programs.id', '>=', 1);
            }


            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('title') && !empty($request->title)) {
                            $query->where('training_programs.title', 'like', "%{$request->get('title')}%");
                        }

                        if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                            $query->where('training_programs.status', '=', "{$request->get('status')}");
                        }

                        if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                            $query->whereDate('training_programs.created_at', '=', "{$request->get('created_at')}");
                        }

                        if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                            $query->whereDate('training_programs.updated_at', '=', "{$request->get('updated_at')}");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('user_type', function ($Records) {
                        $user_type = $Records->user_type;

                        $str = "";
                        if ($user_type == 0) {
                            $str = 'Admin';
                        }
                        elseif ($user_type == 1) {
                            $str = 'Coach';
                        }
                        elseif ($user_type == 2) {
                            $str = 'Player';
                        }
                        elseif ($user_type == 3) {
                            $str = 'Club';
                        }
                        elseif ($user_type == 4) {
                            $str = 'Parent';
                        }
                        return $str;
                    })
                    ->addColumn('status', function ($Records) {
                        $str = dispaly_status_in_table($Records->status);

                        return $str;
                    })
                    ->addColumn('created_at', function ($Records) {
                        $str = dispaly_date_in_table($Records->created_at);
                        return $str;
                    })
                    ->addColumn('action', function ($Records) {
                        $record_id = $Records->id;
                        $Auth_User = Auth::user();
                        $status = $Records->status;

                        $str = '<div>';
                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $record_id) . '  &nbsp;';
                        }

                        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
                            $str .= edit_link_in_table($this->edit_route, $record_id) . '  &nbsp;';
                        }

                        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
                            if ($status == 1) {
                                $str .= inactive_link_in_table($this->inactive_route, $record_id) . '  &nbsp;';
                            }
                            else {
                                $str .= active_link_in_table($this->active_route, $record_id) . '  ';
                            }
                        }

                        $str .= '</div>';
                        return $str;
                    })
                    ->rawColumns(['sr_no', 'user_type', 'status', 'created_at', 'action'])
                    ->setRowId(function ($Records) {
                        return 'myDtRow' . $Records->id;
                    })
                    ->make(true);

            return $response;
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            return view($this->views_path . '.create');
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            $request->validate([
                'program_title'   => 'required',
                'variant_title'   => 'required',
                'description'     => 'required',
                'duration'        => 'required',
                'start_date_time' => 'required',
            ]);
            $variant_title = $request->variant_title;
            $length = count($variant_title);
            for ($i = 0; $i < $length; $i++) {
                $images = $request->images[$i];
                $documents = $request->documents[$i];
                $videos = $request->videos[$i];
                $request->validate([
                    'multi_images.*'    => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                    'multi_videos.*'    => ['required', 'mimes:mp4,ogv,webm', 'max:20480'],
                    'multi_documents.*' => ['required', 'doc,docx,pdf,jpeg,png,jpg,gif,svg', 'max:2048'],
                ]);
            }
            $Model_Data = new TrainingProgram();
            $Model_Data->user_id = $Auth_User->id;
            $Model_Data->title = $request->program_title;
            $Model_Data->status = 1;
            $Model_Data->created_by = $Auth_User->id;
            $Model_Data->save();

            $program_id = $Model_Data->id;
            $uploadsPath = $this->uploads_trainings . '/' . $program_id;

            $variant_title = $request->variant_title;
            $description = $request->description;
            $duration = $request->duration;
            $start_date_time = $request->start_date_time;
            $length = count($variant_title);
            for ($i = 0; $i < $length; $i++) {

                $images = "default_image";
                if (isset($request->images[$i]) && !empty($request->images[$i])) {
                    $file = $request->file('images')[$i];
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    $images = $fileName;
                }

                $documents = "default_image";
                $original_documents_name = "default_image";
                if (isset($request->documents[$i]) && !empty($request->documents[$i])) {
                    $file = $request->file('documents')[$i];
                    $original_documents_name = $file->getClientOriginalName();
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
                $programDetail->original_documents_name = $original_documents_name;
                $programDetail->videos = $videos;
                $programDetail->created_by = $Auth_User->id;
                $programDetail->save();
            }

            $this->create_training_program_pdf($program_id);

            Flash::success($this->msg_created);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
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
        $programDetails = TrainingProgramDetail::where('program_id', $program_id)->where('status', 1)->get();
        $data = ['program' => $program, 'programDetails' => $programDetails, 'uploadsPath' => $SITE_URL . "/" . $uploadsPath];

        $code = rand(1000, 9999);
        $fileName = $code . '-' . time() . '.pdf';
        $program->pdf_file = $fileName;
        $program->save();
        $pdf = PDF::loadView('backend.pdf_training_program', $data)->save($uploadsPath . '/' . $fileName);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = TrainingProgram::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $program_id = $id;
            $programDetails = TrainingProgramDetail::where('program_id', $program_id)->where('status', 1)->get();

            return view($this->views_path . '.show', compact("Model_Data", "programDetails"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function file_delete($id, $type, Request $request) {

        $Auth_User = Auth::user();

        if (($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {

            $file_name = $request->file_name;

            if (empty($file_name)) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }

            $Model_Data = TrainingProgramDetail::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);

                return redirect(route($this->home_route));
            }

            $uploadsPath = $this->uploads_trainings . '/' . $Model_Data->id;

            if ($type == 'video') {
                if ($file_name == $Model_Data->videos) {

                    $old_file_path = $uploadsPath . '/' . $Model_Data->videos;
                    File::delete($uploadsPath . "/" . $Model_Data->videos);
                    $Model_Data->videos = "default_image";
                }
            }
            elseif ($type == 'image') {

                if ($file_name == $Model_Data->images) {

                    $old_file_path = $uploadsPath . '/' . $Model_Data->images;
                    File::delete($uploadsPath . "/" . $Model_Data->images);
                    $Model_Data->images = "default_image";
                }
            }
            elseif ($type == 'documents') {

                if ($file_name == $Model_Data->documents) {

                    $old_file_path = $uploadsPath . '/' . $Model_Data->documents;
                    File::delete($uploadsPath . "/" . $Model_Data->documents);
                    $Model_Data->documents = "default_image";
                }
            }

            $Model_Data->updated_by = $Auth_User->id;

            $Model_Data->save();
            return response()->json(['status' => true, 'messages' => 'File Successfully Deleted.']);
        }
        else {
            return response()->json(['status' => false, 'messages' => $this->edit_permission_error_message]);
        }
    }

    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = TrainingProgram::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $program_id = $id;
            $programDetails = TrainingProgramDetail::where('program_id', $program_id)->where('status', 1)->get();

            return view($this->views_path . '.edit', compact("Model_Data", "programDetails"));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {

            $Model_Data = TrainingProgram::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $request->validate([
                'program_title'   => 'required',
                'variant_title'   => 'required',
                'description'     => 'required',
                'duration'        => 'required',
                'start_date_time' => 'required',
            ]);

            $variant_title = $request->variant_title;
            $length = count($variant_title);
            $j = 0;
            for ($i = 0; $i < $length; $i++) {

                if ($request->program_details_id[$i] != 0) {

                    $val_id = $request->program_details_id[$i];
                    $str = 'images_' . $val_id;
                    $images = (isset($request->{$str})) ? $request->{$str} : '';

                    $str = 'documents_' . $val_id;
                    $documents = (isset($request->{$str})) ? $request->{$str} : '';

                    $str = 'videos_' . $val_id;
                    $videos = (isset($request->{$str})) ? $request->{$str} : '';

                    $request->validate([
                        'multi_images.*'    => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
                        'multi_videos.*'    => ['mimes:mp4,ogv,webm', 'max:20480'],
                        'multi_documents.*' => ['doc,docx,pdf,jpeg,png,jpg,gif,svg', 'max:2048'],
                    ]);
                }
                else {

                    $y = $j;
                    $j++;
                    $images = $request->images[$y];
                    $documents = $request->documents[$y];
                    $videos = $request->videos[$y];
                    $request->validate([
                        'multi_images.*'    => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                        'multi_videos.*'    => ['required', 'mimes:mp4,ogv,webm', 'max:20480'],
                        'multi_documents.*' => ['required', 'doc,docx,pdf,jpeg,png,jpg,gif,svg', 'max:2048'],
                    ]);
                }
            }


            $Model_Data->title = $request->program_title;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            $program_id = $Model_Data->id;
            $uploadsPath = $this->uploads_trainings . '/' . $program_id;

            $variant_title = $request->variant_title;
            $description = $request->description;
            $duration = $request->duration;
            $start_date_time = $request->start_date_time;
            $program_details_id = $request->program_details_id;

            $programDetails = TrainingProgramDetail::select('id')->where('program_id', $program_id)->where('status', 1)->get();
            foreach ($programDetails as $programDetail) {

                $programDetail = TrainingProgramDetail::find($programDetail->id);
                $programDetail->status = 2;
                $programDetail->updated_by = $Auth_User->id;
                $programDetail->save();
            }

            $length = count($variant_title);
            $j = 0;
            for ($i = 0; $i < $length; $i++) {

                $details_id = $program_details_id[$i];
                if ($details_id >= 0) {

                    $programDetail = TrainingProgramDetail::select('id')->where('id', $details_id)->where('program_id', $program_id)->where('status', 2)->first();
                    if (!empty($programDetail)) {

                        $val_id = $programDetail->id;
                        $programDetail = TrainingProgramDetail::find($val_id);

                        $str = 'images_' . $val_id;
                        $images = $programDetail->images;
                        if (!empty($request->file($str))) {
                            $file = $request->file($str);
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $images = $fileName;
                        }

                        $str = 'documents_' . $val_id;
                        $documents = $programDetail->documents;
                        $original_documents_name = $programDetail->original_documents_name;
                        if (!empty($request->file($str))) {
                            $file = $request->file($str);
                            $original_documents_name = $file->getClientOriginalName();
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $documents = $fileName;
                        }

                        $str = 'videos_' . $val_id;
                        $videos = $programDetail->videos;
                        if (!empty($request->file($str))) {
                            $file = $request->file($str);
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $videos = $fileName;
                        }

                        $programDetail->title = $variant_title[$i];
                        $programDetail->description = $description[$i];
                        $programDetail->duration = $duration[$i];
                        $programDetail->start_date_time = $start_date_time[$i];
                        if ($images != $programDetail->images)
                            $programDetail->images = $images;
                        if ($documents != $programDetail->documents)
                            $programDetail->original_documents_name = $original_documents_name;
                        $programDetail->documents = $documents;
                        if ($videos != $programDetail->videos)
                            $programDetail->videos = $videos;
                        $programDetail->status = 1;
                        $programDetail->updated_by = $Auth_User->id;
                        $programDetail->save();
                    }
                    else {

                        $y = $j;
                        $j++;
                        $images = "default_image";
                        if (isset($request->images[$y]) && !empty($request->images[$y])) {
                            $file = $request->file('images')[$y];
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $images = $fileName;
                        }

                        $documents = "default_image";
                        $original_documents_name = "default_image";
                        if (isset($request->documents[$y]) && !empty($request->documents[$y])) {
                            $file = $request->file('documents')[$y];
                            $original_documents_name = $file->getClientOriginalName();
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $documents = $fileName;
                        }

                        $videos = "default_image";
                        if (isset($request->videos[$y]) && !empty($request->videos[$y])) {
                            $file = $request->file('videos')[$y];
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
                        $programDetail->original_documents_name = $original_documents_name;
                        $programDetail->documents = $documents;
                        $programDetail->videos = $videos;
                        $programDetail->status = 1;
                        $programDetail->created_by = $Auth_User->id;
                        $programDetail->save();
                    }
                }
            }

            $programDetails = TrainingProgramDetail::select('id')->where('program_id', $program_id)->where('status', 2)->get();
            foreach ($programDetails as $programDetail) {
                $programDetail = TrainingProgramDetail::find($programDetail->id);
                $programDetail->status = 0;
                $programDetail->updated_by = $Auth_User->id;
                $programDetail->save();
            }

            $this->create_training_program_pdf($program_id);

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * update status of the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function makeActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = TrainingProgram::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $Model_Data->status = 1;
            $Model_Data->save();

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * update status of the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function makeInActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = TrainingProgram::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $Model_Data->status = 0;
            $Model_Data->save();

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function is_not_authorized($Model_Data, $Auth_User, $is_edit = TRUE) {
        $user_type = $Auth_User->user_type;

        $bool = 1;
        if ($Model_Data->user_id == $Auth_User->id) {
            $bool = 0;
        }
        else {
            if ($Auth_User->can('all')) {
                $bool = 0;
            }
            else if ($is_edit == FALSE && $user_type == $this->_COACH_USER) {
                $user_club_id = get_club_id($Model_Data->user_id);
                if ($user_club_id == $Auth_User->id) {
                    $bool = 0;
                }
            }
        }

        return $bool;
    }

}
