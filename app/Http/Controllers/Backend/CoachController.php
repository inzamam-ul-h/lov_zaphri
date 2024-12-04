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
use App\Models\UserPersonal;
use App\Models\UserCalendar;
use App\Models\UserProfessional;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\SessionType;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;

class CoachController extends MainController {

    private $views_path = "backend.coach";
    private $home_route = "availability";
    private $msg_created = "Session created successfully.";
    private $msg_updated = "Session updated successfully.";
    private $msg_deleted = "Session deleted successfully.";
    private $msg_not_found = "Session not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same name";
    private $msg_active = "Session made Active successfully.";
    private $msg_inactive = "Session made InActive successfully.";
    private $msg_approved = "Session has been approved successfully.";
    private $msg_rejected = "Session has been rejected successfully.";
    private $msg_removed = "Session has been removed successfully.";
    private $view_permission = "club-members-view";
    private $edit_permission = "club-members-edit";
    private $status_permission = "club-members-status";
    private $add_permission_error_message = "Error: You are not authorized to create Sessions. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to edit Sessions. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Sessions. Please Contact Administrator.";

    public function availability() {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_COACH_USER || $Auth_User->can('all')) {
            $Model_Data = User::find($Auth_User->id);
            $UserCalendar = UserCalendar::where('user_id', $Auth_User->id)->first();
            $UserPersonal = UserPersonal::where('user_id', $Auth_User->id)->first();
            $UserProfessional = UserProfessional::where('user_id', $Auth_User->id)->first();

            return view($this->views_path . '.availability', compact("Model_Data", "UserCalendar", "UserPersonal", "UserProfessional"));
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function delete_availability($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_COACH_USER) {
            $slot_id = $id;
            $res = Session::where('id', $slot_id)->where('user_id', $Auth_User->id)->orderby('id', 'desc')->first();
            if (empty($res)) {
                Flash::error('Session not found');
                return redirect()->route($this->home_route);
            }
            $session = Session::find($slot_id);
            $session->delete();

            Flash::success('Session successfully deleted');
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

}
