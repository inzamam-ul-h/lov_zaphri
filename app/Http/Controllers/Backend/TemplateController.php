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
use App\Models\Template;

class TemplateController extends MainController {

    private $views_path = "templates";
    private $home_route = "templates.index";
    private $create_route = "templates.create";
    private $edit_route = "templates.edit";
    private $view_route = "templates.show";
    private $delete_route = "templates.destroy";
    private $active_route = "templates.activate";
    private $inactive_route = "templates.deactivate";
    private $msg_created = "Template created successfully.";
    private $msg_updated = "Template updated successfully.";
    private $msg_deleted = "Template deleted successfully.";
    private $msg_not_found = "Template not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same name";
    private $msg_active = "Template made Active successfully.";
    private $msg_inactive = "Template made InActive successfully.";
    private $list_permission = "templates-listing";
    private $add_permission = "templates-add";
    private $edit_permission = "templates-edit";
    private $view_permission = "templates-view";
    private $status_permission = "templates-status";
    private $delete_permission = "templates-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Templates. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Template. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Template. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Template details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Template. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Template. Please Contact Administrator.";

    /**
     * Display a listing of the Model.
     *
     * 
     * @return Response
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 0;
            $records = Template::select(['id'])->where('id', '>=', 1)->limit(1)->get();
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
            $Records = Template::select(['templates.id', 'templates.type', 'templates.type_for', 'templates.title', 'templates.subject', 'templates.created_at', 'templates.updated_at']);

            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('type') && !empty($request->type)) {
                            $query->where('templates.type', 'like', "%{$request->get('type')}%");
                        }

                        if ($request->has('type_for') && !empty($request->type_for)) {
                            $query->where('templates.type_for', 'like', "%{$request->get('type_for')}%");
                        }

                        if ($request->has('title') && !empty($request->title)) {
                            $query->where('templates.title', 'like', "%{$request->get('title')}%");
                        }

                        if ($request->has('subject') && !empty($request->subject)) {
                            $query->where('templates.subject', 'like', "%{$request->get('subject')}%");
                        }

                        if ($request->has('created_at') && !empty($request->created_at)) {
                            $query->where('templates.created_at', 'like', "%{$request->get('created_at')}%");
                        }

                        if ($request->has('updated_at') && !empty($request->updated_at)) {
                            $query->where('templates.updated_at', 'like', "%{$request->get('updated_at')}%");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('title', function ($Records) {
                        $record_id = $Records->id;

                        $str = '<a href="' . route($this->view_route, $record_id) . '" title="View Details">' . $Records->title . '</a>';

                        return $str;
                    })
                    ->addColumn('created_at', function ($Records) {
                        $str = dispaly_date_in_table($Records->created_at);
                        return $str;
                    })
                    ->addColumn('updated_at', function ($Records) {
                        $str = dispaly_date_in_table($Records->updated_at);
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

                        /* if($Auth_User->can($this->status_permission) || $Auth_User->can('all'))
                          {
                          if($status == 1)
                          {
                          $str.= inactive_link_in_table($this->inactive_route,$record_id);
                          }
                          else
                          {
                          $str.= active_link_in_table($this->active_route,$record_id);
                          }
                          } */

                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_link_in_table($record_id);
                          } */

                        $str .= '</div>';
                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->name_en);
                          } */
                        return $str;
                    })
                    ->rawColumns(['sr_no', 'title', 'created_at', 'updated_at', 'action'])
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
                'title'   => 'required',
                'subject' => 'required',
                    //,'description' => 'required'
            ]);

            $Model_Data = new Template();

            if (isset($request->type))
                $Model_Data->type = $request->type;

            if (isset($request->type_for))
                $Model_Data->type_for = $request->type_for;

            $Model_Data->title = $request->title;

            $Model_Data->subject = $request->subject;

            if ($request->type == 'sms') {
                $Model_Data->description = $request->description_sms;
            }

            if ($request->type == 'email') {
                $Model_Data->description = $request->description;
            }




            $Model_Data->created_by = Auth::user()->id;

            $Model_Data->save();

            if (Auth::user() && Auth::user()->user_type == 'employer') {
                $log_array = array();
                $log_array['title'] = 'Templates Added';
                $log_array['description'] = 'Added Templates Details of ' . $request->title . '';
                employers_logs($Auth_User, $log_array);
            }

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
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = Template::find($id);

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
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Template::find($id);

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
     * @param  \App\Models\Model  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {

            $Model_Data = Template::find($id);

            if (empty($Model_Data)) {

                Flash::error($this->msg_not_found);

                return redirect(route($this->home_route));
            }

            $request->validate([
                'subject'     => 'required',
                'description' => 'required'
            ]);

            if (isset($request->type))
                $Model_Data->type = $request->type;

            if (isset($request->type_for))
                $Model_Data->type_for = $request->type_for;

            if (isset($request->title))
                $Model_Data->title = $request->title;

            $Model_Data->subject = $request->subject;

            $Model_Data->description = $request->description;

            $Model_Data->updated_by = Auth::user()->id;

            $Model_Data->save();

            if (Auth::user() && Auth::user()->user_type == 'employer') {
                $log_array = array();
                $log_array['title'] = 'Templates Updated';
                $log_array['description'] = 'Updated Templates Details of ' . $request->title . '';
                employers_logs($Auth_User, $log_array);
            }

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
            $Model_Data = Template::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_active);
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
            $Model_Data = Template::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_inactive);
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
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        return redirect(route($this->home_route));

        /* $Auth_User = Auth::user();
          if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
          {
          $Model_Data = Template::find($id);

          if (empty($Model_Data))
          {
          Flash::error($this->msg_not_found);
          return redirect(route($this->home_route));
          }

          $Model_Data->delete();

          Flash::success($this->msg_deleted);
          return redirect(route($this->home_route));
          }
          else
          {
          Flash::error($this->delete_permission_error_message);
          return redirect()->route($this->home_route);
          } */
    }

}
