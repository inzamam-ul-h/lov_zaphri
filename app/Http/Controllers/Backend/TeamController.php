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
use App\Models\AgeGroup;

class TeamController extends MainController {

    private $views_path = "backend.teams";
    private $home_route = "teams.index";
    private $create_route = "teams.create";
    private $edit_route = "teams.edit";
    private $view_route = "teams.show";
    private $delete_route = "teams.destroy";
    private $active_route = "teams.activate";
    private $inactive_route = "teams.deactivate";
    private $msg_created = "Team added successfully.";
    private $msg_updated = "Team updated successfully.";
    private $msg_deleted = "Team deleted successfully.";
    private $msg_not_found = "Team not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Team name";
    private $msg_active = "Team made Active successfully.";
    private $msg_inactive = "Team made InActive successfully.";
    private $list_permission = "teams-listing";
    private $add_permission = "teams-add";
    private $edit_permission = "teams-edit";
    private $view_permission = "teams-view";
    private $status_permission = "teams-status";
    private $delete_permission = "teams-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Teams. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Team. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Team. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Team details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Team. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Team. Please Contact Administrator.";
    private $member_edit_permission = "team-members-edit";
    private $member_msg_removed = "Team Member has been removed successfully.";
    private $member_edit_permission_error_message = "Error: You are not authorized to add/remove Team Members. Please Contact Administrator.";

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

