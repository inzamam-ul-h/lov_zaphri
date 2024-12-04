<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use Illuminate\Http\Request;
use App\Models\AuthKey;
use App\Models\User;
use App\Models\UserCalendar;
use App\Models\UserPersonal;
use App\Models\UserProfessional;

class ProfileApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $auth_key = (!empty($request->header('token'))) ? $request->header('token') : NULL;

        $this->_Token = $auth_key;
        $user_id = 0;

        if (empty($auth_key) || $auth_key == NULL) {
            return $this->sendError('Session is not active. Please Login again');
        }
        else {
            $Verifications = AuthKey::where('auth_key', $auth_key)->first();
            if (empty($Verifications)) {
                return $this->sendError('Session is not active. Please Login again');
            }
            else {
                $this->_User_Id = $user_id = $Verifications->user_id;

                if ($action == 'check') {
                    $User = User::find($user_id);

                    $data = $this->get_user_array($User);

                    return $this->sendResponse($data, 'Session is active');
                }
                elseif ($action == 'close') {
                    $this->expire_session();
                    return $this->sendSuccess('Session is expired Successfully.');
                }
            }
        }

        $page = 1;
        $limit = 10;
        (isset($request->page) ? $page = trim($request->page) : 1);
        (isset($request->limit) ? $limit = trim($request->limit) : 10);

        if (!empty($request->header('token'))) {
            $User = User::find($user_id);

            if (empty($User)) {
                $this->expire_session();
                return $this->sendError('User Not Found!');
            }
            elseif ($User->status == 0) {
                $this->expire_session();
                return $this->sendError('Your Account is Inactive/Suspended by Admin.');
            }
            elseif ($User->verified == 0) {
                $this->expire_session();
                return $this->sendError('Please verify your email first.');
            }
            elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                $this->expire_session();
                return $this->sendError('Approval pending from Admin.');
            }
            elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                $this->expire_session();
                return $this->sendError('Your Account is rejected by Admin.');
            }
            else {
                switch ($action) {


                    case 'requested_profile': {
                            return $this->requested_profile($request, $User);
                        }
                        break;

                    case 'edit_associated_email': {
                            return $this->edit_associated_email($request, $User);
                        }
                        break;

                    case 'send_code_again_associated_email': {
                            return $this->send_code_again_associated_email($request, $User);
                        }
                        break;

                    case 'send_code_again_associated_phone': {
                            return $this->send_code_again_associated_phone($request, $User);
                        }
                        break;

                    case 'verify_associated_email': {
                            return $this->verify_associated_email($request, $User);
                        }
                        break;

                    case 'verify_associated_phone': {
                            return $this->verify_associated_phone($request, $User);
                        }
                        break;

                    case 'edit_associated_phone_no': {
                            return $this->edit_associated_phone_no($request, $User);
                        }
                        break;

                    case 'edit_profile': {
                            return $this->edit_profile($request, $User);
                        }
                        break;

                    default: {
                            return $this->sendError('Invalid Request');
                        }
                        break;
                }
            }
        }
        else {
            return $this->sendError('You are not authorized.');
        }
    }

    private function verify_associated_phone(Request $request, $User) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '' && isset($request->verification_code) && ltrim(rtrim($request->verification_code)) != '') {
            $phone_no = str_replace("+", "", $request->phone_no);
            $phone_no = "+" . ltrim(rtrim($phone_no));

            $verification_code = $request->verification_code;

            $verified_token = "";

            $user = User::where('phone', $phone_no)->first();

            if ($user == null) {
                return $this->sendError("Phone Number is not registered.");
            }
            else {
                if ($user->phone_no_verified == 1) {
                    return $this->sendError("Phone Number Already Verified. Go to login to get into your account");
                }
                else {
                    $verified_token = $user->phone_verification_key;
                    if ($verified_token != $verification_code) {
                        return $this->sendError("Verification Code is incorrect.");
                    }
                    else {
                        $verified_token .= '-expired';

                        $user->status = 1;
                        $user->phone_no_verified = 1;
                        $user->phone_verification_key = $verified_token;
                        $user->save();

                        return $this->sendSuccess("Phone No. Verified Successfully");
                    }
                }
            }
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function send_code_again_associated_phone(Request $request, $User) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '') {

            // $phone_no = "+" . ltrim(rtrim($request->phone_no));
            $phone_no = str_replace("+", "", $request->phone_no);
            $phone_no = "+" . ltrim(rtrim($phone_no));

            $User = User::where('phone', $phone_no)->first();

            if ($User == null) {
                return $this->sendError("Phone Number is not registered.");
            }
            else {
                if ($User->phone_no_verified == 1) {
                    return $this->sendError("Phone Number Already Verified. Go to login to get into your account");
                }
                else {
                    $otp_response = $this->send_phone_otp($User);
                    if($otp_response){
                        $response = [
                            'responseCode'  => "201",
                            'responseState' => "success",
                            'responseText'  => "New Verification code is sent to your phone no."
                        ];
                        return response()->json($response, 200);
                    } else {
                        return $this->sendError('Error Sending SMS');
                    }
                }
            }
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function edit_associated_phone_no(Request $request, $User) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '' && isset($request->phone_prefix) && ltrim(rtrim($request->phone_prefix)) != '') {
            $phone_no = str_replace("+", "", $request->phone_no);
            $req_phone_no = ltrim(rtrim($phone_no));

            $phone_prefix = str_replace("+", "", $request->phone_prefix);
            $phone_prefix = "+" . ltrim(rtrim($phone_prefix));

            $phone_no = $phone_prefix . $req_phone_no;

            if ($User->phone_no_verified == 1) {
                return $this->sendError("Phone Number Already Verified. Go to login to get into your account");
            }
            $user = User::where('phone', $phone_no)->first();

            if ($user == null) {
                $User->phone = $phone_no;
                $User->phone_prefix = $phone_prefix;
                $User->save();
                
                $User = User::where('phone', $phone_no)->first();
                $otp_response = $this->send_phone_otp($User);
                if($otp_response){
                    $response = [
                        'responseCode'  => "201",
                        'responseState' => "success",
                        'responseText'  => "Verification code is sent to your phone no."
                    ];
                    return response()->json($response, 200);
                } else {
                    return $this->sendError('Error Sending SMS');
                }
            }
            else {
                return $this->sendError("Phone No. provided already Exists. Try another Phone No.");
            }
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function verify_associated_email(Request $request, $User) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->verification_code) && ltrim(rtrim($request->verification_code)) != '') {

            $email = ltrim(rtrim($request->email));

            $verification_code = $request->verification_code;

            $bool = 0;
            $verified_token = "";
            if ($User->email_verified == 1) {
                return $this->sendError("Email Already Verified. Go to login to get into your account");
            }

            $user = User::where('email', $email)->first();

            if ($user != null) {

                $verified_token = $user->email_verification_key;

                if ($verified_token == $verification_code) {

                    $verified_token .= '-expired';

                    $user->status = 1;
                    $user->email_verified = 1;
                    $user->email_verification_key = $verified_token;
                    $user->save();

                    return $this->sendSuccess("Email Verified Successfully");
                }
                else {
                    return $this->sendError("Verification Code is incorrect.");
                }
            }
            else {
                return $this->sendError("Email is not registered.");
            }
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function send_code_again_associated_email(Request $request, $User) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '') {

            $email = ltrim(rtrim($request->email));

            if ($User->email_verified == 1) {
                return $this->sendError("Email Already Verified. Go to login to get into your account");
            }
            $User = User::where('email', $email)->first();

            if ($User != null) {
                $User = User::find($User->id);
                
                $verified_token = random_number();
                $code = $this->get_email_otp($User->email);
                
                $User->verified_token = $verified_token;
                $User->email_verification_key = $code;
                $User->save();

                $this->send_verification_email($User);

                return $this->sendSuccess("New code is sent. Kindly Check your email.");
            }
            else {
                return $this->sendError("Email is not registered.");
            }
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function edit_associated_email(Request $request, $User) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '') {

            $email = $request->email;
            if ($User->email_verified == 1) {
                return $this->sendError("Email Already Verified. Go to login to get into your account");
            }

            $email_user = User::where('email', $email)->first();

            if ($email_user == null) {
                $User = User::find($User->id);
                $User->email = $email;
                
                $verified_token = random_number();
                $code = $this->get_email_otp($User->email);
                
                $User->verified_token = $verified_token;
                $User->email_verification_key = $code;
                $User->save();

                $this->send_verification_email($User);

                return $this->sendSuccess("Email is sent to provided email address with verification code.");
            }
            else {
                return $this->sendError("Email provided already Exists. Try another email");
            }
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function requested_profile(Request $request, $User) {
        if (isset($request->user_id) && ltrim(rtrim($request->user_id)) != '') {
            $user_id = $request->user_id;

            $user = User::find($user_id);
            if ($user == null) {
                return $this->sendError("No user found");
            }


            $personal_profile = $this->get_user_array($user, FALSE);

            $personal_profile["email"] = $user->email;
            $personal_profile["email_verified"] = $user->email_verified;

            $personal_profile["phone_no"] = $user->phone;
            $personal_profile["phone_prefix"] = $user->phone_prefix;
            $personal_profile["phone_no_verified"] = $user->phone_no_verified;

            $professional_profile = $this->professional_profile($user_id);

            $data = [
                'personal_profile'     => $personal_profile,
                'professional_profile' => $professional_profile,
            ];

            return $this->sendResponse($data, 'Successfully returned details');
        }
        else {
            return $this->sendError("Missing parameters");
        }
    }

    private function professional_profile($user_id) {
        $model = UserProfessional::where('user_id', $user_id)->first();

        $professional_profile = array();
        if ($model != null) {

            $professional_profile["org"] = $model->organizational_name;
            $professional_profile["agegroups"] = $model->agegroups;
            $professional_profile["experience"] = $model->experience;
            $clubName = '';
            if ($model->club_authentication == 1) {
                $clubName = get_user_name($model->club);
            }
            $professional_profile['club'] = $clubName;
        }
        else {
            $professional_profile["error"] = 'user not found';
        }
        return $professional_profile;
    }

    public function edit_profile(Request $request, $User) {
        $timestamp = date("Y-m-d H:i:s");
        $user_id = $User->id;
        $user_type = $User->user_type;

        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER || $user_type == $this->_CLUB_USER) { // for coach and player
            if (isset($request->user_id) && ltrim(rtrim($request->user_id)) != '') {
                $log_user_id = $user_id;
                $log_user_type = $user_type;
                $log_user_id = (int) $log_user_id;
                $user_id = $request->user_id;
                $user_id = (int) $user_id;

                $member_exist = 0;
                if ($user_type == $this->_CLUB_USER) {

                    $user = User::where('id', $user_id)
                            ->where('created_by', $log_user_id)
                            ->first();

                    if ($user != null) {
                        $member_exist = 1;
                        $user_type = $user->user_type;
                        $profile_status = $user->profile_status;
                    }

                    if ($member_exist != 1 || $profile_status == 1) {
                        return $this->sendError('You can not edit this user');
                    }
                }
                else {
                    return $this->sendError("You can not edit this user");
                }
            }

            $uploadsPath = $this->uploads_users . '/' . $user_id;
            $file_image = $old_file = "default_image";
            $profile = UserPersonal::where('user_id', $user_id)->first();
            if ($profile) {
                $old_file = $file_image = $profile->coachpic;
            }
            if ($request->hasFile('coachpicture')) {
                $file = $request->file('coachpicture');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_image = $fileName;

                if ($old_file != "" && $old_file != "default_image" && $old_file != 'user.png') {
                    $old_file_path = $uploadsPath . '/' . $old_file;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }
            $SITE_URL = env('APP_URL');
            $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/user.png";
            $uploadsPath = $SITE_URL . "/" . $this->uploads_users . '/' . $user_id;
            $photo = $defaultImage;
            if (!empty($file_image) && $file_image != 'default_image')
                $photo = $uploadsPath . "/" . $file_image;
            $photo_url = $file_image;

            /*if ($user_type == $this->_COACH_USER) { //for coach
                $public_url = $User->public_url;
                $url_status = $User->url_status;
                $url_exists = 0;
                if (isset($request->public_url) && $request->public_url != '') {
                    $public_url = createSlug(strtolower($request->public_url));

                    $urlExists = User::where('id', $user_id)
                            ->where('public_url', $public_url)
                            ->first();
                    if ($urlExists != null) {
                        $url_exists = 1;
                    }

                    if ($url_exists == 1) {
                        $public_url = $User->public_url;
                    }
                    else {
                        $url_status = 1;
                    }
                }
                User::where('id', $user_id)->update(['url_status' => $url_status, 'public_url' => $public_url]);
            }*/

            $settings = UserCalendar::where('user_id', $user_id)->first();
            $time_zone = ($settings->time_zone ?? NULL);
            if(isset($request->time_zone) && ltrim(rtrim($request->time_zone)) != ''){
                $time_zone = $request->time_zone;

                $exists = 0;
                if ($settings != null) {
                    $exists = 1;
                    UserCalendar::where('user_id', $user_id)->update(['time_zone' => $time_zone]);
                }
                if ($exists == 0) {
                    $userCalender = new UserCalendar();
                    $userCalender->user_id = $user_id;
                    $userCalender->time_zone = $time_zone;
                    $userCalender->save();
                }
            }

            if ($profile) {
                if ($user_type == $this->_PLAYER_USER && isset($request->dob)) {
                    $profile->dob = $request->dob;
                }

                if (isset($request->fname) && ltrim(rtrim($request->fname)) != '') {
                    $profile->first_name = $request->fname;
                }

                if (isset($request->lname) && ltrim(rtrim($request->lname)) != '') {
                    $profile->last_name = $request->lname;
                }

                if (isset($request->address) && ltrim(rtrim($request->address)) != '') {
                    $profile->address = $request->address;
                }

                if (isset($request->reg_no) && ltrim(rtrim($request->reg_no)) != '') {
                    $profile->reg_no = $request->reg_no;
                }

                if (isset($request->contact_person) && ltrim(rtrim($request->contact_person)) != '') {
                    $profile->contact_person = $request->contact_person;
                }

                if (isset($request->gender) && ltrim(rtrim($request->gender)) != '') {
                    $profile->gender = $request->gender;
                }

                if (isset($request->meetinglink) && ltrim(rtrim($request->meetinglink)) != '') {
                    $profile->meetinglink = $request->meetinglink;
                }

                if (isset($request->zipcode) && ltrim(rtrim($request->zipcode)) != '') {
                    $profile->zip_code = $request->zipcode;
                }

                if (isset($request->aboutme) && ltrim(rtrim($request->aboutme)) != '') {
                    $profile->about_me = $request->aboutme;
                }
                $profile->coachpic = $file_image;
                $profile->modified = 1;
                $profile->updated_by = $user_id;
                $profile->updated_at = $timestamp;
                $profile->save();

                $User_Profile = User::find($user_id);
                    $User_Profile->name = trim($profile->first_name . ' ' . $profile->last_name);
                    $User_Profile->photo_url = trim($photo_url);
                    $User_Profile->photo = trim($photo);
                    $User_Profile->updated_by = $user_id;
                $User_Profile->save();
            }

            if (!empty($request->org) || !empty($request->agegroups) || !empty($request->experience)) {
                $UserProfessional = UserProfessional::where('user_id', $user_id)->first();
                    if (isset($request->org) && ltrim(rtrim($request->org)) != '') {
                        $UserProfessional->organizational_name = $request->org;
                    }
                    if (isset($request->agegroups) && ltrim(rtrim($request->agegroups)) != '') {
                        $UserProfessional->agegroups = $request->agegroups;
                    }
                    if (isset($request->experience) && ltrim(rtrim($request->experience)) != '') {
                        $UserProfessional->experience = $request->experience;
                    }
                    $UserProfessional->updated_by = $user_id;
                $UserProfessional->save();
            }
            

            if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) { // for coach and player
                $UserProfessional = UserProfessional::where('user_id', $user_id)->first();
                
                if (!empty($profile->first_name) && !empty($profile->last_name) && !empty($profile->about_me) && !empty($profile->zip_code) && !empty($profile->gender) && !empty($time_zone) && !empty($UserProfessional->organizational_name) && !empty($UserProfessional->agegroups) && !empty($UserProfessional->experience)){
                    User::where('id', $user_id)->update(['profile_status' => 1]);
                }
            }
            elseif ($user_type == $this->_CLUB_USER) { // for club
                if (!empty($profile->first_name) && !empty($profile->last_name) && !empty($profile->reg_no) && !empty($profile->contact_person) && !empty($profile->address)) {
                    User::where('id', $user_id)->update(['profile_status' => 1]);
                }
            }

            $user = User::find($user_id);
            $personal_profile = $this->get_user_array($user, FALSE);

            $personal_profile["email"] = $user->email;
            $personal_profile["email_verified"] = $user->email_verified;

            $personal_profile["phone_no"] = $user->phone;
            $personal_profile["phone_prefix"] = $user->phone_prefix;
            $personal_profile["phone_no_verified"] = $user->phone_no_verified;

            $professional_profile = $this->professional_profile($user_id);

            $data = [
                'personal_profile'     => $personal_profile,
                'professional_profile' => $professional_profile,
            ];

            return $this->sendResponse($data, 'Successfully Updated Profile Data');
        }
        else {
            return $this->sendError("Wrong User Type");
        }
    }

}
