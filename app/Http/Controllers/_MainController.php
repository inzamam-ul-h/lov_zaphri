<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Models\AuthKey;
use Twilio\Rest\Client;
use App\Models\User;
use App\Models\UserPersonal;
use App\Models\UserCalendar;
use App\Models\UserEducation;
use App\Models\UserProfessional;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\ContactDetail;

class MainController extends Controller {

    protected $_User_Id = 0;
    protected $_ADMIN_USER = 0;
    protected $_COACH_USER = 1;
    protected $_PLAYER_USER = 2;
    protected $_CLUB_USER = 3;
    protected $_PARENT_USER = 4;
    protected $dashboard_route = "dashboard";
    protected $uploads_root = "uploads";
    protected $uploads_default = "uploads/defaults";
    protected $uploads_users = "uploads/users";
    protected $uploads_videos = "uploads/videos";
    protected $uploads_events = "uploads/events";
    protected $uploads_trainings = "uploads/trainings";
    protected $uploads_plans = "uploads/plans";
    protected $uploads_teams = "uploads/teams";

    protected function get_user_array($User, $refresh_token = FALSE) {
        $data = NULL;
        $SITE_URL = env('APP_URL');
        if (!empty($User)) {
            $defaultImage = $SITE_URL . "/" . $this->uploads_default . "/user.png";
            $uploadsPath = $SITE_URL . "/" . $this->uploads_users . "/" . $User->id;
            $user_id = $User->id;
            $email = $User->email;
            $phone_prefix = $User->phone_prefix;
            $phone_no = $User->phone;
            $unique_id = $User->unique_code;
            $token = $User->auth_key;
            $user_type = $User->user_type;
            $fcm_token = $User->fcm_token;
            $user_type_label = 'Parent';
            if ($user_type == $this->_ADMIN_USER) {
                $user_type_label = "Admin";
            }
            elseif ($user_type == $this->_COACH_USER) {
                $user_type_label = "Coach";
            }
            elseif ($user_type == $this->_PLAYER_USER) {
                $user_type_label = "Player";
            }
            elseif ($user_type == $this->_CLUB_USER) {
                $user_type_label = "Club";
            }
            elseif ($user_type == $this->_PARENT_USER) {
                $user_type_label = "Parent";
            }
            $profile_status = $this->get_user_profile_status($User);
            //$status = $User->status;
            $created_by = $User->created_by;
            $first_name = '';
            $last_name = '';
            $about_me = '';
            $zip_code = '';
            $gender = '';
            $coachpic = '';
            $timezone = '';
            $res_profile = UserPersonal::where('user_id', $user_id)->first();
            if (!empty($res_profile)) {
                $first_name = $res_profile->first_name;
                $last_name = $res_profile->last_name;
                $about_me = $res_profile->about_me;
                $zip_code = $res_profile->zip_code;
                $gender = $res_profile->gender;
                $coachpic = $defaultImage;
                if (!empty($res_profile->coachpic) && $res_profile->coachpic != 'default_image' && $res_profile->coachpic != 'user.png')
                    $coachpic = $uploadsPath . "/" . $res_profile->coachpic;
            }
            $res_calender = UserCalendar::where('user_id', $user_id)->first();
            if (!empty($res_calender)) {
                $timezone = $res_calender->time_zone;
            }
            $user_name = trim($first_name) . ' ' . trim($last_name);
            $user_name = stripslashes(ltrim(rtrim($user_name)));

            $data = [
                'user_id'         => $user_id,
                'user_type'       => $user_type,
                'user_type_label' => $user_type_label,
                'first_name'      => $first_name,
                'last_name'       => $last_name,
                'user_name'       => $user_name,
                'unique_id'       => $unique_id,
                'email'           => $email,
                'phone_no'        => $phone_no,
                'phone_prefix'    => $phone_prefix,
                'profile_status'  => $profile_status,
                'about_me'        => $about_me,
                'zip_code'        => $zip_code,
                'timezone_id'     => $timezone,
                'gender'          => $gender,
                'coachpic'        => $coachpic,
                'created_by'      => $created_by,
                'fcm_token'       => $fcm_token,
            ];

            if ($user_type == $this->_COACH_USER) {
                $data["meetinglink"] = $res_profile->meetinglink;
                $data["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $user_id);
                $data["url_status"] = $User['url_status'];
            }
            if ($user_type == $this->_PLAYER_USER) {
                $data["dob"] = $res_profile->dob;
                $data["club"] = $res_profile->dob;
            }
            if ($user_type == $this->_CLUB_USER) {
                $data["reg_no"] = $res_profile->reg_no;
                $data["contact_person"] = $res_profile->contact_person;
                $data["address"] = $res_profile->address;
            }



            if ($refresh_token) {
                $rand_str = random_number(8);
                $token = $user_id . '-' . $rand_str . '-' . time();

                $AuthKey = new AuthKey();
                $AuthKey->user_id = $user_id;
                $AuthKey->auth_key = $token;
                $AuthKey->save();

                $data['token'] = $token;
            }
        }
        return $data;
    }

    protected function get_user_profile_status($User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $profile = UserPersonal::where('user_id', $user_id)->first();
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) { // for coach and player            
            $settings = UserCalendar::where('user_id', $user_id)->first();
            $time_zone = ($settings->time_zone ?? NULL);
            $UserProfessional = UserProfessional::where('user_id', $user_id)->first();

            if (!empty($profile->first_name) && !empty($profile->last_name) && !empty($profile->about_me) && !empty($profile->zip_code) && !empty($profile->gender) && !empty($time_zone) && !empty($UserProfessional->organizational_name) && !empty($UserProfessional->agegroups) && !empty($UserProfessional->experience)) {
                User::where('id', $user_id)->update(['profile_status' => 1]);
            }
        }
        elseif ($user_type == $this->_CLUB_USER) { // for club
            if (!empty($profile->first_name) && !empty($profile->last_name) && !empty($profile->reg_no) && !empty($profile->contact_person) && !empty($profile->address)) {
                User::where('id', $user_id)->update(['profile_status' => 1]);
            }
        }
        $User = User::find($user_id);
        $profileStatus = ($User->profile_status ?? 0);
        return $profileStatus;
    }

