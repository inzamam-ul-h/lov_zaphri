<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
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
use App\Models\UserCalendar;
use App\Models\UserPersonal;
use App\Models\UserProfessional;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateUserStatusRequest;
use App\Http\Requests\UpdateUserNotificationRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\ArchivedUser;
use App\Models\BlockedUser;
use App\Models\Group;
use App\Models\UserDevice;
use App\Repositories\UserDeviceRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\JsonResponse;

class UserApiController extends BaseController
{

    /** @var UserRepository */
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request, $action = 'listing')
    {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            if ($action == 'check') {
                $fcm_token = (isset($request->fcm_token)) ? $request->fcm_token : NULL;
                if ($fcm_token !== NULL) {
                    $User->fcm_token = $fcm_token;
                    $User->save();
                    $User = User::find($user_id);
                }
                $data = $this->get_user_array($User);

                return $this->sendResponse($data, 'Session is active');
            }
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'create_user',
                'change_password',
                'update-user',
                'upload-photo',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            switch ($action) {
                case 'create_user': {
                        return $this->create_new_user($request, $User);
                    }
                    break;

                case 'change_password': {
                        return $this->change_password($request, $User);
                    }
                    break;

                case 'get_users': {
                        return $this->get_users($request, $User);
                    }
                    break;

                case 'update-user': {
                        return $this->updateUser($request, $User);
                    }
                    break;

                case 'upload-photo': {
                        return $this->uploadPhoto($request, $User);
                    }
                    break;

                default: {
                        return $this->sendError('Invalid Request');
                    }
                    break;
            }
        } elseif ($action == 'close') {
            $this->expire_session();
            return $this->sendSuccess('Session is expired Successfully.');
        } else {
            return $this->sendError($result['message']);
        }
    }

    private function get_users(Request $request, $User)
    {
        if (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) {
            $page_no = $request->page_no;
        } else {
            $page_no = 1;
        }

        if (isset($request->limit) && $request->limit != "" && $request->limit != 0) {
            $limit = $request->limit;
        } else {
            $limit = 5;
        }

        $offset = ($page_no - 1) * $limit;

        $users_array = array();
        $user_type = $request->user_type;
        $profile_status = $request->profile_status;

        $users = User::select('*');
        if ($user_type == $this->_COACH_USER) {
            $users = $users->where('user_type', '1');
        } elseif ($user_type == $this->_PLAYER_USER) {
            $users = $users->where('user_type', '2');
        } elseif ($user_type == $this->_CLUB_USER) {
            $users = $users->where('user_type', '3')
                ->where('admin_approved', '1');
        } elseif ($user_type == $this->_PARENT_USER) {
            $users = $users->where('user_type', '4');
        }

        if ($profile_status == 1) {
            $users = $users->where('profile_status', '1');
        }
        $total_records = $users->count();
        $total_no_of_pages = ceil($total_records / $limit);

        $users = $users->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $current_count = $users->count();

        if ($current_count < 1) {
            return $this->sendError("No users found");
        }

        foreach ($users as $user) {
            $user_array = array();
            $users_array[] = $this->get_user_array($user, FALSE);
        }


        $data = [
            'page_no'           => $page_no,
            'limit'             => $limit,
            'total_records'     => $total_records,
            'current_count'     => $current_count,
            'total_no_of_pages' => $total_no_of_pages,
            'users'             => $users_array,
        ];

        return $this->sendResponse($data, 'Successfully returned details');
    }

    private function create_new_user(Request $request, $User)
    {
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_CLUB_USER || $user_type == $this->_PARENT_USER) {
            if (
                isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->password) && ltrim(rtrim($request->password)) != '' && isset($request->user_type) && ltrim(rtrim($request->user_type)) != ''
            ) {
                $bool = 0;

                $created_by = $user_id;
                $log_user_type = $user_type;

                $user_type = test_input($request->user_type);
                $email = test_input($request->email);
                $l_email = $email = strtolower($email);
                $pass = test_input($request->password);

                if (($log_user_type == 0 || $log_user_type == 3 || $log_user_type == 4) && ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER)) {
                    $User = User::where('email', $email)->first();
                    if (!empty($User)) {
                        return $this->sendError('Email Already exists: Registration fails');
                    } else {
                        $user_id = $this->create_user($user_type, $pass, 'email', $email, NULL, $created_by, $log_user_type);

                        $User = User::find($user_id);
                        if (!empty($User)) {
                            $data = $this->get_user_array($User, FALSE);
                            return $this->sendResponse($data, 'Registered Successfully');
                        } else {
                            return $this->sendError('Registration fails, Please Try Again');
                        }
                    }
                } else {
                    return $this->sendError("You are not allowed to created users");
                }
            } else {
                return $this->sendError("Missing Parameters");
            }
        } else {
            return $this->sendError("Incorrect User Type");
        }
    }

    public function change_password(Request $request, $User)
    {
        if (isset($request->old_password) && ltrim(rtrim($request->old_password)) != '' && isset($request->new_password) && ltrim(rtrim($request->new_password)) != '' && isset($request->confirm_password) && ltrim(rtrim($request->confirm_password)) != '') {
            $new_password = $request->new_password;
            $confirm_password = $request->confirm_password;

            if ($new_password == $confirm_password) {
                $user_id = $User->id;
                $old_password = ($request->old_password);
                $res = User::where('id', $user_id)->where('password', $old_password)->first();

                if (Hash::check($old_password, $User->password)) {
                    $User = User::find($user_id);
                    $User->password = bcrypt($new_password);
                    $User->save();

                    return $this->sendSuccess('Password Changed Successfully');
                } else {
                    return $this->sendError('Invalid current password');
                }
            } else {
                return $this->sendError("Passwords doesn't match");
            }
        } else {
            return $this->sendError('Missing Parameters');
        }
    }

    public function updateUser(Request $request, $User)
    {
        $photo = $User->photo;
        $photo_type = $User->photo_type;

        $request->validate([
            'fileName' => '|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $User->name = trim($request->name) ?? $User->name;
        $User->email = trim($request->email) ?? $User->email;
        if (isset($request->photo) && !empty($request->photo)) {
            $photo = trim($request->photo);
            $photo_type = 1;
        } elseif (isset($request->fileName) && $request->fileName != NULL) {
            $allowedfileExtension = ['jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG'];
            $check = 1; //in_array($extension,$allowedfileExtension);

            if ($check) {
                $uploadsPath = $this->uploads_users . '/' . $User->id;
                $file = $request->file('fileName');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $photo = $fileName;
                $photo_type = 0;
            } else {
                return $this->sendError('Invalid image format provided. Please upload image file (jpeg,jpg,png)');
            }
        }

        $User->photo = $photo;
        $User->photo_type = $photo_type;

        $User->save();

        $array = get_user_array($User);

        $response = [
            'code'    => '201',
            'status'  => TRUE,
            'data'    => [
                'user' => $array
            ],
            'message' => 'User updated, successfully!',
        ];
        return response()->json($response, 200);
    }

    public function uploadPhoto(Request $request, $User)
    {

        if (!$request->hasFile('fileName')) {
            return $this->sendError('Please upload profile image');
        }

        $photo = $User->photo;
        $photo_type = $User->photo_type;

        if (isset($request->fileName) && $request->fileName != NULL) {
            $allowedfileExtension = ['jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG'];
            $ext = $request->fileName->getClientOriginalExtension();
            $check = 0;
            foreach ($allowedfileExtension as $allow) {
                if ($allow == $ext) {
                    $check = 1; //in_array($extension,$allowedfileExtension);
                }
            }
            if ($check == 1) {
                $uploadsPath = $this->uploads_users . '/' . $User->id;
                $file = $request->file('fileName');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $photo = $fileName;
                $photo_type = 0;
            } else {
                return $this->sendError('Invalid image format provided. Please upload image file (jpeg,jpg,png)');
            }
        }

        $User->photo = trim($photo);
        $User->photo_type = $photo_type;

        $User->save();

        $array = get_user_array($User);

        $response = [
            'code'    => '201',
            'status'  => TRUE,
            'data'    => [
                'user' => $array
            ],
            'message' => 'Profile Image uploaded successfully!',
        ];
        return response()->json($response, 200);
    }
}
