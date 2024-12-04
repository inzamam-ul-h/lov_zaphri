<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use App\Models\UserProfessional;
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
use App\Models\Team;
use App\Models\TeamMember;

class TeamApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            if ($profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'teams_listing': {
                        return $this->teams_listing($request, $User);
                    }
                    break;

                case 'teams_details': {
                        return $this->teams_details($request, $User);
                    }
                    break;

                case 'create_team': {
                        return $this->create_team($request, $User);
                    }
                    break;

                case 'edit_team': {
                        return $this->edit_team($request, $User);
                    }
                    break;

                case 'update_team_logo': {
                        return $this->update_team_logo($request, $User);
                    }
                    break;

                case 'update_team_coach': {
                        return $this->update_team_coach($request, $User);
                    }
                    break;

                case 'update_team_ast_coach': {
                        return $this->update_team_ast_coach($request, $User);
                    }
                    break;

                case 'add_team_member': {
                        return $this->add_team_member($request, $User);
                    }
                    break;

                case 'remove_team_member': {
                        return $this->remove_team_member($request, $User);
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

    protected function get_team_array($Team) {
        $data = NULL;
        if (!empty($Team)) {
            $SITE_URL = env('APP_URL');
            $team_id = $Team->id;
            $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/team.png";
            $uploadsPath = $SITE_URL . "/" . $this->uploads_teams . '/' . $team_id;
            $coach_id = $Team->coach_id;
            $ast_coach_id = $Team->ast_coach_id;

            $logo = $defaultImage;
            if (!empty($Team->logo) && $Team->logo != 'default_image')
                $logo = $uploadsPath . "/" . $Team->logo;

            $data = [
                'team_id'     => $Team->id,
                'name'        => $Team->name,
                'age_group'   => $Team->age_group,
                'color'       => $Team->color,
                'description' => $Team->description,
                'logo'        => $logo,
                'status'      => $Team->status
            ];

            $coach = array();
            $User = User::find($coach_id);
            if (!empty($User)) {
                $coach = $this->get_user_array($User);
            }
            $data['coach'] = $coach;

            $assistant_coach = NULL;
            $User = User::find($ast_coach_id);
            if (!empty($User)) {
                $assistant_coach = $this->get_user_array($User);
            }
            $data['assistant_coach'] = $assistant_coach;

            $players = array();
            $TeamMembers = TeamMember::where('team_id', $team_id)->where('status', 1)->get();
            foreach ($TeamMembers as $TeamMember) {
                $player_id = $TeamMember->player_id;
                $User = User::find($player_id);
                if (!empty($User)) {
                    $players[] = $this->get_user_array($User);
                }
            }
            $data['players'] = $players;
        }
        return $data;
    }

    private function teams_listing(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $bool = 0;

        $club_id = $user_id;
        $log_user_type = $user_type;

        if (($log_user_type == 0 || $log_user_type == 3)) {
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

            $total_records = Team::where('club_id', $club_id)->select('id')->count();
            $total_no_of_pages = ceil($total_records / $limit);

            $Teams = Team::where('club_id', $club_id)
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $count = $Teams->count();

            if (empty($Teams)) {
                return $this->sendError('Team do not exists');
            }
            else {
                $teams_array = array();
                foreach ($Teams as $Team) {
                    $teams_array[] = $this->get_team_array($Team);
                }
                $data = [
                    'page_no'           => $page_no,
                    'limit'             => $limit,
                    'total_records'     => $total_records,
                    'current_count'     => $count,
                    'total_no_of_pages' => $total_no_of_pages,
                    'teams_array' => $teams_array,
                ];
                // $data = $teams_array;   
                return $this->sendResponse($data, 'Teams Listing Retrived Successfully');
            }
        }
        else {
            return $this->sendError("You are not allowed to view teams");
        }
    }

    private function teams_details(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $bool = 0;

        $club_id = $user_id;
        $log_user_type = $user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '') {
            $team_id = $request->team_id;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team do not exists');
                }
                else {
                    $data = $this->get_team_array($Team);
                    return $this->sendResponse($data, 'Team details retrived Successfully');
                }
            }
            else {
                return $this->sendError("You are not allowed to view teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function create_team(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->name) && ltrim(rtrim($request->name)) != '' && isset($request->age_group) && ltrim(rtrim($request->age_group)) != '' && isset($request->coach) && ltrim(rtrim($request->coach)) != '') {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $name = test_input($request->name);

                $Team = Team::where('club_id', $club_id)->where('name', $name)->first();
                if (!empty($Team)) {
                    return $this->sendError('Team Already exists with same Name');
                }
                else {
                    $team_id = 0; {
                        $age_group = test_input($request->age_group);
                        $color = '';
                        if (isset($request->color))
                            $color = test_input($request->color);
                        $description = '';
                        if (isset($request->description))
                            $description = test_input($request->description);

                        $coach_id = test_input($request->coach);
                        $coach = UserProfessional::select('id', 'user_id')->where('club', $club_id)->where('user_id', $coach_id)->first();
                        if (empty($coach)) {
                            return $this->sendError('Selected Coach not found');
                        }


                        $file_logo = "default_image";
                        $Team = new Team();
                        $Team->club_id = $club_id;
                        $Team->coach_id = $coach_id;
                        if (isset($request->assistant_coach)) {
                            $ast_coach_id = 0;
                            $ast_coach_id = test_input($request->assistant_coach);
                            $ast_coach = UserProfessional::select('id', 'user_id')->where('club', $club_id)->where('user_id', $ast_coach_id)->first();
                            if (empty($ast_coach)) {
                                return $this->sendError('Selected Assistant Coach not found');
                            }
                            $Team->ast_coach_id = $ast_coach_id;
                        }
                        $Team->name = $name;
                        $Team->age_group = $age_group;
                        $Team->color = $color;
                        $Team->description = $description;
                        $Team->logo = $file_logo;
                        $Team->save();

                        $team_id = $Team->id;

                        $uploadsPath = $this->uploads_teams . '/' . $team_id;
                        $file_logo = "default_image";
                        if ($request->hasFile('logo')) {
                            $file = $request->file('logo');
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $file_logo = $fileName;
                        }
                        $Team = Team::find($team_id);
                        $Team->logo = $file_logo;
                        $Team->save();

                        if (isset($request->players) && $request->players != '') {
                            $players = $request->players;
                            $player_ids = explode(",", $players);

                            //  		filter the selected ids return those ids that are actully associated
                            $player_ids = UserProfessional::select('id', 'user_id')->where('club', $club_id)->whereIn('user_id', $player_ids)->pluck('user_id')->toArray();

                            foreach ($player_ids as $player_id) {
                                $user_type = get_user_type($player_id);
                                if ($user_type == $this->_PLAYER_USER) {
                                    $TeamMember = TeamMember::where('team_id', $team_id)->where('player_id', $player_id)->first();
                                    if (empty($TeamMember)) {
                                        $TeamMember = new TeamMember();
                                        $TeamMember->team_id = $team_id;
                                        $TeamMember->player_id = $player_id;
                                        $TeamMember->save();
                                    }
                                }
                            }
                        }
                    }
                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Created Successfully');
                    }
                    else {
                        return $this->sendError('Team Creation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to created teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function edit_team(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '' && isset($request->name) && ltrim(rtrim($request->name)) != '' && isset($request->age_group) && ltrim(rtrim($request->age_group))) {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $team_id = test_input($request->team_id);

                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team not exists');
                }
                else { {
                        $Team = Team::find($team_id);

                        $name = test_input($request->name);
                        $age_group = test_input($request->age_group);
                        $color = '';
                        if (isset($request->color))
                            $color = test_input($request->color);
                        $description = '';
                        if (isset($request->description))
                            $description = test_input($request->description);

                        $uploadsPath = $this->uploads_teams . '/' . $team_id;
                        $file_logo = $old_file = $Team->logo;
                        if ($request->hasFile('logo')) {
                            $file = $request->file('logo');
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $file_logo = $fileName;
                        }

                        $Team->name = $name;
                        $Team->age_group = $age_group;
                        $Team->color = $color;
                        $Team->description = $description;
                        $Team->logo = $file_logo;
                        $Team->save();
                    }

                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Updated Successfully');
                    }
                    else {
                        return $this->sendError('Team Updation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to edit teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function update_team_logo(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '') {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $team_id = test_input($request->team_id);

                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team not exists');
                }
                else { {
                        $Team = Team::find($team_id);

                        $uploadsPath = $this->uploads_teams . '/' . $team_id;
                        $file_logo = $old_file = $Team->logo;
                        if ($request->hasFile('logo')) {
                            $file = $request->file('logo');
                            $fileName = $this->upload_file_to_path($file, $uploadsPath);
                            $file_logo = $fileName;

                            if ($old_file != "" && $old_file != "default_image") {
                                $old_file_path = $uploadsPath . '/' . $old_file;
                                if (file_exists($old_file_path)) {
                                    unlink($old_file_path);
                                }
                            }
                        }

                        $Team->logo = $file_logo;
                        $Team->save();
                    }

                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Updated Successfully');
                    }
                    else {
                        return $this->sendError('Team Updation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to edit teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function update_team_coach(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '' && isset($request->coach) && ltrim(rtrim($request->coach)) != '') {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $team_id = test_input($request->team_id);

                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team not exists');
                }
                else { {
                        $coach_id = test_input($request->coach);
                        $coach = UserProfessional::select('id', 'user_id')->where('club', $club_id)->where('user_id', $coach_id)->first();
                        if ($coach == null) {
                            return $this->sendError('Selected Coach not found');
                        }

                        $Team = Team::find($team_id);
                        $Team->coach_id = $coach_id;
                        $Team->save();
                    }

                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Updated Successfully');
                    }
                    else {
                        return $this->sendError('Team Updation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to edit teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function update_team_ast_coach(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '' && isset($request->assistant_coach) && ltrim(rtrim($request->assistant_coach)) != '') {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $team_id = test_input($request->team_id);

                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team not exists');
                }
                else { {
                        $ast_coach_id = $Team->ast_coach_id;
                        if (isset($request->assistant_coach)) {
                            $ast_coach_id = test_input($request->assistant_coach);
                        }
                        $ast_coach = UserProfessional::select('id', 'user_id')->where('club', $club_id)->where('user_id', $ast_coach_id)->first();
                        if ($ast_coach == null) {
                            return $this->sendError('Selected Assistant Coach not found');
                        }

                        $Team = Team::find($team_id);
                        $Team->ast_coach_id = $ast_coach_id;
                        $Team->save();
                    }

                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Updated Successfully');
                    }
                    else {
                        return $this->sendError('Team Updation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to edit teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function add_team_member(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '' && isset($request->players) && $request->players != '') {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $team_id = test_input($request->team_id);

                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team not exists');
                }
                else { {
                        $players = $request->players;
                        $player_ids = explode(",", $players);

                        //  		filter the selected ids return those ids that are actully associated
                        $player_ids = UserProfessional::select('id', 'user_id')->where('club', $club_id)->whereIn('user_id', $player_ids)->pluck('user_id')->toArray();

                        foreach ($player_ids as $player_id) {
                            $user_type = get_user_type($player_id);
                            if ($user_type == $this->_PLAYER_USER) {
                                $TeamMember = TeamMember::where('team_id', $team_id)->where('player_id', $player_id)->first();
                                if (empty($TeamMember)) {
                                    $TeamMember = new TeamMember();
                                    $TeamMember->team_id = $team_id;
                                    $TeamMember->player_id = $player_id;
                                    $TeamMember->save();
                                }
                            }
                        }
                    }

                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Updated Successfully');
                    }
                    else {
                        return $this->sendError('Team Updation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to edit teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

    private function remove_team_member(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if (isset($request->team_id) && ltrim(rtrim($request->team_id)) != '' && isset($request->players) && $request->players != '') {
            $bool = 0;

            $club_id = $user_id;
            $log_user_type = $user_type;

            if (($log_user_type == 0 || $log_user_type == 3)) {
                $team_id = test_input($request->team_id);

                $Team = Team::where('id', $team_id)->where('club_id', $club_id)->first();
                if (empty($Team)) {
                    return $this->sendError('Team not exists');
                }
                else { {
                        $players = $request->players;
                        $player_ids = explode(",", $players);

                        foreach ($player_ids as $player_id) {
                            $user_type = get_user_type($player_id);
                            if ($user_type == $this->_PLAYER_USER) {
                                $TeamMember = TeamMember::where('team_id', $team_id)->where('player_id', $player_id)->where('status', 1)->first();
                                if (!empty($TeamMember)) {
                                    $member_id = $TeamMember->id;
                                    $teamMember = TeamMember::find($member_id);
                                    $teamMember->status = 0;
                                    $teamMember->updated_by = $club_id;
                                    $teamMember->save();
                                }
                            }
                        }
                    }

                    $Team = Team::find($team_id);
                    if (!empty($Team)) {
                        $data = $this->get_team_array($Team);
                        return $this->sendResponse($data, 'Team Updated Successfully');
                    }
                    else {
                        return $this->sendError('Team Updation fails, Please Try Again');
                    }
                }
            }
            else {
                return $this->sendError("You are not allowed to edit teams");
            }
        }
        else {
            return $this->sendError("Missing parameters in request");
        }
    }

}
