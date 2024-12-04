<?php

namespace App\Http\Controllers\Api;

use File;
use DateTime;
use GuzzleHttp\Client;
use App\Models\AuthKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController as BaseController;
use App\Models\User;
use App\Models\UserProfessional;
use App\Models\Team;
use App\Models\TeamMember;

class ClubApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'club_association_requests_action',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'club_association': {
                        return $this->club_association($request, $User);
                    }
                    break;

                case 'club_members': {
                        return $this->club_members($request, $User);
                    }
                    break;

                case 'remove_members': {
                        return $this->remove_members($request, $User);
                    }
                    break;

                case 'club_association_requests': {
                        return $this->club_association_requests($request, $User);
                    }
                    break;

                case 'club_association_requests_action': {
                        return $this->club_association_requests_action($request, $User);
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

    private function club_association(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
            if (isset($request->club) && ltrim(rtrim($request->club)) != '') {
                $club = $request->club;

                $row = UserProfessional::where('user_id', $user_id)->orderby('id', 'desc')->first();
                if (!empty($row)) {
                    $prodessional_id = $row->id;
                    $club_retrieved = $row->club;
                    $club_authentication = $row->club_authentication;

                    if ($club == $club_retrieved and $club_authentication == "1") {
                        return $this->sendError("You are Already Associated with this club");
                    }
                    elseif ($club == $club_retrieved and $club_authentication == "0") {
                        return $this->sendError("Association request is already sent. Wait for approval from club");
                    }
                    else {
                        $UserProfessional = UserProfessional::find($prodessional_id);
                        $UserProfessional->club = $club;
                        $UserProfessional->club_authentication = 0;
                        $UserProfessional->updated_by = $user_id;
                        $UserProfessional->save();

                        return $this->sendSuccess("Association requested successfully. Wait for club approval.");
                    }
                }
                else {
                    return $this->sendError("Profile Not Found");
                }
            }
            else {
                return $this->sendError("Missing Parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_association_requests(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            $users = array();

            $rows = UserProfessional::where('club', $user_id)->where('club_authentication', 0)->get();
            foreach ($rows as $row) {
                $array = array();

                $array["user_id"] = $req_user_id = $row->user_id;

                $UserData = User::where('id', $req_user_id)->orderby('id', 'desc')->first();

                $user_type = $UserData->user_type;
                if ($user_type == $this->_COACH_USER) {
                    $array["user_type"] = "Coach";
                }
                elseif ($user_type == $this->_PLAYER_USER) {
                    $array["user_type"] = "Player";
                }
                elseif ($user_type == $this->_CLUB_USER) {
                    $array["user_type"] = "Club";
                }
                $array["email"] = $UserData->email;
                $array["phone_no"] = $UserData->phone_no;
                $array["user_name"] = get_user_name($row->user_id);

                $users[] = $array;
            }

            $response = [
                'requests' => $users
            ];
            return $this->sendResponse($response, 'Successfully Returned Data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_members(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {

            $users = array();

            $rows = UserProfessional::select('user_professionals.*', 'users.user_type', 'users.profile_status')
                    ->join('users', 'user_professionals.user_id', '=', 'users.id')
                    ->where('club', $user_id)
                    ->where('users.profile_status', 1)
                    ->where('club_authentication', 1);
            if ($request->user_type == 1) {
                $rows->where('users.user_type', 1);
            }
            elseif ($request->user_type == 2) {
                $rows->where('users.user_type', 2);
            }

            $players = array();
            if (isset($request->team_id) && $request->team_id != '') {
                $team_id = $request->team_id;
                $TeamMembers = TeamMember::where('team_id', $team_id)->where('status', 1)->get();
                foreach ($TeamMembers as $TeamMember) {
                    $player_id = $TeamMember->player_id;
                    $User = User::find($player_id);
                    if (!empty($User)) {
                        $players[] = $User->id;
                    }
                }
                $rows->whereNotIn('user_professionals.user_id', $players);
            }

            $rows = $rows->get();
            foreach ($rows as $row) {
                $array = array();

                $array["user_id"] = $req_user_id = $row->user_id;

                $UserData = User::where('id', $req_user_id)->orderby('id', 'desc')->first();

                $users[] = $this->get_user_array($UserData, FALSE);
            }

            $response = [
                'requests' => $users
            ];
            return $this->sendResponse($response, 'Successfully Returned Data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function remove_members(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            if (isset($request->user_id) && $request->user_id != '') {
                $club_id = $user_id;
                $member_id = $request->user_id;
                $Teams = Team::where('club_id', $club_id)->get();
                foreach ($Teams as $Team) {
                    $TeamMember = TeamMember::where('team_id', $team_id)->where('player_id', $member_id)->first();
                    if (!empty($TeamMember)) {
                        $rec_id = $TeamMember->id;
                        $TeamMember = TeamMember::find($rec_id);
                        $TeamMember->delete();
                    }
                }

                $rows = UserProfessional::select('user_professionals.id')
                        ->join('users', 'user_professionals.user_id', '=', 'users.id')
                        ->where('club', $club_id)
                        ->where('user_professionals.user_id', $member_id)
                        ->where('users.profile_status', 1)
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
                return $this->sendSuccess("Successfully Removed Member");
            }
            else {
                return $this->sendError("Missing Paramemters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function club_association_requests_action(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            if (isset($request->user_id) && ltrim(rtrim($request->user_id)) != '' && isset($request->approval) && ltrim(rtrim($request->approval)) != '') {
                $req_user_id = $request->user_id;
                $approval = $request->approval;

                $row = UserProfessional::where('user_id', $req_user_id)->where('club', $user_id)->orderby('id', 'desc')->first();
                if (!empty($row)) {
                    $prodessional_id = $row->id;

                    if ($approval == 1) {
                        $UserProfessional = UserProfessional::find($prodessional_id);
                        $UserProfessional->club_authentication = 1;
                        $UserProfessional->save();

                        return $this->sendSuccess("Successfully Approved Association Request");
                    }
                    elseif ($approval == 0) {
                        $UserProfessional = UserProfessional::find($prodessional_id);
                        $UserProfessional->club_authentication = 2;
                        $UserProfessional->save();

                        return $this->sendSuccess("Successfully Rejected Association Request");
                    }
                    else {
                        return $this->sendError("Incorrect Approval Value");
                    }
                }
                else {
                    return $this->sendError("Profile Not Found");
                }
            }
            else {
                return $this->sendError("Missing Parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

}
