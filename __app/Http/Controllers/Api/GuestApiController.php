<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\AuthKey;
use App\Models\User;

class GuestApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $this->_Token = (!empty($request->header('token'))) ? $request->header('token') : NULL;

        $token = $this->_Token;
        $user_id = 0;

        if (!empty($token)) {
            $Verifications = AuthKey::where('auth_key', $token)->first();
            if (!empty($Verifications)) {
                $this->_User_Id = $user_id = $Verifications->user_id;
                $token = $token . '-Expired';

                DB::table('auth_keys')->where('user_id', $user_id)->update([
                    'token' => $token
                ]);
            }
        }

        switch ($action) {
            case 'signup_email': {
                    return $this->signup_email($request);
                }
                break;

            case 'verify_email': {
                    return $this->verify_email($request);
                }
                break;

            case 'send_code_again_email': {
                    return $this->send_code_again_email($request);
                }
                break;

            case 'forgot_password_email': {
                    return $this->forgot_password_email($request);
                }
                break;

            case 'signin_email': {
                    return $this->signin_email($request);
                }
                break;

            case 'signup_phone': {
                    return $this->signup_phone($request);
                }
                break;

            case 'verify_phone': {
                    return $this->verify_phone($request);
                }
                break;

            case 'send_code_again_phone': {
                    return $this->send_code_again_phone($request);
                }
                break;

            case 'forgot_password_phone': {
                    return $this->forgot_password_phone($request);
                }
                break;

            case 'forgot_password_phone_verify': {
                    return $this->forgot_password_phone_verify($request);
                }
                break;

            case 'signin_phone': {
                    return $this->signin_phone($request);
                }
                break;

            default: {
                    return $this->sendError('Invalid Request');
                }
                break;
        }
    }

    private function signup_email(Request $request) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->password) && ltrim(rtrim($request->password)) != '' && isset($request->user_type) && ltrim(rtrim($request->user_type)) != '') {
            $email = strtolower(test_input($request->email));

            $User = User::where('email', $email)->first();
            if (!empty($User)) {
                return $this->sendError('Email Already exists: Registration fails');
            }
            else {
                $user_type = test_input($request->user_type);
                $pass = test_input($request->password);

                $user_id = $this->create_user($user_type, $pass, 'email', $email);

                $User = User::find($user_id);
                if (!empty($User)) {
                    $data = $this->get_user_array($User, FALSE);
                    return $this->sendResponse($data, 'Registered Successfully');
                }
                else {
                    return $this->sendError('Registration fails, Please Try Again');
                }
            }
        }
        elseif (!isset($request->email) || ltrim(rtrim($request->email)) == '') {
            return $this->sendError('Missing Email address in request: Registration fails');
        }
        elseif (!isset($request->password) || ltrim(rtrim($request->password)) == '') {
            return $this->sendError('Missing Password in request: Registration fails');
        }
    }

    private function verify_email(Request $request) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->verification_code) && ltrim(rtrim($request->verification_code)) != '') {
            $email = strtolower(ltrim(rtrim($request->email)));

            $User = User::where('email', $email)->first();
            if (empty($User)) {
                return $this->sendError('Email is not registered.');
            }
            else {
                if ($User->verified == '1' && $User->email_no_verified == '1') {
                    return $this->sendError('Email Already Verified. Go to login to get into your account');
                }
                else {
                    $verified_token = $User->email_verification_key;
                    $verification_code = $request->verification_code;

                    if ($verified_token != $verification_code) {
                        return $this->sendError('Verification Code is incorrect.');
                    }
                    else {
                        $user_id = $User->id;

                        $verified_token .= '-expired';

                        $User = User::find($user_id);
                        $User->status = 1;
                        $User->verified = 1;
                        $User->email_verified = 1;
                        $User->email_verification_key = $verified_token;
                        $User->save();

                        $User = User::find($user_id);

                        if ($User->status == 0) {
                            return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                        }
                        elseif ($User->verified == 0) {
                            return $this->sendError('Please verify your email first.', 105);
                        }
                        elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                            return $this->sendError('Email Verified Successfully. Approval pending from Admin.');
                        }
                        elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                            return $this->sendError('Your Account is rejected by Admin.');
                        }
                        else {
                            if ($User->user_type != $this->_CLUB_USER) {
                                $this->send_welcome_email($User);
                            }
                            $data = $this->get_user_array($User, TRUE);

                            return $this->sendResponse($data, 'Email Verified Successfully');
                        }
                    }
                }
            }
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

    private function send_code_again_email(Request $request) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '') {
            $email = strtolower(ltrim(rtrim($request->email)));

            $User = User::where('email', $email)->first();
            if (empty($User)) {
                return $this->sendError('Email is not registered.');
            }
            else {
                $user_id = $User->id;
                if ($User->verified == '1' && $User->email_verified == '1') {
                    return $this->sendError('Email Already Verified. Try another Email');
                }
                else {
                    $User = User::find($user_id);
                    
                    $verified_token = random_number();
                    $code = $this->get_email_otp($User->email);
                    
                    $User->verified_token = $verified_token;
                    $User->email_verification_key = $code;
                    $User->save();

                    $this->send_verification_email($User);

                    return $this->sendSuccess('New code is sent. Kindly Check your email.');
                }
            }
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

    private function forgot_password_email(Request $request) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '') {
            $email = strtolower(ltrim(rtrim($request->email)));

            $User = User::where('email', $email)->first();
            if (empty($User)) {
                return $this->sendError('Invalid Email Address provided: Authentication fails');
            }
            else {
                if ($User->status == 0) {
                    return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                }
                elseif ($User->verified == 0) {
                    return $this->sendError('Please verify your email first.', 105);
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    return $this->sendError('Approval pending from Admin.');
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                    return $this->sendError('Your Account is rejected by Admin.');
                }
                else {
                    $user_id = $User->id;
                    $email = $User->email;

                    $reset_pass_token = random_number();

                    $User = User::find($user_id);
                    $User->reset_pass_token = $reset_pass_token;
                    $User->save();

                    $this->send_forget_password_email($User);

                    return $this->sendSuccess('An Email has been sent to your email address. Please check your Email.');
                }
            }
        }
        else {
            return $this->sendError('Missing Email for Authentication');
        }
    }

    private function signin_email(Request $request) {
        if (isset($request->email) && ltrim(rtrim($request->email)) != '' && isset($request->password) && ltrim(rtrim($request->password)) != '') {
            $email = strtolower(ltrim(rtrim($request->email)));
            $type_pass = ltrim(rtrim($request->password));

            $User = User::where('email', '=', $email)->first();
            if (!$User) {
                return $this->sendError('Login Failed, Incorrect Credentials Provided!');
            }
            if (!Hash::check($type_pass, $User->password)) {
                return $this->sendError('Login Failed, Incorrect Credentials Provided!');
            }
            if (empty($User)) {
                return $this->sendError('Invalid Login Credentials provided: Authentication fails');
            }
            else {
                if ($User->status == 0) {
                    return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                }
                elseif ($User->verified == 0) {
                    return $this->sendError('Please verify your email first.', 105);
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    return $this->sendError('Approval pending from Admin.');
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                    return $this->sendError('Your Account is rejected by Admin.');
                }
                else {
                    //$this->expire_session($User->id);
                    $user_type = $User->user_type;

                    if ($user_type == $this->_COACH_USER) {
                        //$last_session_price = 0;//get_last_session_price($user_id);
                        //$data['last_session_price'] = $last_session_price;
                    }
                    $data = $this->get_user_array($User, TRUE);

                    return $this->sendResponse($data, 'Authenticated Successfully');
                }
            }
        }
        else {
            return $this->sendError('Missing Login Credentials for Authentication');
        }
    }

    private function signup_phone(Request $request) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '' && isset($request->password) && ltrim(rtrim($request->password)) != '' && isset($request->user_type) && ltrim(rtrim($request->user_type)) != '') {
            
            $phone_prefix = "+" . str_replace("+", "", ltrim(rtrim($request->phone_prefix)));
            $phone_no = $phone_prefix . "" . str_replace("+", "", strtolower($request->phone_no));

            $User = User::where('phone', $phone_no)->first();
            if (!empty($User) || $User != NULL) {
                return $this->sendError('Phone Number Already Exists');
            }
            else {
                $user_type = $request->user_type;
                $pass = $request->password;

                $user_id = $this->create_user($user_type, $pass, 'phone', $phone_no, $phone_prefix);

                $User = User::find($user_id);
                if (!empty($User)) {                    
                    $otp_response = $this->send_phone_otp($User);
                    $message = "";
                    if($otp_response){
                        $message = "Registered Successfully. A Verification code is sent to your phone no.";
                    } else {
                        $message = "Registered Successfully. A Verification code is sent to your phone no.";
                    }

                    $data = $this->get_user_array($User, FALSE);

                    return $this->sendResponse($data, $message);
                }
                else {
                    return $this->sendError('Registration fails, Please Try Again');
                }
            }
        }
        elseif (!isset($request->phone_no) || ltrim(rtrim($request->phone_no)) == '') {
            return $this->sendError('Missing Phone No in request: Registration fails');
        }
        elseif (!isset($request->password) || ltrim(rtrim($request->password)) == '') {
            return $this->sendError('Missing Password in request: Registration fails');
        }
    }

    private function verify_phone(Request $request) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '' && isset($request->verification_code) && ltrim(rtrim($request->verification_code)) != '') {
            $phone_no = "+" . str_replace("+", "", ltrim(rtrim($request->phone_no)));

            $User = User::where('phone', $phone_no)->first();
            if (empty($User)) {
                return $this->sendError("Phone Number ($phone_no) is not registered.");
            }
            else {
                if ($User->verified == '1' && $User->phone_no_verified == '1') {
                    return $this->sendError('Phone Number Already Verified. Go to login to get into your account');
                }
                else {
                    $verification_code = $request->verification_code;
                    $verified_token = $User->phone_verification_key;
                    if ($verified_token != $verification_code) {
                        return $this->sendError('Verification Code is incorrect.');
                    }
                    else {
                        $user_id = $User->id;
                        $verified_token .= '-expired';

                        $User = User::find($user_id);
                        $User->status = 1;
                        $User->verified = 1;
                        $User->phone_no_verified = 1;
                        $User->phone_verification_key = $verified_token;
                        $User->save();

                        $User = User::find($user_id);

                        if ($User->status == 0) {
                            return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                        }
                        elseif ($User->verified == 0) {
                            return $this->sendError('Please verify your Phone No. first.', 105);
                        }
                        elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                            return $this->sendError('Phone Number Verified Successfully. Approval pending from Admin.');
                        }
                        elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                            return $this->sendError('Your Account is rejected by Admin.');
                        }
                        else {
                            $data = $this->get_user_array($User, TRUE);
                            return $this->sendResponse($data, 'Phone No. Verified Successfully');
                        }
                    }
                }
            }
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

    private function send_code_again_phone(Request $request) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '') {
            $phone_no = "+" . str_replace("+", "", ltrim(rtrim($request->phone_no)));

            $User = User::where('phone', $phone_no)->first();
            if (empty($User)) {
                return $this->sendError("Phone Number ($phone_no) is not registered.");
            }
            else {
                if ($User->verified == '1' && $User->phone_no_verified == '1') {
                    return $this->sendError('Phone Number Already Verified. Go to login to get into your account');
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
            return $this->sendError('Missing Parameters');
        }
    }

    private function forgot_password_phone(Request $request) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '') {
            $phone_no = "+" . str_replace("+", "", ltrim(rtrim($request->phone_no)));

            $User = User::where('phone', $phone_no)->first();
            if (empty($User)) {
                return $this->sendError("Phone Number ($phone_no) is not registered.");
            }
            else {
                if ($User->status == 0) {
                    return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                }
                elseif ($User->verified == 0) {
                    return $this->sendError('Please verify your Phone No. first.', 105);
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    return $this->sendError('Phone Number Verified Successfully. Approval pending from Admin.');
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                    return $this->sendError('Your Account is rejected by Admin.');
                }
                else {
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
            }
        }
        else {
            return $this->sendError('Missing Phone Number for Authentication');
        }
    }

    private function forgot_password_phone_verify(Request $request) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '' && isset($request->reset_verification_code) && ltrim(rtrim($request->reset_verification_code)) != '' && isset($request->new_password) && ltrim(rtrim($request->new_password)) != '' && isset($request->confirm_password) && ltrim(rtrim($request->confirm_password)) != '') {
            $phone_no = "+" . str_replace("+", "", ltrim(rtrim($request->phone_no)));

            $User = User::where('phone', $phone_no)->first();
            if (empty($User)) {
                return $this->sendError("Phone Number ($phone_no) is not registered.");
            }
            else {
                if ($User->status == 0) {
                    return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                }
                elseif ($User->verified == 0) {
                    return $this->sendError('Please verify your Phone No. first.', 105);
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    return $this->sendError('Phone Number Verified Successfully. Approval pending from Admin.');
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                    return $this->sendError('Your Account is rejected by Admin.');
                }
                else {
                    $password = $request->new_password;
                    $cpassword = $request->confirm_password;
                    if ($password != $cpassword) {
                        return $this->sendError('Passwords Do Not Match');
                    }
                    else {
                        $reset_verification_code = $request->reset_verification_code;
                        $code = $User->reset_pass_token;

                        if ($code != $reset_verification_code) {
                            return $this->sendError('Incorrect Verification Code.');
                        }
                        else {
                            $user_id = $User->id;
                            $reset_pass_token = random_number();

                            $User = User::find($user_id);
                            $User->password = $password;
                            $User->reset_pass_token = $reset_pass_token;
                            $User->save();

                            $response = [
                                'responseCode'  => "201",
                                'responseState' => "success",
                                'responseText'  => "Password Reset Successfully."
                            ];
                            return response()->json($response, 200);
                        }
                    }
                }
            }
        }
        else {
            return $this->sendError('Missing Parameters');
        }
    }

    private function signin_phone(Request $request) {
        if (isset($request->phone_no) && ltrim(rtrim($request->phone_no)) != '' && isset($request->password) && ltrim(rtrim($request->password)) != '') {
            $phone_no = "+" . str_replace("+", "", ltrim(rtrim($request->phone_no)));

            $type_pass = ltrim(rtrim($request->password));

            $User = User::where('phone', '=', $phone_no)->first();
            if (!$User) {
                return $this->sendError('Login Failed, Incorrect Credentials Provided!');
            }
            if (!Hash::check($type_pass, $User->password)) {
                return $this->sendError('Login Failed, Incorrect Credentials Provided!');
            }
            if (empty($User)) {
                return $this->sendError('Invalid Login Credentials provided: Authentication fails');
            }
            else {
                if ($User->status == 0) {
                    return $this->sendError('Your Account is Inactive/Suspended by Admin.');
                }
                elseif ($User->verified == 0) {
                    return $this->sendError('Please verify your phone first.', 105);
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    return $this->sendError('Approval pending from Admin.');
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                    return $this->sendError('Your Account is rejected by Admin.');
                }
                else {
                    //$this->expire_session($User->id);
                    $user_type = $User->user_type;

                    if ($user_type == $this->_COACH_USER) {
                        //$last_session_price = 0;//get_last_session_price($user_id);
                        //$data['last_session_price'] = $last_session_price;
                    }
                    $data = $this->get_user_array($User, TRUE);

                    return $this->sendResponse($data, 'Authenticated Successfully');
                }
            }
        }
        else {
            return $this->sendError('Missing Login Credentials for Authentication');
        }
    }

}
