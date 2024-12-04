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
use App\Models\Video;
use App\Models\Category;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanDetail;

class TrainingPlanController extends MainController {

    private $views_path = "backend.training_plans";
    private $home_route = "training-plans.index";
    private $create_route = "training-plans.create";
    private $edit_route = "training-plans.edit";
    private $view_route = "training-plans.show";
    private $delete_route = "training-plans.destroy";
    private $active_route = "training-plans.activate";
    private $inactive_route = "training-plans.deactivate";
    private $msg_created = "Training plan added successfully.";
    private $msg_updated = "Training plan updated successfully.";
    private $msg_deleted = "Training plan deleted successfully.";
    private $msg_not_found = "Training plan not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Training plan name";
    private $list_permission = "training-plans-listing";
    private $add_permission = "training-plans-add";
    private $edit_permission = "training-plans-edit";
    private $view_permission = "training-plans-view";
    private $status_permission = "training-plans-status";
    private $delete_permission = "training-plans-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Traing Plans. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Traing Plan. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Traing Plan. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Traing Plan details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Traing Plan. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Traing Plan. Please Contact Administrator.";

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
            $Records = TrainingPlan::leftJoin('users', 'training_plans.user_id', '=', 'users.id')
                    ->select(['training_plans.id', 'training_plans.plan_name as title', 'training_plans.status', 'training_plans.created_at', 'training_plans.user_id', 'users.name as user_name', 'users.user_type'])
                    ->orderBy('training_plans.id', 'DESC');
            switch ($Auth_User->user_type) {
                case $this->_CLUB_USER: {
                        $club = $Auth_User;
                        $coaches_ids = $this->get_club_coach_ids($club);
                        $Records = $Records->whereIn('training_plans.user_id', $coaches_ids);
                    }
                    break;

                case $this->_COACH_USER: {
                        $coach_id = $Auth_User->id;

                        $Records = $Records->where('training_plans.user_id', $coach_id);
                    }
                    break;

                case $this->_PLAYER_USER: {
                        $coach_id = 0;
                        $Records = $Records->where('training_plans.user_id', '=', $coach_id);
                    }
                    break;

                case $this->_PARENT_USER: {
                        $coach_id = 0;
                        $Records = $Records->where('training_plans.user_id', '=', $coach_id);
                    }
                    break;
                default: {
                        //
                    }
                    break;
            }

            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('title') && !empty($request->title)) {
                            $query->where('training_plans.plan_name', 'like', "%{$request->get('title')}%");
                        }

                        if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                            $query->where('training_plans.status', '=', "{$request->get('status')}");
                        }

                        if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                            $query->whereDate('training_plans.created_at', '=', "{$request->get('created_at')}");
                        }

                        if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                            $query->whereDate('training_plans.updated_at', '=', "{$request->get('updated_at')}");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('title', function ($Records) {
                        $record_id = $Records->id;
                        $title = $Records->title;

                        $str = '<a class="text-primary" href="' . route($this->view_route, $record_id) . '" title="View Details">' . $title . '</a>';

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
                    ->rawColumns(['sr_no', 'title', 'user_type', 'status', 'created_at', 'action'])
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
        if ($Auth_User->can($this->add_permission) || $Auth_User->user_type == $this->_COACH_USER || $Auth_User->can('all')) {

            $club_id = get_club_id($Auth_User->id);

            $videos = Video::join('categories', 'categories.id', '=', 'videos.category')
                    ->join('users', 'users.id', '=', 'videos.created_by')
                    ->where([['videos.user_id', $club_id], ['videos.status', '1']])
                    ->orWhere([['videos.user_id', $Auth_User->id], ['videos.status', '1']])
                    ->select('videos.*', 'users.name as user_name', 'categories.name as category_name')
                    ->get();

            return view($this->views_path . '.create', compact("videos"));
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
                'plan_name' => 'required',
                'video_ids' => 'required'
            ]);

            $user_id = $Auth_User->id;
            $pdf_name_store = 'default_image';

            $Model_Data = new TrainingPlan();
            $Model_Data->user_id = $user_id;
            $Model_Data->plan_name = $request->plan_name;
            $Model_Data->status = 1;
            $Model_Data->pdf_file = $pdf_name_store;
            $Model_Data->created_by = $Auth_User->id;
            $Model_Data->save();

            $plan_id = $Model_Data->id;
            if (isset($request->video_ids)) {
                $video_ids = $request->video_ids;
                foreach ($video_ids as $video_id) {
                    $planDetails = new TrainingPlanDetail();
                    $planDetails->plan_id = $plan_id;
                    $planDetails->video_id = $video_id;
                    $planDetails->status = 1;
                    $planDetails->created_by = $Auth_User->id;
                    $planDetails->save();
                }
            }

            $this->create_training_plan_pdf($plan_id);

            Flash::success($this->msg_created);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
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
                        ->where('training_plan_details.status', 1)
                        ->select('videos.*')->get();
        $data = ['plan' => $plan, 'planDetails' => $planDetails, 'uploadsPath' => $SITE_URL . "/" . $uploadsPath, 'video_uploadsPath' => $video_uploadsPath];

        $code = rand(1000, 9999);
        $fileName = $code . '-' . time() . '.pdf';
        $plan->pdf_file = $fileName;
        $plan->save();
        $pdf = PDF::loadView('backend.pdf_training_plan', $data)->save($uploadsPath . '/' . $fileName);
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
            $Model_Data = TrainingPlan::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $plan_id = $id;
            $planDetails = TrainingPlanDetail::join('videos', 'videos.id', '=', 'training_plan_details.video_id')
                    ->join('categories', 'categories.id', '=', 'videos.category')
                    ->join('users', 'users.id', '=', 'training_plan_details.created_by')
                    ->select('videos.*', 'users.name as user_name', 'categories.name as category_name')
                    ->where('training_plan_details.plan_id', $plan_id)
                    ->get();

            return view($this->views_path . '.show', compact("Model_Data", "planDetails"));
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
    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = TrainingPlan::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $club_id = get_club_id($Auth_User->id);
            if ($Auth_User->user_type == $this->_COACH_USER) {
                $videos = Video::where([['videos.user_id', $club_id], ['videos.status', '1']])
                                ->orwhere([['user_id', $Auth_User->id], ['status', '1']])->get();
            }
            $plan_id = $id;
            $planDetails = TrainingPlanDetail::join('videos', 'videos.id', '=', 'training_plan_details.video_id')
                    ->select('training_plan_details.video_id')
                    ->where('training_plan_details.plan_id', $plan_id)
                    ->get();

            return view($this->views_path . '.edit', compact("Model_Data", "planDetails", "videos"));
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
            $Model_Data = TrainingPlan::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $request->validate([
                'plan_name' => 'required',
                'video_ids' => 'required',
            ]);
            $plan_id = $id;
            $Model_Data->plan_name = $request->plan_name;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();
            $planDetails = TrainingPlanDetail::select('id')->where('plan_id', $plan_id)->where('status', 1)->get();
            foreach ($planDetails as $planDetail) {
                $planDetail = TrainingPlanDetail::find($planDetail->id);
                $planDetail->status = 2;
                $planDetail->updated_by = $Auth_User->id;
                $planDetail->save();
            }
            if (isset($request->video_ids)) {
                $video_ids = $request->video_ids;

                foreach ($video_ids as $video_id) {
                    $planDetail = TrainingPlanDetail::select('id')->where('plan_id', $plan_id)->where('video_id', $video_id)->where('status', 2)->first();
                    if (!empty($planDetail)) {
                        $planDetail = TrainingPlanDetail::find($planDetail->id);
                        $planDetail->status = 1;
                        $planDetail->updated_by = $Auth_User->id;
                        $planDetail->save();
                    }
                    else {
                        $planDetail = new TrainingPlanDetail();
                        $planDetail->plan_id = $plan_id;
                        $planDetail->video_id = $video_id;
                        $planDetail->status = 1;
                        $planDetail->created_by = $Auth_User->id;
                        $planDetail->save();
                    }
                }
            }

            $planDetails = TrainingPlanDetail::select('id')->where('plan_id', $plan_id)->where('status', 2)->get();
            foreach ($planDetails as $planDetail) {
                $planDetail = TrainingPlanDetail::find($planDetail->id);
                $planDetail->status = 0;
                $planDetail->updated_by = $Auth_User->id;
                $planDetail->save();
            }

            $this->create_training_plan_pdf($plan_id);

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
            $Model_Data = TrainingPlan::find($id);
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
            $Model_Data = TrainingPlan::find($id);
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
            else if ($is_edit == FALSE && $user_type == $this->_CLUB_USER) {
                $user_club_id = get_club_id($Model_Data->user_id);
                if ($user_club_id == $Auth_User->id) {
                    $bool = 0;
                }
            }
        }

        return $bool;
    }

}