    protected function get_parent_user_ids($User) {
        $users = array();
        $users[] = 0;
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PARENT_USER) {
            $rows = User::select(['users.id'])->where('parent_id', $user_id)->get();
            foreach ($rows as $UserData) {
                $users[] = $UserData->id;
            }
        }
        return $users;
    }

    protected function get_club_user_ids($User) {
        $club_id = 0;
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            $club_id = $user_id;
        }
        elseif ($user_type == $this->_PLAYER_USER || $user_type == $this->_COACH_USER) {
            $UserData = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')->where([['user_professionals.club_authentication', 1], ['user_professionals.user_id', $user_id], ['users.profile_status', 1]])
                            ->select('user_professionals.club')->orderby('user_professionals.id', 'desc')->first();
            if (!empty($UserData)) {
                $club_id = $UserData->club;
            }
        }
        $users = array();
        if ($club_id > 0) {
            $users[] = $club_id; {
                $rows = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')
                                ->where([['user_professionals.club_authentication', 1], ['user_professionals.club', $club_id], ['users.profile_status', 1]])
                                ->select('users.id')->get();
                foreach ($rows as $UserData) {
                    $users[] = $UserData->id;
                }
            }
        }
        return $users;
    }

    protected function get_club_coach_ids($User) {
        $club_id = 0;
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            $club_id = $user_id;
        }
        elseif ($user_type == $this->_PLAYER_USER || $user_type == $this->_COACH_USER) {
            $UserData = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')->where([['user_professionals.club_authentication', 1], ['user_professionals.user_id', $user_id], ['users.profile_status', 1]])
                            ->select('user_professionals.club')->orderby('user_professionals.id', 'desc')->first();
            if (!empty($UserData)) {
                $club_id = $UserData->club;
            }
        }
        $users = array();
        if ($club_id > 0) {
            $rows = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')
                            ->where([['user_professionals.club_authentication', 1], ['user_professionals.club', $club_id], ['users.profile_status', 1], ['users.user_type', 1], ['users.status', 1]])
                            ->select('users.id')->get();
            foreach ($rows as $UserData) {
                $users[] = $UserData->id;
            }
        }
        return $users;
    }

    protected function get_club_player_ids($User) {
        $club_id = 0;
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_CLUB_USER) {
            $club_id = $user_id;
        }
        elseif ($user_type == $this->_PLAYER_USER || $user_type == $this->_COACH_USER) {
            $UserData = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')->where([['user_professionals.club_authentication', 1], ['user_professionals.user_id', $user_id], ['users.profile_status', 1]])
                            ->select('user_professionals.club')->orderby('user_professionals.id', 'desc')->first();
            if (!empty($UserData)) {
                $club_id = $UserData->club;
            }
        }
        $users = array();
        if ($club_id > 0) {
            $rows = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')
                            ->where([['user_professionals.club_authentication', 1], ['user_professionals.club', $club_id], ['users.profile_status', 1], ['users.user_type', 2], ['users.status', 1]])
                            ->select('users.id')->get();
            foreach ($rows as $UserData) {
                $users[] = $UserData->id;
            }
        }
        return $users;
    }

    protected function create_user($user_type, $pass, $type, $type_value, $phone_prefix = NULL, $created_by = NULL, $created_user_type = 0) {
        $User = NULL;

        $status = "1";

        $unique_code = '';
        do {
            $unique = 1;
            $unique_code = random_number();
            $res = User::where('unique_code', $unique_code)->first();
            if (!empty($res)) {
                $unique = 0;
            }
        }
        while ($unique == 0);

        if ($type == 'email') {
            $email = $type_value;

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
            $code = $this->get_email_otp($email);

            $User = new User();
            $User->user_type = $user_type;
            $User->email = $email;
            $User->password = bcrypt($pass);
            $User->public_url = $public_url;
            $User->status = $status;
            $User->email_verification_key = $code;
            $User->verified_token = $verified_token;
            $User->unique_code = $unique_code;
            if ($created_by != NULL) {
                $User->verified = 1;
                $User->email_verified = 1;
                $User->created_by = $created_by;
            }
            else {
                $User->verified = 0;
                $User->email_verified = 0;
            }
            $User->save();

            $this->send_verification_email($User);
        }
        elseif ($type == 'phone') {
            $phone_no = $type_value;

            $exists = 0;
            $public_url = createSlug(random_number());
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

            $verified_token = "1234";

            $User = new User();
            $User->user_type = $user_type;
            $User->phone = $phone_no;
            $User->phone_prefix = $phone_prefix;
            $User->password = bcrypt($pass);
            $User->public_url = $public_url;
            $User->status = $status;
            $User->verified = 0;
            $User->phone_verification_key = $verified_token;
            $User->unique_code = $unique_code;
            $User->save();
        }

        if ($User !== NULL) {

            $User->assignRole(get_user_type_role($user_type));
            $user_id = $User->id;

            $UserPersonal = new UserPersonal();
            $UserPersonal->user_id = $user_id;
            $UserPersonal->save();

            $UserEducation = new UserEducation();
            $UserEducation->user_id = $user_id;
            $UserEducation->save();

            $UserProfessional = new UserProfessional();
            $UserProfessional->user_id = $user_id;
            if ($created_by != NULL) {
                $UserProfessional->club = $created_by;
                $UserProfessional->club_authentication = 1;
            }
            $UserProfessional->save();
        }

        return $user_id;
    }

    protected function get_email_otp($email) {
        $otp = 1234;
        $pos = strpos($email, 'test');
        if ($pos === false) {
            $otp = rand(1000, 9999);
        }
        return $otp;
    }

    protected function get_phone_otp($phone_no, $send_otp) {
        $otp = 1234;
        if ($send_otp > 0) {
            $pos = strpos($phone_no, '+92');
            if ($pos === false) {
                $otp = rand(1000, 9999);
            }
        }
        return $otp;
    }

    protected function send_phone_otp($User) {
        $user_id = $User->id;
        $phone_no = $User->phone;
        $send_otp = 0;
        if ($send_otp > 0) {
            $pos = strpos($phone_no, '+92');
            if ($pos === false) {
                //$send_otp = 1;
            }
        }
        $otp = $this->get_phone_otp($phone_no, $send_otp);

        $User = User::find($user_id);
        $User->reset_pass_token = $otp;
        $User->phone_verification_key = $otp;
        $User->save();

        if ($send_otp > 0) {
            try {
                $twilio_message = "Your Zaphri Varification Code is $otp";
                $this->sendTwilioMessage($twilio_message, $phone_no);
                return TRUE;
            }
            catch (\Throwable $th) {
                return FALSE;
            }
        }
        else {
            return TRUE;
        }
    }

    protected function sendTwilioMessage($message, $recipients) {
        $pos = strpos($recipients, '+92');
        if ($pos === false) {
            $account_sid = env("TWILIO_SID");
            $auth_token = env("TWILIO_AUTH_TOKEN");
            $twilio_number = env("TWILIO_NUMBER");
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($recipients,
                    ['from' => $twilio_number, 'body' => $message]);
        }
    }

    protected function create_uploads_directory($uploads_path) {
        if (!is_dir($uploads_path)) {
            $uploads_root = $this->uploads_root;
            $src_file = $uploads_root . "/index.html";
            $directory = $uploads_root;
            $upload_directories = str_replace($this->uploads_root . '/', '', $uploads_path);
            $upload_directories = explode('/', $upload_directories);
            foreach ($upload_directories as $dir) {
                $directory .= '/' . $dir;
                $dest_file = $directory . "/index.html";
                if (!is_dir($directory)) {
                    mkdir($directory);
                    copy($src_file, $dest_file);
                }
            }
        }
        return $uploads_path;
    }

    protected function upload_file_to_path($file, $path) {
        /* $validator = Validator::make($request->all(), [
          'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
          ]);
          if ($validator->fails()) {
          return sendCustomResponse($validator->messages()->first(),  'error', 500);
          } */
        $code = rand(1000, 9999);

        $path = $this->create_uploads_directory($path);
        $fileName = $code . '-' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);

        return $fileName;
    }

    /* Emails utilities */

    protected function save_booking_payments($slots, $User) {
        $user_id = $User->id;

        $bypass_payment = 1;
        $bypass_status = $bypass_payment;

        $pay_date = time();
        $trx_id = $random = substr(md5(mt_rand()), 0, 15);

        $total_payment = 0;
        $session_prices = array();

        $results = Payment::where('status', 0)->get();
        if (!empty($results)) {
            foreach ($results as $payment) {
                PaymentDetail::where('payment_id', $payment->id)->delete();
                Payment::find($payment->id)->delete();
            }
        }
        $session_exists = 0;
        $sessions = Session::select('id', 'price')->whereIn('id', $slots)->get();
        if (!empty($sessions)) {
            foreach ($sessions as $session) {
                $session_exists = 1;
                $total_payment = ($total_payment + $session->price);
                $session_prices[$session->id] = $session->price;
            }
        }
        $status = FALSE;
        $message = 'No Sessions Found';
        $payment_id = 0;
        if ($session_exists > 0) {
            $Payment = new Payment();
            $Payment->user_id = $user_id;
            $Payment->amount = $total_payment;
            if ($bypass_payment == 1) {
                $Payment->paid_amount = $total_payment;
                $Payment->pay_date = $pay_date;
                $Payment->transaction_id = $trx_id;
            }
            $Payment->status = $bypass_status;
            $Payment->save();
            $payment_id = $Payment->id;

            $bookings = Booking::select('id', 'session_id')->whereIn('session_id', $slots)->get();
            if (!empty($bookings)) {
                foreach ($bookings as $booking) {
                    $booking_id = $booking->id;
                    $session_id = $booking->session_id;
                    $amount = $session_prices[$session_id];

                    $updBooking = Booking::find($booking_id);
                    $updBooking->payment_id = $payment_id;
                    if ($bypass_payment == 1) {
                        $updBooking->status = 2;
                    }
                    $updBooking->save();

                    $PaymentDetail = new PaymentDetail();
                    $PaymentDetail->payment_id = $payment_id;
                    $PaymentDetail->booking_id = $booking_id;
                    $PaymentDetail->amount = $amount;
                    if ($bypass_payment == 1) {
                        $PaymentDetail->paid_amount = $amount;
                        $PaymentDetail->pay_date = $pay_date;
                        $PaymentDetail->status = $bypass_status;
                    }
                    $PaymentDetail->status = $bypass_status;
                    $PaymentDetail->save();

                    send_booking_email($booking_id);
                }
            }

            $status = TRUE;
            $message = 'Successfully Created Payment';
            if ($bypass_payment == 1) {
                $message = 'Successfully Generated Payment';
            }
        }

        $response = new \stdClass();
        $response->status = $status;
        $response->payment_id = $payment_id;
        $response->message = $message;

        return $response;
    }

    /* Emails utilities */

    protected function send_verification_email($User) {
        $email = $User->email;
        $user_name = $User->name;
        $user_type = ucwords(get_user_type_role($User->user_type));
        $code = $User->email_verification_key;
        $verified_token = $User->verified_token;

        $confirm_link = "[SITEURL]/verify-email/" . $email . "/" . $verified_token;

        $button_text = '<a rel="nofollow" target="_blank" href="' . $confirm_link . '" style="background:#46b34a;padding:15px 60px;border-radius:30px;font-size:17px;text-decoration:none;color:#fff;font-weight:bold;">
            Verify Email Address
        </a>';

        $subject = getGeneralData('verify_subject');

        $email_message = getGeneralData('verify_email');
        $email_message = str_replace('[USERNAME]', $user_name, $email_message);
        $email_message = str_replace('[USERTYPE]', $user_type, $email_message);
        $email_message = str_replace('[Email]', $email, $email_message);
        $email_message = str_replace('[code]', $code, $email_message);
        $email_message = str_replace('[Button: link to verify]', $button_text, $email_message);
        $email_message = str_replace('[text: link to verify]', $confirm_link, $email_message);

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => $user_name,
            'mail_receiver_email ' => $email,
            'success_msg'          => 'Verification Email Sent Successfully',
            'error_msg'            => 'Could not send Verification Email. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

    protected function send_welcome_email($User) {
        $email = $User->email;
        $user_name = $User->name;
        $user_type = ucwords(get_user_type_role($User->user_type));

        $subject = getGeneralData('welcome_subject');

        $email_message = getGeneralData('welcome_email');
        $email_message = str_replace('[USERNAME]', $user_name, $email_message);
        $email_message = str_replace('[USERTYPE]', $user_type, $email_message);
        $email_message = str_replace('[Email]', $email, $email_message);

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => $user_name,
            'mail_receiver_email ' => $email,
            'success_msg'          => 'Welcome Email Sent Successfully',
            'error_msg'            => 'Could not send Welcome Email. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

    protected function send_rejection_email($User) {
        $email = $User->email;
        $user_name = $User->name;
        $user_type = ucwords(get_user_type_role($User->user_type));

        $subject = 'Account Rejected';

        $email_message = '<p>Dear [USERNAME],</p><p><span style="white-space:pre">						</span></p><p>Your [SITENAME] account application has been rejected as a [USERTYPE].</p><p><br></p><p style="color: rgb(29, 34, 40); font-family: " helvetica="" neue",="" helvetica,="" arial,="" sans-serif;"="">Thank you for choosing <span style="color: rgb(103, 106, 108); font-family: " open="" sans",="" "helvetica="" neue",="" helvetica,="" arial,="" sans-serif;"="">[SITENAME]</span>!</p><p style="color: rgb(29, 34, 40); font-family: " helvetica="" neue",="" helvetica,="" arial,="" sans-serif;"="">-<br>The <span style="color: rgb(103, 106, 108); font-family: " open="" sans",="" "helvetica="" neue",="" helvetica,="" arial,="" sans-serif;"="">[SITENAME] </span>Team</p><p style="color: rgb(29, 34, 40); font-family: " helvetica="" neue",="" helvetica,="" arial,="" sans-serif;"=""><span style="font-size: 10pt; color: rgb(103, 106, 108);">&nbsp;</span><br></p><p></p><p style="margin: 0cm 0cm 7.5pt; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial;"><span segoe="" ",="" roboto,="" oxygen,="" ubuntu,="" "fira="" "droid="" "helvetica="" sans-serif;="" font-size:="" 14px;="" letter-spacing:="" -0.07px;="" white-space:="" "=""><span open="" sans",sans-serif;color:#172b4d"="" style="font-size: 10pt;">You are receiving this email because you recently created an account in the&nbsp;</span></span><span open="" sans",="" sans-serif;"="" style="font-size: 10pt;">[SITENAME]</span><span segoe="" ",="" roboto,="" oxygen,="" ubuntu,="" "fira="" "droid="" "helvetica="" sans-serif;="" font-size:="" 14px;="" letter-spacing:="" -0.07px;="" white-space:="" "=""><span open="" sans",sans-serif;color:#172b4d"="" style="font-size: 10pt;">&nbsp;registered with this email address. If this was not you, please ignore this email.</span></span></p>';
        $email_message = str_replace('[USERNAME]', $user_name, $email_message);
        $email_message = str_replace('[USERTYPE]', $user_type, $email_message);
        $email_message = str_replace('[Email]', $email, $email_message);

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => $user_name,
            'mail_receiver_email ' => $email,
            'success_msg'          => 'Rejection Email Sent Successfully',
            'error_msg'            => 'Could not send Rejection Email. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

    protected function send_forget_password_email($User) {
        $email = $User->email;
        $user_name = $User->name;
        $user_type = ucwords(get_user_type_role($User->user_type));
        $reset_pass_token = $User->reset_pass_token;

        $reset_link = "[SITEURL]/reset-password/$email/$reset_pass_token";

        $button_text = '<a rel="nofollow" target="_blank" href="' . $reset_link . '" style="background:#46b34a;padding:15px 60px;border-radius:30px;font-size:17px;text-decoration:none;color:#fff;font-weight:bold;">
                Reset Password
        </a>';

        $subject = getGeneralData('forgot_subject');
        $email_message = getGeneralData('forgot_email');
        $email_message = str_replace('[USERNAME]', $user_name, $email_message);
        $email_message = str_replace('[USERTYPE]', $user_type, $email_message);
        $email_message = str_replace('[Email]', $email, $email_message);
        $email_message = str_replace('[code]', $reset_pass_token, $email_message);
        $email_message = str_replace('[Button: link to reset]', $button_text, $email_message);
        $email_message = str_replace('[text: link to reset]', $reset_link, $email_message);

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => $user_name,
            'mail_receiver_email ' => $email,
            'success_msg'          => 'Reset Password Email Sent Successfully',
            'error_msg'            => 'Could not send Reset Password Email. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

    protected function send_reset_password_email($User) {
        $email = $User->email;
        $user_name = $User->name;
        $user_type = ucwords(get_user_type_role($User->user_type));

        $subject = getGeneralData('reset_subject');

        $email_message = getGeneralData('reset_email');
        $email_message = str_replace('[USERNAME]', $user_name, $email_message);
        $email_message = str_replace('[USERTYPE]', $user_type, $email_message);
        $email_message = str_replace('[Email]', $email, $email_message);

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => $user_name,
            'mail_receiver_email ' => $email,
            'success_msg'          => 'Reset Password confirmation Email Sent Successfully',
            'error_msg'            => 'Could not send Reset Password confirmation Email. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

    protected function send_inquiry_email($request, $User, $event_id) {
        $response = array();
        $Model_Data = Event::find($event_id);

        if (empty($Model_Data)) {
            $response['responseStatus'] = FALSE;
            $response['responseText'] = 'Event details not found';
        }

        $inquiry = $request->inquiry;

        $event_title = $Model_Data->title;

        $Event_User = User::find($Model_Data->user_id);
        $event_user_name = $Event_User->name;
        $event_user_email = $Event_User->email;

        $subject = getGeneralData('inquire_event_subject');
        $email_message = getGeneralData('inquire_event_email');
        $email_message = str_replace('[INQUIRY]', $inquiry, $email_message);
        $email_message = str_replace('[INQUIRY_USER_NAME]', $User->name, $email_message);
        $email_message = str_replace('[EVENT_TITLE]', $event_title, $email_message);
        $email_message = str_replace('[USER_NAME]', $event_user_name, $email_message);

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => $event_user_name,
            'mail_receiver_email ' => $event_user_email,
            'success_msg'          => 'Inquiry Sent Successfully',
            'error_msg'            => 'Could not send Inquiry. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

    protected function send_contact_request_email($request) {
        $name = trim($request->name);
        $email = trim($request->email);
        $website = trim($request->website);
        $comment = trim($request->comment);

        $ContactDetail = ContactDetail::find(1);
        $mail_to = $ContactDetail->email;

        $subject = 'Contact Request';

        $email_message = '';
        $email_message .= '<tr>';
        $email_message .= '<td>Dear Representative,</td>';
        $email_message .= '</tr>';

        $email_message .= '<tr><td>&nbsp;</td></tr>';

        $email_message .= '<tr>';
        $email_message .= '<td>We have received new Contact Request.</td>';
        $email_message .= '</tr>';

        $email_message .= '<tr><td>&nbsp;</td></tr>';

        $email_message .= '<tr>';
        $email_message .= '<td>Here are the details provided by site visitor</td>';
        $email_message .= '</tr>';

        if (!empty($name)) {
            $email_message .= '<tr><td>&nbsp;</td></tr>';

            $email_message .= '<tr>';
            $email_message .= '<td>Name: ';
            $email_message .= $name;
            $email_message .= '</td>';
            $email_message .= '</tr>';
        }

        if (!empty($email)) {
            $email_message .= '<tr><td>&nbsp;</td></tr>';

            $email_message .= '<tr>';
            $email_message .= '<td>Email: ';
            $email_message .= $email;
            $email_message .= '</td>';
            $email_message .= '</tr>';
        }

        if (!empty($website)) {
            $email_message .= '<tr><td>&nbsp;</td></tr>';

            $email_message .= '<tr>';
            $email_message .= '<td>Website: ';
            $email_message .= $website;
            $email_message .= '</td>';
            $email_message .= '</tr>';
        }

        if (!empty($website)) {
            $email_message .= '<tr><td>&nbsp;</td></tr>';

            $email_message .= '<tr>';
            $email_message .= '<td>Additional details</td>';
            $email_message .= '</tr>';

            $email_message .= '<tr><td>&nbsp;</td></tr>';

            $email_message .= '<tr>';
            $email_message .= '<td>';
            $email_message .= $comment;
            $email_message .= '</td>';
            $email_message .= '</tr>';
        }

        $email_message .= '<tr><td>&nbsp;</td></tr>';

        $email_data = [
            'subject '             => $subject,
            'message'              => $email_message,
            'mail_receiver_name'   => 'Site Admin',
            'mail_receiver_email ' => $mail_to,
            'success_msg'          => 'Contact Request Email Sent Successfully',
            'error_msg'            => 'Could not send Contact Request email. Please Try Again.'
        ];

        $response = new_custom_mail($email_data);

        return $response;
    }

}
