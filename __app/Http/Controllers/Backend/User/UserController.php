<?php

namespace App\Http\Controllers\Backend\User;

use Auth;
use File;
use Flash;
use DateTime;
use Response;
use Attribute;
use Datatables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Module;
use App\Models\Employer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ModelHasRoles;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\MainController as MainController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Rules\MatchOldPassword;
use App\Models\UserCalendar;
use App\Models\UserPersonal;
use App\Models\UserEducation;
use App\Models\UserProfessional;
use App\Models\AgeGroup;
use App\Models\TimeZone;
use App\Models\Experience;

class UserController extends MainController {

    protected $uploads_root = "uploads";
    private $uploads_user_path = "uploads/users/";
    private $views_path = "backend.users";
    private $home_route = "users.index";
    private $create_route = "users.create";
    private $edit_route = "users.edit";
    private $view_route = "users.show";
    private $delete_route = "users.destroy";
    private $active_route = "users.activate";
    private $inactive_route = "users.deactivate";
    private $approve_route = "users.approve";
    private $reject_route = "users.deactivate";
    private $update_password_route = "users.updatePassword";
    private $msg_created = "User added successfully.";
    private $msg_updated = "User updated successfully.";
    private $msg_deleted = "User deleted successfully.";
    private $msg_not_found = "User not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same User name";
    private $msg_active = "User made Active successfully.";
    private $msg_inactive = "User made InActive successfully.";
    private $msg_approved = "User application has been approved successfully.";
    private $msg_rejected = "User application has been rejected successfully.";
    private $list_permission = "users-listing";
    private $add_permission = "users-add";
    private $edit_permission = "users-edit";
    private $view_permission = "users-view";
    private $status_permission = "users-status";
    private $delete_permission = "users-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Users. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add User. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update User. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View User details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of User. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete User. Please Contact Administrator.";

