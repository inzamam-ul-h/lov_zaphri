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
use App\Models\SessionType;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;

class FeedbackController extends MainController {

    private $views_path = "feedbacks";
    private $home_route = "feedbacks.index";
    private $create_route = "feedbacks.create";
    private $edit_route = "feedbacks.edit";
    private $view_route = "feedbacks.show";
    private $delete_route = "feedbacks.destroy";
    private $active_route = "feedbacks.activate";
    private $inactive_route = "feedbacks.deactivate";
    private $msg_created = "Feedback added successfully.";
    private $msg_updated = "Feedback updated successfully.";
    private $msg_deleted = "Feedback deleted successfully.";
    private $msg_not_found = "Feedback not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Feedback name";
    private $list_permission = "feedbacks-listing";
    private $add_permission = "feedbacks-add";
    private $edit_permission = "feedbacks-edit";
    private $view_permission = "feedbacks-view";
    private $status_permission = "feedbacks-status";
    private $delete_permission = "feedbacks-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of feedback. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add feedback. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update feedback. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View product details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of feedback. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete feedback. Please Contact Administrator.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 0;
            $records = Feedback::select(['id'])->where('id', '>=', 1)->limit(1)->get();
            foreach ($records as $record) {
                $records_exists = 1;
            }

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
            $Records = Feedback::leftJoin('states', 'cities.state_id', '=', 'states.id')
                    ->select(['cities.id', 'cities.name', 'cities.status', 'cities.is_featured', 'states.name as pr_name']);

            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('name') && !empty($request->name)) {
                            $query->where('cities.name', 'like', "%{$request->get('name')}%");
                        }

                        if ($request->has('is_featured') && $request->get('is_featured') != -1) {
                            $query->where('cities.is_featured', '=', "{$request->get('is_featured')}");
                        }

                        if ($request->has('status') && $request->get('status') != -1) {
                            $query->where('cities.status', '=', "{$request->get('status')}");
                        }


                        if ($request->has('pr_name') && !empty($request->pr_name)) {
                            $query->where('states.name', 'like', "%{$request->get('pr_name')}%");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('is_featured', function ($Records) {
                        $str = dispaly_status_in_table($Records->is_featured);

                        return $str;
                    })
                    ->addColumn('status', function ($Records) {
                        $str = dispaly_status_in_table($Records->status);

                        return $str;
                    })
                    ->addColumn('action', function ($Records) {
                        $record_id = $Records->id;
                        $Auth_User = Auth::user();
                        $status = $Records->status;

                        $str = '<button data-bs-toggle="dropdown" class="btn btn-info btn-sm">
							<i class="fa fa-ellipsis-v"></i>
						</button>
						<div class="dropdown-menu">';

                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $record_id);
                        }

                        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
                            $str .= edit_link_in_table($this->edit_route, $record_id);
                        }

                        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {


                            if ($status == 1) {
                                $str .= inactive_link_in_table($this->inactive_route, $record_id);
                            }
                            else {
                                $str .= active_link_in_table($this->active_route, $record_id);
                            }
                        }

                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_link_in_table($record_id);
                          } */


                        $str .= '</div>';
                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->name);
                          } */
                        return $str;
                    })
                    ->rawColumns(['sr_no', 'is_featured', 'status', 'action'])
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
                'state_id' => 'required',
                'name'     => 'required'
            ]);

            $Model_Data = new Feedback();

            $Model_Data->state_id = $request->state_id;

            $Model_Data->name = $request->name;

            $Model_Data->created_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_created);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
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
            $Model_Data = Feedback::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }


            return view($this->views_path . '.show', compact("Model_Data"));
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
            $Model_Data = Feedback::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            return view($this->views_path . '.edit', compact("Model_Data"));
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
            $Model_Data = Feedback::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $request->validate([
                'state_id' => 'required',
                'name'     => 'required'
            ]);

            $Model_Data->state_id = $request->state_id;

            $Model_Data->name = $request->name;

            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

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
            $Model_Data = Feedback::find($id);

            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
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
            $Model_Data = Feedback::find($id);

            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
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

}
