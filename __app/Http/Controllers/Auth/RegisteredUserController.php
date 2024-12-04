<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MainController as MainController;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class RegisteredUserController extends MainController {

    public function storeEmail(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email'     => 'required|string|email|min:6|max:255|unique:users',
                    'user_type' => 'required',
                    'password'  => ['required', Password::min(6)->mixedCase()->numbers()]
        ]);

        if ($validator->passes()) {
            $user_type = $request->user_type;
            $email = $request->email;
            $pass = $request->password;

            $user_id = $this->create_user($user_type, $pass, 'email', $email);

            return response()->json(['status' => true, 'messages' => 'User Account created successfully. Please check your email to activate your account.']);
        }

        return response()->json(['status' => false, 'messages' => $validator->errors()->all()]);
    }

    public function verifyCodeEmail(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email'             => 'required|string|email|min:6|max:255',
                    'verification_code' => 'required'
        ]);

        if ($validator->passes()) {
            $email = $request->email;
            $response = $this->validate_email_address($email);
            if ($response['status'] === TRUE && isset($response['User'])) {
                $User = $response['User'];
                $verification_code = $request->verification_code;
                $verified_token = $User->email_verification_key;
                if ($verified_token != $verification_code) {
                    return response()->json(['status' => false, 'messages' => 'Verification Code is incorrect.']);
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

                    if ($User->user_type != $this->_CLUB_USER) {
                        $this->send_welcome_email($User);
                    }

                    return response()->json(['status' => true, 'messages' => 'Email Verified Successfully']);
                }
            }
            elseif ($response['status'] === TRUE) {
                return response()->json(['status' => true, 'messages' => $response['message']]);
            }
            else {
                return response()->json(['status' => false, 'messages' => $response['message']]);
            }
        }

        return response()->json(['status' => false, 'messages' => $validator->errors()->all()]);
    }

    public function resendCodeEmail(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required|string|email|min:6|max:255'
        ]);

        if ($validator->passes()) {
            $email = $request->email;
            $response = $this->validate_email_address($email);
            if ($response['status'] === TRUE && isset($response['User'])) {
                $User = $response['User'];
                {
                    $user_id = $User->id;

                    $response = $this->send_code_email($user_id);
                    if ($response['status'] === TRUE) {
                        return response()->json(['status' => true, 'messages' => $response['message']]);
                    }
                    else {
                        return response()->json(['status' => false, 'messages' => $response['message']]);
                    }
                }
            }
            elseif ($response['status'] === TRUE) {
                return response()->json(['status' => true, 'messages' => $response['message']]);
            }
            else {
                return response()->json(['status' => false, 'messages' => $response['message']]);
            }
        }

        return response()->json(['status' => false, 'messages' => $validator->errors()->all()]);
    }

    private function validate_email_address($email) {
        $response = array();
        $User = User::where('email', $email)->first();
        if (empty($User)) {
            $response['status'] = FALSE;
            $response['message'] = "Email Address ($email) is not registered.";
        }
        else {
            if ($User->verified == '1' && $User->email_verified == '1') {
                $response['status'] = TRUE;
                $response['message'] = "Email Already Verified. Go to login to get into your account";
            }
            else {
                $User = User::find($User->id);

                if ($User->status == 0) {
                    $response['status'] = FALSE;
                    $response['message'] = "Your Account is Inactive/Suspended by Admin.";
                }
                elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                    $response['status'] = FALSE;
                    $response['message'] = "Email Address verified Successfully. Approval pending from Admin.";
                }
                /* elseif ($User->user_type == 3 && $User->admin_approved == 2)
                  {
                  $response['status'] = FALSE;
                  $response['message'] = "Your Account is rejected by Admin.";
                  } */
                else {
                    $response['status'] = TRUE;
                    $response['message'] = "Email Address is valid";
                    $response['User'] = $User;
                }
            }
        }
        return $response;
    }

    private function send_code_email($user_id) {
        $User = User::find($user_id);

        $verified_token = random_number();
        $code = $this->get_email_otp($User->email);

        $User->verified_token = $verified_token;
        $User->email_verification_key = $code;
        $User->save();

        $response = array();
        try {
            $this->send_verification_email($User);

            $response['status'] = TRUE;
            $response['message'] = "Verification code is sent to your Email Address.";
        }
        catch (\Throwable $th) {
            $response['status'] = false;
            $response['message'] = "Error Sending Email";
        }

        return $response;
    }

    public function storePhone(Request $request) {
        $validator = Validator::make($request->all(), [
                    'phone'           => 'required|min:6|max:255|unique:users',
                    'phone_no_prefix' => 'required',
                    'user_type'       => 'required',
                    'password'        => ['required', Password::min(6)->mixedCase()->numbers()]
        ]);

        if ($validator->passes()) {
            $phone_prefix = str_replace("+", "", $request->phone_no_prefix);
            $phone_prefix = "+" . ltrim(rtrim($phone_prefix));

            $phone_no = strtolower($request->phone);
            $phone_no = $phone_prefix . "" . $phone_no;

            $user_type = $request->user_type;
            $pass = $request->password;

            $user_id = $this->create_user($user_type, $pass, 'phone', $phone_no, $phone_prefix);

            $response = $this->send_code_phone_number($user_id);
            /* if($response['status'] === TRUE){
              return response()->json(['status' => true, 'messages' => $response['message']]);
              } else {
              return response()->json(['status' => false, 'messages' => $response['message']]);
              } */

            return response()->json(['status' => true, 'messages' => 'User Account created successfully. Please check your phone to activate your account.']);
        }

        return response()->json(['status' => false, 'messages' => $validator->errors()->all()]);
    }

    public function resendCodePhone(Request $request) {
        $validator = Validator::make($request->all(), [
                    'phone'           => 'required|min:6|max:255',
                    'phone_no_prefix' => 'required'
        ]);

        if ($validator->passes()) {
            $phone_prefix = str_replace("+", "", $request->phone_no_prefix);
            $phone_prefix = "+" . ltrim(rtrim($phone_prefix));

            $phone_no = strtolower($request->phone);
            $phone_no = $phone_prefix . "" . $phone_no;

            $response = $this->validate_phone_number($phone_no);
            if ($response['status'] === TRUE && isset($response['User'])) {
                $User = $response['User'];
                {
                    $user_id = $User->id;

                    $response = $this->send_code_phone_number($user_id);
                    if ($response['status'] === TRUE) {
                        return response()->json(['status' => true, 'messages' => $response['message']]);
                    }
                    else {
                        return response()->json(['status' => false, 'messages' => $response['message']]);
                    }
                }
            }
            elseif ($response['status'] === TRUE) {
                return response()->json(['status' => true, 'messages' => $response['message']]);
            }
            else {
                return response()->json(['status' => false, 'messages' => $response['message']]);
            }
        }

        return response()->json(['status' => false, 'messages' => $validator->errors()->all()]);
    }

    public function verifyCodePhone(Request $request) {
        $validator = Validator::make($request->all(), [
                    'phone'             => 'required|min:6|max:255',
                    'phone_no_prefix'   => 'required',
                    'verification_code' => 'required'
        ]);

        if ($validator->passes()) {
            $phone_prefix = str_replace("+", "", $request->phone_no_prefix);
            $phone_prefix = "+" . ltrim(rtrim($phone_prefix));

            $phone_no = strtolower($request->phone);
            $phone_no = $phone_prefix . "" . $phone_no;

            $response = $this->validate_phone_number($phone_no);
            if ($response['status'] === TRUE && isset($response['User'])) {
                $User = $response['User'];
                $verification_code = $request->verification_code;
                $verified_token = $User->phone_verification_key;
                if ($verified_token != $verification_code) {
                    return response()->json(['status' => false, 'messages' => 'Verification Code is incorrect.']);
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

                    return response()->json(['status' => true, 'messages' => 'Phone No. Verified Successfully']);
                }
            }
            elseif ($response['status'] === TRUE) {
                return response()->json(['status' => true, 'messages' => $response['message']]);
            }
            else {
                return response()->json(['status' => false, 'messages' => $response['message']]);
            }
        }

        return response()->json(['status' => false, 'messages' => $validator->errors()->all()]);
    }

    private function validate_phone_number($phone_no) {
        $response = array();
        $User = User::where('phone', $phone_no)->first();
        if (empty($User)) {
            $response['status'] = FALSE;
            $response['message'] = "Phone Number ($phone_no) is not registered.";
        }
        else {
            if ($User->verified == '1' && $User->phone_no_verified == '1') {
                $response['status'] = TRUE;
                $response['message'] = "Phone Number Already Verified. Go to login to get into your account";
            }
            else {
                $User = User::find($User->id);

                if ($User->status == 0) {
                    $response['status'] = FALSE;
                    $response['message'] = "Your Account is Inactive/Suspended by Admin.";
                }
                /* elseif ($User->user_type == 3 && $User->admin_approved == 0)
                  {
                  $response['status'] = FALSE;
                  $response['message'] = "Phone Number Verified Successfully. Approval pending from Admin.";
                  }
                  elseif ($User->user_type == 3 && $User->admin_approved == 2)
                  {
                  $response['status'] = FALSE;
                  $response['message'] = "Your Account is rejected by Admin.";
                  } */
                else {
                    $response['status'] = TRUE;
                    $response['message'] = "Phone Number is valid";
                    $response['User'] = $User;
                }
            }
        }
        return $response;
    }

    private function send_code_phone_number($user_id) {
        $response = array();
        $User = User::find($user_id);
        $otp_response = $this->send_phone_otp($User);
        if ($otp_response) {
            $response['status'] = TRUE;
            $response['message'] = "Verification code is sent to your phone no.";
        }
        else {
            $response['status'] = false;
            $response['message'] = "Error Sending SMS";
        }

        return $response;
    }

}