            $Records = Team::Join('users', 'teams.club_id', '=', 'users.id')
                    ->join('team_members', 'team_members.team_id', '=', 'teams.id')
                    ->select('teams.id', 'teams.name', 'teams.status', 'teams.created_at', 'teams.club_id', 'users.name as user_name', 'users.user_type')
                    ->orderBy('teams.id', 'DESC')
                    ->groupBy('teams.id');
            switch ($Auth_User->user_type) {
                case $this->_CLUB_USER: {
                        $club_id = $Auth_User->id;
                        $Records = $Records->where('club_id', '=', $club_id);
                    }
                    break;

                case $this->_COACH_USER: {
                        $coach_id = $Auth_User->id;
                        $Records = $Records->where('teams.coach_id', '=', $coach_id)->orwhere('teams.ast_coach_id', '=', $coach_id);
                    }
                    break;
                case $this->_PLAYER_USER: {
                        $player_id = $Auth_User->id;
                        $Records = $Records->where('team_members.player_id', '=', $player_id);
                    }
                    break;

                default: {
                        //
                    }
                    break;
            }

            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('name') && !empty($request->name)) {
                            $query->where('teams.name', 'like', "%{$request->get('name')}%");
                        }

                        if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                            $query->where('teams.status', '=', "{$request->get('status')}");
                        }

                        if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                            $query->whereDate('teams.created_at', '=', "{$request->get('created_at')}");
                        }

                        if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                            $query->whereDate('teams.updated_at', '=', "{$request->get('updated_at')}");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('title', function ($Records) {
                        $record_id = $Records->id;
                        $title = $Records->name;

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
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            $age_group = AgeGroup::select()->get();

            $club = $Auth_User;

            $club_coaches_ids = $this->get_club_coach_ids($club);
            $coaches = User::select('id as coach_id', 'name as coach_name')->whereIn('id', $club_coaches_ids)->get();

            $ast_coaches = User::select('id as ast_coach_id', 'name as ast_coach_name')->whereIn('id', $club_coaches_ids)->get();

            $club_player_ids = $this->get_club_player_ids($club);
            $players = User::select('id as player_id', 'name as player_name')->whereIn('id', $club_player_ids)->get();

            return view($this->views_path . '.create', compact('coaches', 'ast_coaches', 'players', 'age_group'));
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
                'name'        => 'required',
                'age_group'   => 'required',
                'description' => 'required',
                //'coach_id' => 'required',
                'logo'        => 'required',
                'player_id'   => 'required',
                'logo'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048'
            ]);
            if ($request->coach_id != '' && $request->ast_coach_id != '' && $request->coach_id == $request->ast_coach_id) {
                Flash::error('Coach and Assistant Coach can not be same');
                return redirect()->route($this->create_route);
            }

            $club = $Auth_User;
            $club_coaches_ids = $this->get_club_coach_ids($club);
            if (isset($request->coach_id)) {
                $coach_id = $request->coach_id;
                if (!in_array($coach_id, $club_coaches_ids)) {
                    Flash::error('Coach not Found');
                    return redirect()->route($this->create_route);
                }
            }
            if (isset($request->ast_coach_id)) {
                $coach_id = $request->ast_coach_id;
                if (!in_array($coach_id, $club_coaches_ids)) {
                    Flash::error('Coach not Found');
                    return redirect()->route($this->create_route);
                }
            }

            $user_id = $Auth_User->id;
            $file_logo = "default_image";
            $color = '';
            if (isset($request->color))
                $color = test_input($request->color);

            $Model_Data = new Team();
            $Model_Data->name = $request->name;
            $Model_Data->club_id = $user_id;
            $Model_Data->description = $request->description;
            $Model_Data->age_group = $request->age_group;
            if (isset($request->coach_id)) {
                $Model_Data->coach_id = $request->coach_id;
            }
            if (isset($request->ast_coach_id)) {
                $Model_Data->ast_coach_id = $request->ast_coach_id;
            }
            $Model_Data->color = $color;
            $Model_Data->logo = $file_logo;
            $Model_Data->created_by = $user_id;
            $Model_Data->save();

            $team_id = $Model_Data->id;

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

            $club_player_ids = $this->get_club_player_ids($club);

            $playerIds = $request->input('player_id');
            if (is_array($playerIds) && count($playerIds) > 0) {
                foreach ($playerIds as $playerId) {
                    if (in_array($playerId, $club_player_ids)) {
                        $teamMember = new TeamMember();
                        $teamMember->team_id = $team_id;
                        $teamMember->player_id = $playerId;
                        $teamMember->save();
                    }
                }
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = Team::find($id);
            if (empty($Model_Data || $this->is_not_authorized($Model_Data, $Auth_User, FALSE))) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $teamId = $id;

            $coach = User::select('*')->where('id', $Model_Data->coach_id)->first();

            $ast_coach = User::select('*')->where('id', $Model_Data->ast_coach_id)->first();

            $players = TeamMember::join('users', 'users.id', '=', 'team_members.player_id')
                    ->select('users.*')
                    ->where('team_members.team_id', $teamId)
                    ->where('team_members.status', 1)
                    ->get();

            $age_group = AgeGroup::select('*')->where('id', $Model_Data->age_group)->where('status', 1)->first();
            $Model_Data->age_group = $age_group->title;
            return view($this->views_path . '.show', compact("Model_Data", "coach", "ast_coach", "players"));
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
        if ($Auth_User->id == $id || ($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {
            $file_name = $request->file_name;
            if (empty($file_name)) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }
            $Model_Data = Team::find($id);
            if (empty($Model_Data || $this->is_not_authorized($Model_Data, $Auth_User))) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }
            $uploadsPath = $this->uploads_users . '/' . $Model_Data->id;
            if ($type == 'logo') {
                if ($file_name == $Model_Data->logo) {

                    $old_file_path = $uploadsPath . '/' . $Model_Data->logo;
                    File::delete($uploadsPath . "/" . $Model_Data->logo);
                    $Model_Data->logo = "default_image";
                }
            }

            return response()->json(['status' => true, 'messages' => 'File Successfully Deleted.']);
        }
        else {
            return response()->json(['status' => false, 'messages' => $this->edit_permission_error_message]);
        }
    }

    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Team::find($id);
            if (empty($Model_Data || $this->is_not_authorized($Model_Data, $Auth_User))) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $age_group = AgeGroup::select()->get();

            $club = User::find($Model_Data->club_id);

            $club_coaches_ids = $this->get_club_coach_ids($club);
            $coaches = User::select('id as coach_id', 'name as coach_name')->whereIn('id', $club_coaches_ids)->get();
            $ast_coaches = User::select('id as ast_coach_id', 'name as ast_coach_name')->whereIn('id', $club_coaches_ids)->get();

            $club_player_ids = $this->get_club_player_ids($club);
            $players = User::select('id as player_id', 'name as player_name')->whereIn('id', $club_player_ids)->get();

            $TeamMembers = TeamMember :: select('player_id')
                    ->where('team_id', $id)
                    ->where('status', 1)
                    ->get();
            $player_ids = [];
            foreach ($TeamMembers as $player) {
                $player_ids[] = $player->player_id;
            }

            return view($this->views_path . '.edit', compact("Model_Data", 'TeamMembers', 'coaches', 'ast_coaches', 'age_group', 'player_ids', 'players'));
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
            $Model_Data = Team::find($id);
            if (empty($Model_Data || $this->is_not_authorized($Model_Data, $Auth_User))) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $request->validate([
                'name'        => 'required',
                'age_group'   => 'required',
                'description' => 'required',
                //'coach_id' => 'required',
                'player_id'   => 'required',
                'logo'        => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048'
            ]);
            if ($request->coach_id != '' && $request->ast_coach_id != '' && $request->coach_id == $request->ast_coach_id) {
                Flash::error('Coach and Assistant Coach can not be same');
                return redirect()->route($this->create_route);
            }
            $team_id = $id;
            $club = User::find($Model_Data->club_id);
            $club_coaches_ids = $this->get_club_coach_ids($club);
            if (isset($request->coach_id)) {
                $coach_id = $request->coach_id;
                if (!in_array($coach_id, $club_coaches_ids)) {
                    Flash::error('Coach not Found');
                    return redirect()->route($this->create_route);
                }
            }
            if (isset($request->ast_coach_id)) {
                $coach_id = $request->ast_coach_id;
                if (!in_array($coach_id, $club_coaches_ids)) {
                    Flash::error('Coach not Found');
                    return redirect()->route($this->create_route);
                }
            }

            $uploadsPath = $this->uploads_teams . '/' . $team_id;
            $file_logo = $old_file = $Model_Data->logo;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_logo = $fileName;
            }

            $Model_Data->name = $request->name;
            $Model_Data->description = $request->description;
            $Model_Data->age_group = $request->age_group;
            if (isset($request->coach_id)) {
                $Model_Data->coach_id = $request->coach_id;
            }
            else {
                $Model_Data->coach_id = null;
            }

            if (isset($request->ast_coach_id)) {
                $Model_Data->ast_coach_id = $request->ast_coach_id;
            }
            else {
                $Model_Data->ast_coach_id = null;
            }
            $Model_Data->logo = $file_logo;

            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            $planDetails = TeamMember::select('id')->where('team_id', $team_id)->where('status', 1)->get();
            foreach ($planDetails as $planDetail) {
                $planDetail = TeamMember::find($planDetail->id);
                $planDetail->status = 2;
                $planDetail->updated_by = $Auth_User->id;
                $planDetail->save();
            }
            $club_player_ids = $this->get_club_player_ids($club);
            if (isset($request->player_id)) {
                $playerIds = $request->player_id;
                foreach ($playerIds as $playerId) {
                    if (in_array($playerId, $club_player_ids)) {
                        $teamMember = TeamMember::select('id')->where('team_id', $team_id)->where('player_id', $playerId)->where('status', 2)->first();
                        if (!empty($teamMember)) {
                            $teamMember = TeamMember::find($teamMember->id);
                            $teamMember->status = 1;
                            $teamMember->updated_by = $Auth_User->id;
                            $teamMember->save();
                        }
                        else {
                            $teamMember = new TeamMember();
                            $teamMember->team_id = $team_id;
                            $teamMember->player_id = $playerId;
                            $teamMember->status = 1;
                            $teamMember->created_by = $Auth_User->id;
                            $teamMember->save();
                        }
                    }
                }
            }


            $teamMembers = TeamMember::select('id')->where('team_id', $team_id)->where('status', 2)->get();
            foreach ($teamMembers as $teamMember) {
                $teamMember = TeamMember::find($teamMember->id);
                $teamMember->status = 0;
                $teamMember->updated_by = $Auth_User->id;
                $teamMember->save();
            }
            // exit();
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
            $Model_Data = Team::find($id);
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
            $Model_Data = Team::find($id);
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

    public function remove_coach($team_id, $user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->member_edit_permission) || $Auth_User->can('all')) {
            $id = $team_id;
            $Model_Data = Team::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            if ($Model_Data->coach_id == $user_id) {
                $Model_Data->coach_id = null;
            }
            elseif ($Model_Data->ast_coach_id == $user_id) {
                $Model_Data->ast_coach_id = null;
            }
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->member_msg_removed);
            return redirect()->route($this->view_route, $team_id);
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function remove_player($team_id, $user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->member_edit_permission) || $Auth_User->can('all')) {
            $id = $team_id;
            $Model_Data = Team::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $teamMember = TeamMember::select('id')->where('team_id', $team_id)->where('player_id', $user_id)->where('status', 1)->first();
            if (!empty($teamMember)) {
                $teamMember = TeamMember::find($teamMember->id);
                $teamMember->status = 0;
                $teamMember->updated_by = $Auth_User->id;
                $teamMember->save();
            }

            Flash::success($this->member_msg_removed);
            return redirect()->route($this->view_route, $team_id);
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function add_player(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->member_edit_permission) || $Auth_User->can('all')) {
            $request->validate([
                'team_id'   => 'required',
                'player_id' => 'required'
            ]);
            $id = $team_id = $request->team_id;
            $Model_Data = Team::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $club = User::find($Model_Data->club_id);

            $planDetails = TeamMember::select('id')->where('team_id', $team_id)->where('status', 1)->get();
            foreach ($planDetails as $planDetail) {
                $planDetail = TeamMember::find($planDetail->id);
                $planDetail->status = 2;
                $planDetail->updated_by = $Auth_User->id;
                $planDetail->save();
            }
            $club_player_ids = $this->get_club_player_ids($club);
            if (isset($request->player_id)) {
                $playerIds = $request->player_id;
                foreach ($playerIds as $playerId) {
                    if (in_array($playerId, $club_player_ids)) {
                        $teamMember = TeamMember::select('id')->where('team_id', $team_id)->where('player_id', $playerId)->where('status', 2)->first();
                        if (!empty($teamMember)) {
                            $teamMember = TeamMember::find($teamMember->id);
                            $teamMember->status = 1;
                            $teamMember->updated_by = $Auth_User->id;
                            $teamMember->save();
                        }
                        else {
                            $teamMember = new TeamMember();
                            $teamMember->team_id = $team_id;
                            $teamMember->player_id = $playerId;
                            $teamMember->status = 1;
                            $teamMember->created_by = $Auth_User->id;
                            $teamMember->save();
                        }
                    }
                }
            }


            $teamMembers = TeamMember::select('id')->where('team_id', $team_id)->where('status', 2)->get();
            foreach ($teamMembers as $teamMember) {
                $teamMember = TeamMember::find($teamMember->id);
                $teamMember->status = 0;
                $teamMember->updated_by = $Auth_User->id;
                $teamMember->save();
            }

            Flash::success($this->member_msg_removed);
            return redirect()->route($this->view_route, $team_id);
        }
        else {
            Flash::error($this->edit_permission_error_message);
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
        $club_id = $Model_Data->club_id;
        $bool = 1;
        if ($club_id == $Auth_User->id) {
            $bool = 0;
        }
        else {
            if ($Auth_User->can('all')) {
                $bool = 0;
            }
            else if ($is_edit == FALSE && ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER)) {
                $user_club_id = get_club_id($Auth_User->id);
                if ($user_club_id == $club_id) {
                    $bool = 0;
                }
            }
        }

        return $bool;
    }

}
