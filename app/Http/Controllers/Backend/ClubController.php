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
use App\Models\Team;
use App\Models\TeamMember;

class ClubController extends MainController {

    private $views_path = "backend.club";
    private $home_route = "dashboard";
    private $msg_not_found = "User not found. Please try again.";
    private $msg_approved = "Association Request has been approved successfully.";
    private $msg_rejected = "Association Request has been rejected successfully.";
    private $msg_removed = "Member has been removed successfully.";
    private $view_permission = "club-members-view";
    private $edit_permission = "club-members-edit";
    private $status_permission = "club-members-status";
    private $view_permission_error_message = "Error: You are not authorized to View Club Members details. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to remove Club Members. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Association Request. Please Contact Administrator.";

    public function member_coaches() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $User_ids = $this->get_club_coach_ids($Auth_User);
            $Model_Data = User::select('*')->whereIn('id', $User_ids)->where('user_type', $this->_COACH_USER)->get();

            return view($this->views_path . '.coaches', compact("Model_Data"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function member_players() {
        $Auth_User = Auth::user();
        if (($Auth_User->can($this->view_permission) || $Auth_User->can('all'))) {
            $User_ids = $this->get_club_player_ids($Auth_User);

            $Model_Data = User::select('*')->whereIn('id', $User_ids)->where('user_type', $this->_PLAYER_USER)->get();
            return view($this->views_path . '.players', compact("Model_Data"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function approve($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_CLUB_USER || ($Auth_User->can($this->status_permission) || $Auth_User->can('all'))) {
            $id = $user_id;
            $Model_Data = UserProfessional::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error('Association Request not found');
                return redirect()->route($this->home_route);
            }
            $Model_Data->club_authentication = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_approved);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function reject($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_CLUB_USER || ($Auth_User->can($this->status_permission) || $Auth_User->can('all'))) {
            $id = $user_id;
            $Model_Data = UserProfessional::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error('Association Request not found');
                return redirect()->route($this->home_route);
            }
            $Model_Data->club_authentication = 2;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_rejected);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function remove($user_id) {
        $Auth_User = Auth::user();
        if (($Auth_User->user_type == $this->_CLUB_USER && $Auth_User->can($this->edit_permission)) || $Auth_User->can('all')) {
            $club_id = $Auth_User->id;
            $member_id = $user_id;
            $Model_Data = User::find($user_id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $Teams = Team::where('club_id', $club_id)->get();
            foreach ($Teams as $Team) {
                $TeamMember = TeamMember::where('team_id', $Team->id)->where('player_id', $member_id)->first();
                if (!empty($TeamMember)) {
                    $teamMember = TeamMember::find($TeamMember->id);
                    $teamMember->status = 0;
                    $teamMember->updated_by = $Auth_User->id;
                    $teamMember->save();
                }
            }

            $rows = UserProfessional::select('user_professionals.id')
                    ->join('users', 'user_professionals.user_id', '=', 'users.id')
                    ->where('club', $club_id)
                    ->where('user_professionals.user_id', $member_id)
                    ->where('club_authentication', 1);

            $rows = $rows->get();
            foreach ($rows as $row) {
                $rec_id = $row->id;
                $UserProfessional = UserProfessional::find($rec_id);
                $UserProfessional->club = 0;
                $UserProfessional->club_authentication = 0;
                $UserProfessional->updated_by = $club_id;
                $UserProfessional->save();
            }

            Flash::success($this->msg_removed);
            if ($Model_Data->user_type == 1) {
                return redirect()->route('clubs.member_coaches');
            }
            elseif ($Model_Data->user_type == 2) {
                return redirect()->route('clubs.member_players');
            }
            else {
                return redirect()->route($this->home_route);
            }
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function is_not_authorized($Model_Data, $Auth_User) {
        $bool = 1;
        if ($Model_Data->club == $Auth_User->id) {
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
