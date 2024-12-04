<?php

namespace App\Http\Controllers\Backend;

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
use App\Models\ParentRequest;
use App\Models\UserProfessional;

class ParentController extends MainController {

    private $invite_route = "parent.invite";
    private $remove_invite_route = "parent.remove";
    private $approve_route = "player.approve";
    private $reject_route = "player.deactivate";
    private $parent_route = "parents.history_index";
    private $views_path = "backend.parent";
    private $home_route = "dashboard";
    private $msg_approved = "Association Request has been Accepted successfully.";
    private $msg_not_found = "User not found. Please try again.";
    private $msg_invite = "Association Request has been Sent successfully.";
    private $msg_rejected = "Association Request has been removed successfully.";
    private $msg_removed = "Member has been removed successfully.";
    private $view_permission = "parent-members-view";
    private $edit_permission = "parent-members-edit";
    private $status_permission = "parent-members-status";
    private $view_permission_error_message = "Error: You are not authorized to View Parent Members details. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to remove Parent Members. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Association Request. Please Contact Administrator.";

    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PARENT_USER || $Auth_User->can('all')) {
            $records_exists = 1;

            return view($this->views_path . '.listing', compact("records_exists"));
        }
        else {

            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function search_datatable(Request $request) {
        $Auth_User = Auth::user();
        $ids = [0];
        $id = [$Auth_User->id];
        if (($request->has('email') && !empty($request->email))) {
            $Records = User::whereIn('parent_id', $ids)->whereNotIn('parent_id', $id)->where('user_type', 2);
        }
        else {
            $Records = User::where('id', 0);
        }

        $response = Datatables::of($Records)
                ->filter(function ($query) use ($request) {

                    if ($request->has('email') && !empty($request->email)) {
                        $query->where('email', 'like', "%{$request->get('email')}%");
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
                ->addColumn('action', function ($Records) {
                    $Auth_User = Auth::user();
                    $record_id = $Records->id;
                    $email = $Records->email;
                    $user_model = User::where('email', $email)->first();
                    $req_user_id = $user_model->id;
                    $request_model = ParentRequest::where('parent_id', $Auth_User->id)->where('user_id', $req_user_id)->where('status', 0)->first();

                    $status = $Records->status;

                    $str = '<div>';
                    if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                        $str .= view_link_in_table($this->view_route, $record_id) . '  &nbsp;';
                    }

                    if ($request_model != null) {
                        $str .= remove_invite_link_in_table($this->remove_invite_route, $record_id) . '  ';
                    }
                    else {
                        $str .= invite_link_in_table($this->invite_route, $record_id) . '  ';
                    }

                    $str .= '</div>';

                    return $str;
                })
                ->rawColumns(['sr_no', 'user_type', 'status', 'action'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function history_index() {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PARENT_USER || $Auth_User->user_type == $this->_PLAYER_USER || $Auth_User->can('all')) {
            $records_exists = 1;

            return view($this->views_path . '.history_listing', compact("records_exists"));
        }
        else {

            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function history_datatables(Request $request) {
        $Auth_User = Auth::user();

        if ($Auth_User->user_type == $this->_PARENT_USER || $Auth_User->can('all')) {
            $status_array = [0, 1, 2, 3];
            $Records = ParentRequest::where('parent_id', $Auth_User->id)->whereIn('status', $status_array);
        }
        if ($Auth_User->user_type == $this->_PLAYER_USER || $Auth_User->can('all')) {
            $status_array = [0, 1, 2];
            $Records = ParentRequest::where('user_id', $Auth_User->id)->whereIn('status', $status_array);
        }

        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('name', function ($Records) {
                    $Auth_User = Auth::user();
                    if ($Auth_User->user_type == $this->_PLAYER_USER) {
                        $str = get_user_name($Records->parent_id);
                    }
                    if ($Auth_User->user_type == $this->_PARENT_USER) {
                        $str = get_user_name($Records->user_id);
                    }

                    return $str;
                })
                ->addColumn('email', function ($Records) {
                    $Auth_User = Auth::user();
                    if ($Auth_User->user_type == $this->_PLAYER_USER) {
                        $str = get_user_email($Records->parent_id);
                    }
                    if ($Auth_User->user_type == $this->_PARENT_USER) {
                        $str = get_user_email($Records->user_id);
                    }
                    return $str;
                })
                ->addColumn('status', function ($Records) {
                    $str = parent_invite_status($Records->status);

                    return $str;
                })
                ->addColumn('action', function ($Records) {
                    $Auth_User = Auth::user();
                    $status = $Records->status;
                    $record_id = $Records->id;
                    $str = '<div>';
                    if ($Auth_User->user_type == $this->_PARENT_USER) {

                        $user_id = $Records->user_id;
                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $user_id) . '  &nbsp;';
                        }

                        if (($Records != null ) && (($Records->status == 0 ))) {
                            $str .= remove_invite_link_in_table($this->remove_invite_route, $record_id) . '  ';
                        }
                    }
                    if ($Auth_User->user_type == $this->_PLAYER_USER) {

                        $parent_id = $Records->parent_id;

                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $parent_id) . '  &nbsp;';
                        }

                        if (($Records != null ) && ($Records->status == 0 )) {
                            $str .= reject_link_in_table($this->reject_route, $record_id) . '  ';

                            $str .= approve_link_in_table($this->approve_route, $record_id) . '  ';
                        }
                    }
                    $str .= '</div>';
                    return $str;
                })
                ->rawColumns(['sr_no', 'name', 'email', 'status', 'action'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function member_player() {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PARENT_USER || $Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $User_ids = $this->get_parent_user_ids($Auth_User);
            $Model_Data = User::select('*')->whereIn('id', $User_ids)->where('user_type', $this->_PLAYER_USER)->get();
            if (empty($Model_Data)) {
                Flash::error('Player Profile not found');
                return redirect()->route($this->home_route);
            }
            return view($this->views_path . '.player', compact("Model_Data"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function member_parent() {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PLAYER_USER || $Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $parent_id = User::select('*')->where('id', $Auth_User->id)->where('parent_id', '>', 0)->first();
            if (empty($parent_id)) {
                Flash::error('Parent profile  not found');
                return redirect()->route($this->home_route);
            }
            $Model_Data = User::select('*')->where('id', $parent_id->parent_id)->get();

            if (empty($Model_Data)) {
                Flash::error('User not found');
                return redirect()->route($this->home_route);
            }

            return view($this->views_path . '.player', compact("Model_Data"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function invite($user_id) {
        $Auth_User = Auth::user();

        if ($Auth_User->user_type == $this->_PARENT_USER || $Auth_User->can('all')) {
            $id = $user_id;

            $Model_Data = User::find($id);

            if (empty($Model_Data)) {
                Flash::error('User not found');
                return redirect()->route($this->home_route);
            }
            $old_invite_data = ParentRequest::where('user_id', $id)->first();
            if (!empty($old_invite_data)) {
                $old_invite_data->status = 5;
                $old_invite_data->save();
            }
            $Model_Data = new ParentRequest();
            $Model_Data->user_id = $id;
            $Model_Data->parent_id = $Auth_User->id;
            $Model_Data->save();
            Flash::success($this->msg_invite);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function remove($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PARENT_USER || $Auth_User->can('all')) {
            $id = $user_id;
            $Model_Data = ParentRequest::where('id', $id)->where('parent_id', $Auth_User->id)->where('status', 0)->first();

            if (empty($Model_Data)) {

                Flash::error('Association Request not found');
                return redirect()->route($this->parent_route);
            }

            $Model_Data->status = 3;
            $Model_Data->save();

            Flash::success($this->msg_rejected);
            return redirect()->route($this->parent_route);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function approve($user_id) {

        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PLAYER_USER || $Auth_User->can('all')) {
            $id = $user_id;

            $Model_Data = ParentRequest::where('id', $id)->where('user_id', $Auth_User->id)->where('status', 0)->first();

            if (empty($Model_Data)) {
                Flash::error('Association Request not found');
                return redirect()->route($this->parent_route);
            }

            $Model_Data->status = 1;
            $Model_Data->save();

            $User = User::find($Auth_User->id);
            $User->parent_id = $Model_Data->parent_id;
            $User->save();

            Flash::success($this->msg_approved);
            return redirect()->route($this->parent_route);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function reject($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_PLAYER_USER || $Auth_User->can('all')) {
            $id = $user_id;

            $Model_Data = ParentRequest::where('id', $id)->where('user_id', $Auth_User->id)->where('status', 0)->first();

            if (empty($Model_Data)) {
                Flash::error('Association Request not found');
                return redirect()->route($this->parent_route);
            }

            $Model_Data->status = 2;
            $Model_Data->save();

            Flash::success($this->msg_rejected);
            return redirect()->route($this->parent_route);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function is_not_authorized($Model_Data, $Auth_User) {
        $bool = 1;
        if ($Model_Data->parent_id == $Auth_User->id) {
            $bool = 0;
        }
        else {
            if ($Auth_User->can('all')) {
                $bool = 0;
            }
        }

        return $bool;
    }

}