    /**
     * Display a listing of the Model.
     *
     */
    public function index($user_type = 0) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_COACH_USER || $Auth_User->user_type == $this->_COACH_USER) {
            return redirect()->route($this->view_route, $Auth_User->id);
        }
        elseif ($Auth_User->user_type == $this->_CLUB_USER) {
            $club = $Auth_User;
            $ids = array();
            switch ($user_type) {
                case $this->_COACH_USER: {
                        $ids = $this->get_club_coach_ids($club);
                    }
                    break;

                case $this->_PLAYER_USER: {
                        $ids = $this->get_club_player_ids($club);
                    }
                    break;

                default: {
                        Flash::error($this->list_permission_error_message);
                        return redirect()->route($this->dashboard_route);
                    }
                    break;
            }
            $records_exists = (count($ids) > 0) ? 1 : 0;

            return view($this->views_path . '.listing', compact("records_exists", 'user_type'));
        }
        elseif ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 0;
            $records = User::select(['id'])->where('id', '>', 1)->where('user_type', '=', $user_type)->get();
            foreach ($records as $record) {
                $records_exists = 1;
            }

            return view($this->views_path . '.listing', compact("records_exists", 'user_type'));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function login_as_user($user_id) {
        $Auth_User = Auth::user();

        if ($Auth_User->user_type == 0 || $Auth_User->can('all')) {
            $Model_Data = User::find($user_id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            Auth::logout(); // for end current session
            Auth::loginUsingId($user_id);

            Flash::success("Successfully Logged in");
            return redirect()->route($this->dashboard_route);
        }
        else {
            Flash::error("You do not have permission to login as other users");
            return redirect()->route($this->dashboard_route);
        }
    }

    public function datatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_CLUB_USER) {
            $club = $Auth_User;
            $ids = array();
            $ids[] = 0;
            $user_type = $request->user_type;
            switch ($user_type) {
                case $this->_COACH_USER: {
                        $ids = $this->get_club_coach_ids($club);
                    }
                    break;

                case $this->_PLAYER_USER: {
                        $ids = $this->get_club_player_ids($club);
                    }
                    break;

                default: {
                        Flash::error($this->list_permission_error_message);
                        return redirect()->route($this->dashboard_route);
                    }
                    break;
            }
            return $this->admin_datatable($request, $ids);
        }
        elseif ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            return $this->admin_datatable($request);
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function admin_datatable(Request $request, $ids = NULL) {
        $Records = User::select(['id', 'user_type', 'name', 'email', 'phone', 'status', 'admin_approved', 'created_at']);
        if ($request->has('user_type') && $request->get('user_type') != -1 && $request->get('user_type') != '') {
            if ($ids !== NULL) {
                $Records = $Records->whereIn('users.id', $ids);
            }
            else {
                $Records = $Records->where('users.id', '>', 1)->where('users.user_type', '=', "{$request->get('user_type')}");
            }
        }
        $response = Datatables::of($Records)
                ->filter(function ($query) use ($request) {
                    if ($request->has('name') && !empty($request->name)) {
                        $query->where('users.name', 'like', "%{$request->get('name')}%");
                    }

                    if ($request->has('email') && !empty($request->email)) {
                        $query->where('users.email', 'like', "%{$request->get('email')}%");
                    }

                    if ($request->has('phone') && !empty($request->phone)) {
                        $query->where('users.phone', 'like', "%{$request->get('phone')}%");
                    }

                    if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                        $query->where('users.status', '=', "{$request->get('status')}");
                    }

                    if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                        $query->whereDate('users.created_at', '=', "{$request->get('created_at')}");
                    }

                    if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                        $query->whereDate('users.updated_at', '=', "{$request->get('updated_at')}");
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
                ->addColumn('admin_approved', function ($Records) {
                    $str = dispaly_status_in_table($Records->admin_approved);

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
                    $admin_approved = $Records->admin_approved;

                    $str = '<div>';
                    //if($Auth_User->can($this->view_permission) || $Auth_User->can('all'))
                    {
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

                        if ($admin_approved == 0) {
                            $str .= approve_link_in_table($this->approve_route, $record_id) . '  ';
                        }
                        elseif (($Auth_User->user_type == 0 || $Auth_User->can('all')) && $admin_approved == 1) {
                            $str .= '<a class="dropdown-item text-warning" ui-toggle-class="bounce" ui-target="#animate" onclick="loginModal(' . $record_id . ')">
								<i class="fa fa-eye text-warning"></i> Login As
								</a>';
                        }
                    }

                    /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                      {
                      $str.= delete_link_in_table($record_id);
                      } */

                    $str .= '</div>';
                    /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                      {
                      $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->name_en);
                      } */
                    if (($Auth_User->user_type == 0 || $Auth_User->can('all')) && $admin_approved == 1) {
                        $str .= login_modal_in_table('users.login_as_user', $record_id, $Records->name);
                    }
                    return $str;
                })
                ->rawColumns(['sr_no', 'user_type', 'status', 'admin_approved', 'created_at', 'action'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function club_approve_datatable(Request $request) {
        $Records = User::select(['id', 'user_type', 'name', 'email', 'phone', 'status', 'admin_approved', 'created_at'])
                ->where('users.user_type', $this->_CLUB_USER)
                ->where('users.admin_approved', 0);
        $response = Datatables::of($Records)
                ->filter(function ($query) use ($request) {
                    if ($request->has('name') && !empty($request->name)) {
                        $query->where('users.name', 'like', "%{$request->get('name')}%");
                    }

                    if ($request->has('email') && !empty($request->email)) {
                        $query->where('users.email', 'like', "%{$request->get('email')}%");
                    }

                    if ($request->has('phone') && !empty($request->phone)) {
                        $query->where('users.phone', 'like', "%{$request->get('phone')}%");
                    }

                    if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                        $query->where('users.status', '=', "{$request->get('status')}");
                    }

                    if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                        $query->whereDate('users.created_at', '=', "{$request->get('created_at')}");
                    }

                    if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                        $query->whereDate('users.updated_at', '=', "{$request->get('updated_at')}");
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
                ->addColumn('admin_approved', function ($Records) {
                    $str = dispaly_status_in_table($Records->admin_approved);

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
                    $admin_approved = $Records->admin_approved;

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

                        if ($admin_approved == 0) {
                            $str .= approve_link_in_table($this->approve_route, $record_id) . '  ';
                        }
                        elseif (($Auth_User->user_type == 0 || $Auth_User->can('all')) && $admin_approved == 1) {
                            $str .= '<a class="dropdown-item text-warning" ui-toggle-class="bounce" ui-target="#animate" onclick="loginModal(' . $record_id . ')">
								<i class="fa fa-eye text-warning"></i> Login As
								</a>';
                        }
                    }

                    /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                      {
                      $str.= delete_link_in_table($record_id);
                      } */

                    $str .= '</div>';
                    /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                      {
                      $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->name_en);
                      } */
                    if (($Auth_User->user_type == 0 || $Auth_User->can('all')) && $admin_approved == 1) {
                        $str .= login_modal_in_table('users.login_as_user', $record_id, $Records->name);
                    }
                    return $str;
                })
                ->rawColumns(['sr_no', 'user_type', 'status', 'admin_approved', 'created_at', 'action'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function create_user_by_type($user_type = 0) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            $roles_array = Role::select(['id', 'name', 'display_to'])->where('id', '>=', 1)->orderby('name', 'asc')->get();
            $TimeZones = TimeZone::select()->get();
            $age_groups = AgeGroup::select()->get();
            $clubs = User::where('user_type', 3)->get();
            $no_of_experience = Experience::select()->get();

            return view($this->views_path . '.create', compact("user_type", "roles_array", "no_of_experience", "age_groups", "TimeZones", "clubs"));
        }
        elseif ($Auth_User->user_type == $this->_CLUB_USER) {
            switch ($user_type) {
                case $this->_COACH_USER: {
                        
                    }
                    break;
                case $this->_PLAYER_USER: {
                        
                    }
                    break;

                default: {
                        Flash::error($this->add_permission_error_message);
                        return redirect()->route($this->home_route);
                    }
                    break;
            }
            $roles_array = Role::select(['id', 'name', 'display_to'])->where('id', '>=', 1)->orderby('name', 'asc')->get();
            $TimeZones = TimeZone::select()->get();
            $age_groups = AgeGroup::select()->get();
            $no_of_experience = Experience::select()->get();
            $clubs = User::where('id', $Auth_User->id)->where('user_type', 3)->get();

            return view($this->views_path . '.create', compact("user_type", "roles_array", "no_of_experience", "age_groups", "TimeZones", "clubs"));
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
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
            $user_type = $Auth_User->user_type;
            $roles_array = Role::select(['id', 'name', 'display_to'])->where('id', '>=', 1)->orderby('name', 'asc')->get();
            $TimeZones = TimeZone::select()->get();
            $age_groups = AgeGroup::select()->get();
            $no_of_experience = Experience::select()->get();
            $clubs = User::where('user_type', 3)->get();

            return view($this->views_path . '.create', compact("user_type", "roles_array", "age_groups", "TimeZones", "no_of_experience", "clubs"));
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    private function get_redirect_route($user_type, $route) {
        if ($route == 'view') {
            switch ($user_type) {
                case 1: $route = 'users.coaches_listing';
                    break;
                case 2: $route = 'users.players_listing';
                    break;
                case 3: $route = 'users.clubs_listing';
                    break;
                case 4: $route = 'users.parents_listing';
                    break;
                default: $route = 'users.admin_listing';
                    break;
            }
            return $route;
        }
        else {
            $string = '';
            switch ($user_type) {
                case 1: $string = 'coaches.' . $route;
                    break;
                case 2: $string = 'players.' . $route;
                    break;
                case 3: $string = 'clubs.' . $route;
                    break;
                case 4: $string = 'parents.' . $route;
                    break;
                default: $string = 'users.' . $route;
                    break;
            }
            return $string;
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
        if ($Auth_User->user_type == $this->_CLUB_USER || ($Auth_User->can($this->add_permission) || $Auth_User->can('all'))) {
            $user_type = $request->input('user_type');
            if ($Auth_User->user_type == $this->_CLUB_USER) {
                switch ($user_type) {
                    case $this->_COACH_USER: {
                            
                        }
                        break;
                    case $this->_PLAYER_USER: {
                            
                        }
                        break;

                    default: {
                            Flash::error($this->add_permission_error_message);
                            return redirect()->route($this->home_route);
                        }
                        break;
                }
            }

            $request->validate([
                'reg_no'   => 'max:16',
                'f_name'   => 'required|string|min:2|max:255',
                'l_name'   => 'required|string|min:2|max:255',
                'phone'    => 'min:10|max:50|unique:users',
                'email'    => 'required|string|email|min:6|max:255|unique:users',
                'password' => ['required', Password::min(6)->mixedCase()->numbers()],
                'photo'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            //if ($Auth_User->user_type == 0)
            {
                $uniqueCode = '';
                do {
                    $unique = 1;
                    $uniqueCode = Str::random(9);
                    $res = User::where('unique_code', $uniqueCode)->get();
                    if ($res->isEmpty()) {
                        $unique = $uniqueCode;
                    }
                    else {
                        $unique = 0;
                    }
                }
                while ($unique == 0);
            }

            $name = $request->f_name . ' ' . $request->l_name;

            $Model_Data = new User();
            $Model_Data->name = $name;
            $Model_Data->email = $request->input('email');
            if ($user_type != 3) {
                $Model_Data->phone = $request->input('phone');
            }
            $email = $Model_Data->email;

            $exists = 0;
            $email_expl = explode('@', $email);
            $public_url = createSlug($email_expl[0]);
            $res = User::where('public_url', $public_url)->first();
            if (!empty($res)) {
                $exists = 1;
            }
            if ($exists == 1) {
                $public_url = '';
                do {
                    $unique = 1;
                    $public_url = random_number();
                    $res = User::where('public_url', $public_url)->first();
                    if (!empty($res)) {
                        $unique = 0;
                    }
                }
                while ($unique == 0);
            }
            $verified_token = random_number();
            $Model_Data->public_url = $public_url;

            $Model_Data->user_type = $request->input('user_type');
            $Model_Data->password = Hash::make($request->password);
            $Model_Data->created_by = $request->input('user_id');
            $Model_Data->unique_code = $unique;
            $Model_Data->email_verified = 1;
            $Model_Data->profile_status = 1;
            $Model_Data->verified_token = $verified_token;
            $Model_Data->verified = 1;
            $Model_Data->admin_approved = 1;
            $Model_Data->status = 1;
            $Model_Data->created_by = $Auth_User->id;

            $Model_Data->save();

            $this->send_welcome_email($Model_Data);

            $Model_Data->assignRole(get_user_type_role($user_type));

            $user_id = $Model_Data->id;

            $uploadsPath = $this->uploads_users . '/' . $user_id;
            $file_image = 'default_image';
            if (isset($request->photo) && $request->photo != null) {
                $file = $request->file('photo');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_image = $fileName;

                $SITE_URL = env('APP_URL');
                $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/user.png";
                $uploadsPath = $SITE_URL . "/" . $this->uploads_users . '/' . $user_id;
                $photo = $defaultImage;
                if (!empty($file_image) && $file_image != 'default_image')
                    $photo = $uploadsPath . "/" . $file_image;
                $photo_url = $file_image;

                $Model_Data = User::find($user_id);
                $Model_Data->photo_url = trim($photo_url);
                $Model_Data->photo = trim($photo);
                $Model_Data->save();
            }


            if ($user_type > 0) {

                $UserPersonal = new UserPersonal();
                $UserPersonal->first_name = trim($request->f_name);
                $UserPersonal->last_name = trim($request->l_name);
                $UserPersonal->coachpic = trim($file_image);
                $UserPersonal->user_id = $user_id;

                switch ($user_type) {
                    case $this->_CLUB_USER: {
                            $UserPersonal->reg_no = trim($request->reg_no);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->contact_person = trim($request->contact_person);
                            $UserPersonal->contact_number = trim($request->contact_number);
                            $UserPersonal->about_me = trim($request->about_me);
                        }
                        break;

                    case $this->_COACH_USER: {

                            $UserPersonal->meetinglink = trim($request->meeting_link);
                            $UserPersonal->gender = trim($request->gender);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->zip_code = trim($request->zip_code);
                            $UserPersonal->about_me = trim($request->about_me);
                        }
                        break;
                    case $this->_PLAYER_USER: {
                            $UserPersonal->dob = trim($request->dob);
                            $UserPersonal->gender = trim($request->gender);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->zip_code = trim($request->zip_code);
                            $UserPersonal->about_me = trim($request->about_me);
                        }
                        break;

                    case $this->_PARENT_USER: {
                            $UserPersonal->gender = trim($request->gender);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->zip_code = trim($request->zip_code);
                        }
                        break;
                    default: {
                            //
                        }
                        break;
                }

                $UserPersonal->created_by = $Auth_User->id;
                $UserPersonal->save();
            }

            $club_authentication = 0;
            $club_associated = $request->club_associated;
            if ($Auth_User->user_type == $this->_CLUB_USER) {
                $club_associated = $Auth_User->id;
                $club_authentication = 1;
            }
            if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
                $UserProfessional = new UserProfessional();
                $UserProfessional->user_id = $user_id;
                $UserProfessional->club = $club_associated;
                $UserProfessional->club_authentication = $club_authentication;

                if (isset($request->organizational_name)) {
                    $UserProfessional->organizational_name = $request->organizational_name;
                }
                if (isset($request->no_of_experience)) {
                    $UserProfessional->no_of_experience = $request->no_of_experience;
                }
                $UserProfessional->agegroups = $request->age_group;
                if (isset($request->experience)) {
                    $UserProfessional->experience = $request->experience;
                }
                $UserProfessional->created_by = $Auth_User->id;
                $UserProfessional->save();
            }

            if (isset($request->time_zone)) {
                $UserCalendar = new UserCalendar();
                $UserCalendar->user_id = $user_id;
                $UserCalendar->time_zone = $request->time_zone;
                $UserCalendar->created_by = $Auth_User->id;
                $UserCalendar->save();
            }

            Flash::success($this->msg_created);
            $route = $this->get_redirect_route($user_type, 'view');
            return redirect()->route($route, $user_type);
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == $this->_CLUB_USER || $Auth_User->id == $id || $Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $UserProfessional = UserProfessional::where('user_id', $id)->first();

            if (!empty($UserProfessional) && $Auth_User->id != $id && $Auth_User->user_type == $this->_CLUB_USER && $Auth_User->id != $UserProfessional->club) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->dashboard_route));
            }
            $Model_Data = User::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $TimeZones = TimeZone::select()->get();
            $age_groups = AgeGroup::select()->get();
            $clubs = User::where('user_type', 3)->get();
            $UserCalendar = UserCalendar::where('user_id', $Model_Data->id)->first();
            $UserPersonal = UserPersonal::where('user_id', $Model_Data->id)->first();

            if ($Auth_User->user_type == $this->_CLUB_USER) {
                $id = UserProfessional::where('user_id', $Model_Data->id)->where('club', $Auth_User->id)->first();
                if (empty($id)) {
                    Flash::error($this->msg_not_found);
                    return redirect(route($this->home_route));
                }
                $age_you_coached = get_age_group_title($UserProfessional->agegroups ?? NULL);

                $experience = get_experience_title($UserProfessional->no_of_experience ?? NULL);

                $UserProfessional->agegroups = $age_you_coached;
                $UserProfessional->no_of_experience = $experience;
            }
            if ($Auth_User->user_type == $this->_COACH_USER || $Auth_User->user_type == $this->_PLAYER_USER) {

                $age_you_coached = get_age_group_title($UserProfessional->agegroups ?? NULL);

                $experience = get_experience_title($UserProfessional->no_of_experience ?? NULL);

                $UserProfessional->agegroups = $age_you_coached;
                $UserProfessional->no_of_experience = $experience;
            }



            return view($this->views_path . '.show', compact("Model_Data", "age_groups", "TimeZones", "clubs", "UserProfessional", "UserCalendar", "UserPersonal"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function file_delete($id, $type, Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->id == $id || ($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {
            $file_name = $request->file_name;
            if (empty($file_name)) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }
            $Model_Data = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }
            $uploadsPath = $this->uploads_users . '/' . $Model_Data->id;

            $coachpic = '';
            $old_image = $Model_Data->photo_url;
            $image_array = explode('/', $old_image);
            $index = (count($image_array) - 1);
            $old_image = $image_array[$index];

            if ($old_image != "" && $old_image != "user.png" && $old_image != "default_image") {
                File::delete($uploadsPath . "/" . $old_image);
            }

            $SITE_URL = env('APP_URL');
            $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/user.png";
            $Model_Data->photo_url = "user.png";
            $Model_Data->photo = trim($defaultImage);
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            $UserPersonal = UserPersonal::where('user_id', $Model_Data->id)->first();
            if (!empty($UserPersonal)) {
                $UserPersonal->coachpic = "user.png";
                $UserPersonal->updated_by = $Auth_User->id;
                $UserPersonal->save();
            }

            return response()->json(['status' => true, 'messages' => 'File Successfully Deleted.']);
        }
        else {
            return response()->json(['status' => false, 'messages' => $this->edit_permission_error_message]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user_id) {
        $id = $user_id;
        $Auth_User = Auth::user();
        if ($Auth_User->id == $id || ($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {
            $Model_Data = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $TimeZones = TimeZone::select()->get();
            $age_groups = AgeGroup::select()->get();
            $no_of_experience = Experience:: select()->get();
            $clubs = User::where('user_type', 3)->get();
            $UserCalendar = UserCalendar::where('user_id', $Model_Data->id)->first();
            $UserProfessional = UserProfessional::where('user_id', $Model_Data->id)->first();
            $UserPersonal = UserPersonal::where('user_id', $Model_Data->id)->first();

            return view($this->views_path . '.edit', compact("Model_Data", "no_of_experience", "age_groups", "TimeZones", "clubs", "UserProfessional", "UserCalendar", "UserPersonal"));
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
    public function update($user_id, Request $request) {
        $id = $user_id;
        $Auth_User = Auth::user();
        if ($Auth_User->id == $id || ($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {
            $Model_Data = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            if (isset($request->password)) {
                $request->validate([
                    'reg_no'   => 'min:16|max:16',
                    'f_name'   => 'required|string|min:2|max:50',
                    'l_name'   => 'required|string|min:2|max:50',
                    'email'    => ['string', 'min:6', 'max:255', Rule::unique('users')->ignore($Model_Data->id)],
                    'phone'    => ['string', 'min:10', 'max:50', Rule::unique('users')->ignore($Model_Data->id)],
                    'photo'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048',
                    'password' => [Password::min(6)->mixedCase()->numbers()],
                ]);

                $Model_Data->password = \Hash::make($request->password);
            }
            else {
                $request->validate([
                    'reg_no' => 'max:16',
                    'f_name' => 'required|string|min:2|max:50',
                    'l_name' => 'required|string|min:2|max:50',
                    'email'  => ['string', 'min:6', 'max:255', Rule::unique('users')->ignore($Model_Data->id)],
                    'phone'  => ['string', 'min:10', 'max:50', Rule::unique('users')->ignore($Model_Data->id)],
                    'photo'  => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048',
                ]);
            }

            $uploadsPath = $this->uploads_users . '/' . $user_id;

            $coachpic = '';
            $image = $Model_Data->photo_url;
            $image_array = explode('/', $image);

            $index = (count($image_array) - 1);

            $image = $old_image = $image_array[$index];
            if (isset($request->photo) && $request->photo != null) {
                $file = $request->file('photo');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $image = $fileName;

                if ($old_image != "" && $old_image != "user.png" && $old_image != "default_image") {
                    File::delete($uploadsPath . "/" . $old_image);
                }
                $file_image = $image;
                $SITE_URL = env('APP_URL');
                $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/user.png";
                $uploadsPath = $SITE_URL . "/" . $this->uploads_users . '/' . $user_id;
                $photo = $defaultImage;
                if (!empty($file_image) && $file_image != 'default_image')
                    $photo = $uploadsPath . "/" . $file_image;
                $photo_url = $coachpic = $file_image;
                $Model_Data->photo_url = trim($photo_url);
                $Model_Data->photo = trim($photo);
            }



            $name = trim($request->f_name . ' ' . $request->l_name);
            $Model_Data->name = $name;
            if (isset($request->email))
                $Model_Data->email = $request->email;
            if (isset($request->phone))
                $Model_Data->phone = $request->phone;
            if (isset($request->public_url) && $request->public_url != null) {
                $Model_Data->public_url = $request->public_url;
            }
            if ($Model_Data->email_verified == 1) {
                $Model_Data->profile_status = 1;
            }
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            $user_type = $Model_Data->user_type;

            $UserPersonal = UserPersonal::select('id')->where('user_id', $user_id)->first();
            if (!empty($UserPersonal)) {

                $UserPersonal->first_name = trim($request->f_name);
                $UserPersonal->last_name = trim($request->l_name);
                if ($coachpic != '') {
                    $UserPersonal->coachpic = trim($file_image);
                }

                switch ($user_type) {
                    case $this->_CLUB_USER: {
                            $UserPersonal->reg_no = trim($request->reg_no);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->contact_person = trim($request->contact_person);
                            $UserPersonal->contact_number = trim($request->contact_number);
                            $UserPersonal->about_me = trim($request->about_me);
                        }
                        break;

                    case $this->_COACH_USER: {

                            $UserPersonal->meetinglink = trim($request->meeting_link);
                            $UserPersonal->gender = trim($request->gender);
                            $UserPersonal->zip_code = trim($request->zip_code);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->about_me = trim($request->about_me);
                        }
                        break;
                    case $this->_PLAYER_USER: {
                            if (isset($request->dob)) {
                                $UserPersonal->dob = trim($request->dob);
                            }

                            $UserPersonal->address = trim($request->address);

                            $UserPersonal->gender = trim($request->gender);
                            $UserPersonal->zip_code = trim($request->zip_code);
                            $UserPersonal->about_me = trim($request->about_me);
                        }
                        break;

                    case $this->_PARENT_USER: {
                            $UserPersonal->gender = trim($request->gender);
                            $UserPersonal->address = trim($request->address);
                            $UserPersonal->zip_code = trim($request->zip_code);
                        }
                        break;
                    default: {
                            //
                        }
                        break;
                }

                $UserPersonal->updated_by = $Auth_User->id;
                $UserPersonal->save();
            }

            if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
                $UserProfessional = UserProfessional::where('user_id', $user_id)->first();
                if (!empty($UserProfessional)) {
                    $UserProfessional = UserProfessional::find($UserProfessional->id);
                    if ($UserProfessional->club != $request->club_associated) {
                        $UserProfessional->club = $request->club_associated;
                        $UserProfessional->club_authentication = 0;
                    }
                    $UserProfessional->organizational_name = $request->organizational_name;
                    $UserProfessional->agegroups = $request->age_group;
                    if (isset($request->no_of_experience)) {
                        $UserProfessional->no_of_experience = $request->no_of_experience;
                    }
                    if (isset($request->experience)) {
                        $UserProfessional->experience = $request->experience;
                    }
                    $UserProfessional->updated_by = $user_id;
                    $UserProfessional->save();
                }
                else {
                    $UserProfessional = new UserProfessional();
                    $UserProfessional->user_id = $user_id;
                    $UserProfessional->club = $request->club_associated;
                    $UserProfessional->club_authentication = 0;
                    $UserProfessional->organizational_name = $request->organizational_name;
                    if (isset($request->no_of_experience)) {
                        $UserProfessional->no_of_experience = $request->no_of_experience;
                    }
                    if (isset($request->experience)) {
                        $UserProfessional->experience = $request->experience;
                    }
                    $UserProfessional->agegroups = $request->age_group;
                    $UserProfessional->created_by = $Auth_User->id;
                    $UserProfessional->save();
                }
            }

            if (isset($request->time_zone)) {
                $time_zone = $request->time_zone;
                $UserCalendar = UserCalendar::where('user_id', $user_id)->first();
                if (!empty($UserCalendar)) {
                    $UserCalendar = UserCalendar::find($UserCalendar->id);
                    $UserCalendar->time_zone = $time_zone;
                    $UserCalendar->updated_by = $user_id;
                    $UserCalendar->save();
                }
                else {
                    $UserCalendar = new UserCalendar();
                    $UserCalendar->user_id = $user_id;
                    $UserCalendar->time_zone = $time_zone;
                    $UserCalendar->created_by = $Auth_User->id;
                    $UserCalendar->save();
                }
            }

            Flash::success($this->msg_updated);
            return redirect()->route($this->view_route, $user_id);
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->view_route, $id);
        }
    }

    /**
     * Show the form for change password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword() {
        return view($this->views_path . '.password');
    }

    /**
     * Update the password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request) {
        $request->validate([
            'current_password'     => ['required', new MatchOldPassword],
            'new_password'         => ['required', Password::min(6)->mixedCase()->numbers()],
            'new_confirm_password' => ['same:new_password'],
        ]);
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password'     => ['different:current_password']
        ]);

        $Auth_User = Auth::user();
        $id = $Auth_User->id;

        User::find($id)->update(['password' => \Hash::make($request->new_password), 'updated_by' => $id]);

        Flash::success('Password updated successfully.');
        return redirect()->route($this->view_route, $id);
    }

    public function approve($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = User::find($user_id);
            if (empty($Model_Data) || $this->is_not_authorized($user_id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 1;
            $Model_Data->admin_approved = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            $this->send_welcome_email($Model_Data);

            Flash::success($this->msg_approved);
            $route = $this->get_redirect_route($Model_Data->user_type, 'view');
            return redirect()->route($route, $Model_Data->user_type);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function reject($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = User::find($user_id);
            if (empty($Model_Data) || $this->is_not_authorized($user_id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            $this->send_rejection_email($Model_Data);

            Flash::success($this->msg_rejected);
            $route = $this->get_redirect_route($Model_Data->user_type, 'view');
            return redirect()->route($route, $Model_Data->user_type);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function show_application($user_id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $id = $user_id;
            $Model_Data = $user = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            return view($this->views_path . '.show_application', compact("user", "Model_Data"));
        }
        else {
            Flash::error($this->view_permission_error_message);
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
        if ($Auth_User->id != $id && $id > 1 && ($Auth_User->can($this->status_permission) || $Auth_User->can('all'))) {
            $Model_Data = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_active);
            $route = $this->get_redirect_route($Model_Data->user_type, 'view');
            return redirect()->route($route, $Model_Data->user_type);
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
        if ($Auth_User->id != $id && $id > 1 && ($Auth_User->can($this->status_permission) || $Auth_User->can('all'))) {
            $Model_Data = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_inactive);
            $route = $this->get_redirect_route($Model_Data->user_type, 'view');
            return redirect()->route($route, $Model_Data->user_type);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function destroy($id, Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->id != $id && $id > 1 && ($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))) {
            $Model_Data = User::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->delete();

            Flash::success($this->msg_deleted);
            $route = $this->get_redirect_route($Model_Data->user_type, 'view');
            return redirect()->route($route, $Model_Data->user_type);
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function is_not_authorized($id, $Auth_User) {
        $user_type = $Auth_User->user_type;

        $bool = 1;
        if ($id == $Auth_User->id) {
            $bool = 0;
        }
        else {
            if ($user_type == 0) {
                $bool = 0;
            }
        }

        return $bool;
    }

}
