<?php

use App\Models\AgeGroup;
use App\Models\ContactDetail;
use App\Models\User;
use App\Models\Category;
use App\Models\SessionType;
use App\Models\TimeZone;
use App\Models\Experience;
use App\Models\UserPersonal;
use App\Models\UserCalendar;
use App\Models\UserProfessional;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentDetail;

if (!function_exists('get_profile_image_path')) {

    function get_profile_image_path($image) {
        $image_path = 'uploads/defaults/user.png';
        return $image_path;
    }

}

if (!function_exists('user_profile_image_path')) {

    function user_profile_image_path($user_id) {
        $SITE_URL = env('APP_URL');
        $profileImage = $SITE_URL . "/uploads/defaults/user.png";
        $User = User::find($user_id);
        if (!empty($User)) {
            $profileImage = $User->photo;
        }
        $profileImage = str_replace("localhost/lova/zaphry/code", $SITE_URL, $profileImage);
        $profileImage = str_replace("local.zaphry.com", $SITE_URL, $profileImage);
        $profileImage = str_replace("dev.zaphry.us", $SITE_URL, $profileImage);
        $profileImage = str_replace("zaphri.com", $SITE_URL, $profileImage);
        $profileImage = str_replace("http://", "https://", $profileImage);
        return $profileImage;
    }

}


if (!function_exists('get_club_id')) {

    function get_club_id($user_id) {
        $club_id = 0;
        $ModelData = UserProfessional::where('user_id', $user_id)
                ->where('club_authentication', '1')->orderby('id', 'desc')
                ->first();
        if (!empty($ModelData)) {
            $club_id = $ModelData->club;
        }

        return $club_id;
    }

}

if (!function_exists('get_user_name')) {

    function get_user_name($user_id) {
        $name = 'Not Provided';
        $User = User::find($user_id);
        if (!empty($User)) {
            $name = $User->name;
            $ModelData = UserPersonal::select('first_name', 'last_name')
                    ->where('user_id', $user_id)
                    ->first();
            if (!empty($ModelData)) {
                $f_name = $ModelData->first_name;
                $l_name = $ModelData->last_name;
                if ($f_name != '' || $l_name != '') {
                    $full_name = trim($f_name . ' ' . $l_name);
                    $name = stripslashes($full_name);
                }
            }
        }
        $name = ucwords($name);
        return $name;
    }

}

if (!function_exists('get_user_short_name')) {

    function get_user_short_name($user_id) {
        $name = trim(get_user_name($user_id));
        if ($name != '') {
            $array = explode(' ', $name);
            $name = '';
            foreach ($array as $arr) {
                $name .= $arr[0];
            }
        }
        return $name;
    }

}

if (!function_exists('get_user_type')) {

    function get_user_type($user_id) {
        $str = '';
        $ModelData = User::find($user_id);
        if (!empty($ModelData)) {
            $str = $ModelData->user_type;
        }
        return $str;
    }

}

if (!function_exists('get_user_email')) {

    function get_user_email($user_id) {
        $str = '';
        $ModelData = User::find($user_id);
        if (!empty($ModelData)) {
            $str = $ModelData->email;
        }
        return $str;
    }

}

if (!function_exists('get_user_data')) {

    function get_user_data($field, $user_id) {
        $str = '';
        $ModelData = User::find($user_id);
        if (!empty($ModelData)) {
            $str = stripslashes($ModelData->{$field});
        }
        return $str;
    }

}

if (!function_exists('get_age_group_title')) {

    function get_age_group_title($user_id) {

        $str = '';
        $ModelData = Agegroup::where('id', $user_id)
                ->first();

        if (!empty($ModelData)) {
            $str = $ModelData->title;
        }
        return $str;
    }

}

if (!function_exists('get_experience_title')) {

    function get_experience_title($user_id) {

        $str = '';
        $ModelData = Experience::where('id', $user_id)
                ->first();

        if (!empty($ModelData)) {
            $str = $ModelData->title;
        }
        return $str;
    }

}

if (!function_exists('get_session_type')) {

    function get_session_type($id) {
        $str = '';
        $ModelData = SessionType::where('id', $id)->pluck('name')->first();
        if (!empty($ModelData)) {
            $str = stripslashes($ModelData);
        }
        return $str;
    }

}

if (!function_exists('get_category')) {

    function get_category($cat_id) {
        $name = '';
        $ModelData = Category::find($cat_id);
        if (!empty($ModelData)) {
            $name = $ModelData->name;
        }
        return $name;
    }

}

if (!function_exists('get_user_profile_data')) {

    function get_user_profile_data($field, $user_id) {
        $str = '';
        $ModelData = UserPersonal::where('user_id', $user_id)->pluck($field)->first();
        if (!empty($ModelData)) {
            $str = stripslashes($ModelData);
        }
        return $str;
    }

}

if (!function_exists('get_user_profile_status')) {

    function get_user_profile_status($user_id) {
        $str = '';
        $ModelData = User::find($user_id);
        if (!empty($ModelData)) {
            $str = $ModelData->profile_status;
        }
        return $str;
    }

}

if (!function_exists('parent_invite_status')) {

    function parent_invite_status($status) {
        $str = '';
        switch ($status) {
            case 0: $str = 'Pending';
                break;
            case 1: $str = 'Accepted';
                break;
            case 2: $str = 'Rejected';
                break;
            case 3: $str = 'Removed by Parent';
                break;
            default: $str = 'others';
                break;
        }
        return $str;
    }

}

if (!function_exists('get_expiry')) {

    function get_expiry($time, $ago = " to go") {
        $time = ($time - time());  // to get the time since that moment

        $tokens = array(
            31536000 => 'year',
            2592000  => 'month',
            604800   => 'week',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ' . $ago;
        }
    }

}

if (!function_exists('get_age_title')) {

    function get_age_title($age_id) {
        $ageTitle = "";
        $ageGroup = AgeGroup::find($age_id);
        if ($ageGroup == null) {
            $ageTitle = 'unknown';
        }
        else {
            $ageTitle = $ageGroup->title;
        }
        return $ageTitle;
    }

}

if (!function_exists('get_contact_details_data')) {

    function get_contact_details_data($field) {
        $str = '';
        $contactDetails = ContactDetail::where('user_id', 1)->pluck($field);
        foreach ($contactDetails as $contactDetail) {
            $str = stripslashes($contactDetail);
        }
        return $str;
    }

}

if (!function_exists('get_user_rating')) {

    function get_user_rating($user_id) {
        $rating = get_user_data('rating', $user_id);
        $percentage = ($rating * 20);
        $str = "<ul class='star-rating'>
                    <li class='current-rating' style='width:" . $percentage . "%' title='" . $rating . " stars'></li>
                </ul>";
        return $str;
    }

}

if (!function_exists('get_user_calendar_data')) {

    function get_user_calendar_data($field, $user_id) {
        $str = '';
        $results = UserCalendar::select($field)->where('user_id', $user_id)->first();
        if (!empty($results)) {
            $str = stripslashes($results->{$field});
        }

        return $str;
    }

}

if (!function_exists('set_user_timezone')) {

    function set_user_timezone($user_id) {
        $time_zone = ltrim(rtrim(get_user_calendar_data('time_zone', $user_id)));
        $time_zone = get_timezone_data('name', $time_zone);
        if ($time_zone == '')
            $time_zone = 'Asia/Karachi';
        date_default_timezone_set($time_zone);
    }

}

if (!function_exists('get_timezone_title')) {

    function get_timezone_title($name, $field = 'display_name') {
        $str = '';
        $results = TimeZone::select($field)->where('name', $name)->get();
        if (!empty($results)) {
            foreach ($results as $row) {
                $str = str_replace('\r', '', $row->{$field});
            }
        }

        return $str;
    }

}

if (!function_exists('get_timezone_data')) {

    function get_timezone_data($field, $id) {
        $str = '';
        $results = TimeZone::select($field)->where('id', $id)->get();
        if (!empty($results)) {
            foreach ($results as $row) {
                $str = str_replace('\r', '', $row->{$field});
            }
        }

        return $str;
    }

}

if (!function_exists('get_last_session_price')) {

    function get_last_session_price($user_id) {

        $str = 50;
        $res = Session::where('user_id', $user_id)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = $res->price;
        }

        return $str;
    }

}

if (!function_exists('get_slot_data')) {

    function get_slot_data($field, $id) {

        $str = '';
        $res = Session::find($id);
        if (!empty($res)) {
            $str = stripslashes($res->{$field});
        }

        return $str;
    }

}

if (!function_exists('get_slot_data_by_time_start')) {

    function get_slot_data_by_time_start($time_start, $user_id) {

        $str = 0;
        $res = Session::where('user_id', $user_id)->where('time_start', $time_start)->where('status', 1)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = $res->id;
        }

        return $str;
    }

}

if (!function_exists('get_booking_data')) {

    function get_booking_data($field, $id) {

        $str = '';
        $res = Booking::find($id);
        if (!empty($res)) {
            $str = stripslashes($res->{$field});
        }

        return $str;
    }

}

if (!function_exists('get_booking_data_by_user_and_avail_id')) {

    function get_booking_data_by_user_and_avail_id($field, $session_id, $req_user_id) {

        $str = '';
        $res = Booking::where('req_user_id', $req_user_id)->where('session_id', $session_id)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = stripslashes($res->{$field});
        }

        return $str;
    }

}

if (!function_exists('get_booking_data_by_vail_id')) {

    function get_booking_data_by_vail_id($field, $session_id) {

        $str = '';
        $res = Booking::where('session_id', $session_id)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = stripslashes($res->{$field});
        }

        return $str;
    }

}

if (!function_exists('get_slot_status')) {

    function get_slot_status($session_id) {

        $str = 0;
        $res = Booking::where('session_id', $session_id)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = $res->status;
        }

        return $str;
    }

}

if (!function_exists('get_availability_req_status')) {

    function get_availability_req_status($time_start, $user_id) {

        $str = 0;
        $res = Session::where('user_id', $user_id)->where('time_start', $time_start)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = $res->req_booking;
        }

        return $str;
    }

}

if (!function_exists('get_availability_inv_status')) {

    function get_availability_inv_status($time_start, $user_id) {

        $str = 0;
        $res = Session::where('user_id', $user_id)->where('time_start', $time_start)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = $res->inv_booking;
        }

        return $str;
    }

}

if (!function_exists('get_slot_booking_invites_for_tooltip')) {

    function get_slot_booking_invites_for_tooltip($session_id) {

        $str = 0;
        $res = Booking::where('session_id', $session_id)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = 1;
        }

        return $str;
    }

}

if (!function_exists('get_slot_availability')) {

    function get_slot_availability($session_id) {

        $str = 0;
        $res = Booking::where('session_id', $session_id)->where('status', 1)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = 1;
        }

        return $str;
    }

}

if (!function_exists('get_slot_user_availability')) {

    function get_slot_user_availability($session_id, $user_id) {

        $str = 1;
        $res = Booking::where('req_user_id', $user_id)->where('session_id', $session_id)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $str = 0;
            $req_user_id = $res->req_user_id;
            $status = $res->status;
            if ($req_user_id == $user_id) {
                $str = 2; // pending user booking
                if ($status == 1) {
                    $str = 3; // approved user booking
                }
                elseif ($status == 2) {
                    $str = 4; // declined user booking
                }
            }
        }

        return $str;
    }

}

if (!function_exists('set_time_slot_from_profile')) {

    function set_time_slot_from_profile($user_id, $time_start, $time_end, $status, $update = 1) {

        $bool = 1;
        //set_user_timezone($user_id);

        $created_at = time();
        $time_start = strtotime($time_start);
        $time_end = strtotime($time_end);

        if ($time_start >= $created_at) {
            $slot_id = 0;
            $res = Session::where('user_id', $user_id)->where('time_start', $time_start)->where('time_end', $time_end)->orderby('id', 'desc')->first();
            if (!empty($res)) {
                $slot_id = $res->id;
            }

            if ($slot_id == 0) {
                $session = new Session();
                $session->user_id = $user_id;
                $session->time_start = $time_start;
                $session->time_end = $time_end;
                $session->status = $status;
                $session->save();
            }
            elseif ($update == 1 && $slot_id > 0) {
                $session = Session::find($slot_id);
                if ($status == 1) {
                    $session->status = $status;
                    $session->save();
                }
                else {
                    if (get_slot_status($slot_id) == 1) {
                        $session->status = $status;
                        $session->save();
                    }
                    else {
                        $bool = 0;
                    }
                }
            }
        }

        return $bool;
    }

}

if (!function_exists('set_time_slot')) {

    function set_time_slot($user_id, $type, $price, $description, $color, $year, $month, $day, $start_hour, $end_hour, $status, $update = 1, $mins = 0) {

        $bool = 1;
        //set_user_timezone($user_id);

        $created_at = time();
        $modified_at = time();

        $mins = sprintf('%02d', $mins);

        $time_start = strtotime(date("Y-m-d H:i:s", strtotime("$year-$month-$day $start_hour:$mins:00")));
        $time_end = strtotime(date("Y-m-d H:i:s", strtotime("$year-$month-$day $end_hour:$mins:00")));

        if ($time_start >= $created_at) {
            $slot_id = 0;
            $res = Session::where('user_id', $user_id)->where('time_start', $time_start)->where('time_end', $time_end)->orderby('id', 'desc')->first();
            if (!empty($res)) {
                $slot_id = $res->id;
            }

            if ($slot_id == 0) {
                $session = new Session();
                $session->user_id = $user_id;
                $session->type = $type;
                $session->price = $price;
                $session->description = $description;
                $session->color = $color;
                $session->time_start = $time_start;
                $session->time_end = $time_end;
                $session->status = $status;
                $session->save();
            }
            elseif ($update == 1 && $slot_id > 0) {
                $session = Session::find($slot_id);
                if ($status == 1) {
                    $session->status = $status;
                    $session->save();
                }
                else {
                    if (get_slot_status($slot_id) == 1) {
                        $session->status = $status;
                        $session->save();
                    }
                    else {
                        $bool = 0;
                    }
                }
            }
        }

        return $bool;
    }

}

if (!function_exists('update_time_slot_when_booking')) {

    function update_time_slot_when_booking($user_id, $session_id, $booking_id) {

        $bool = 1;
        $session = Session::find($session_id);
        if (!empty($session)) {
            $session->inv_booking = 1;
            $session->req_booking = 1;
            $session->status = 1;
            $session->save();
        }
        return $bool;
    }

}

if (!function_exists('send_booking_email')) {

    function send_booking_email($booking_id) {
        $current_time = time();

        $booking = Booking::find($booking_id);
        if (!empty($booking)) {

            $security = '';
            do {
                $unique = 1;
                $security = random_number();
                $res = Booking::where('req_security', $security)->orWhere('inv_security', $security)->first();
                if (!empty($res)) {
                    $unique = 0;
                }
            }
            while ($unique == 0);
            $req_security = $security;

            $security = '';
            do {
                $unique = 1;
                $security = random_number();
                $res = Booking::where('req_security', $security)->orWhere('inv_security', $security)->first();
                if (!empty($res)) {
                    $unique = 0;
                }
            }
            while ($unique == 0);
            $inv_security = $security;

            $booking->req_security = $req_security;
            $booking->inv_security = $inv_security;
            $booking->save();

            $session_id = $booking->session_id;

            $invited_user = User::find($booking->user_id);
            $invited_user_details = array();
            $invited_user_details['name'] = ucwords($invited_user->name);
            $invited_user_details['email'] = $invited_user->email;

            $requested_user = User::find($booking->req_user_id);
            $requested_user_details = array();
            $requested_user_details['name'] = ucwords($requested_user->name);
            $requested_user_details['email'] = $requested_user->email;

            $row = Session::find($session_id);
            if (!empty($row)) {
                $time_start = $row->time_start;
                $time_start_full = date("D M j Y H:i A T", $time_start);

                if ($time_start > $current_time) {

                    //requesting user email
                    {
                        $cancel_link = "[SITEURL]/cancel-booking/$req_security";
                        $button_cancel = '<a href="' . $cancel_link . '" style="font-weight:bold;color:#ed5565;">Cancel Booking</a>';

                        $reschedule_link = "[SITEURL]/reschedule-booking/$req_security";
                        $button_reschedule = '<a href="' . $reschedule_link . '" style="font-weight:bold;color:#3369E7;">Reschedule Booking</a>';

                        $email = $requested_user_details['email'];

                        $subject = getGeneralData('request_subject');
                        $subject = str_replace('[Invited-Username]', $invited_user_details['name'], $subject);
                        $subject = str_replace('[Requesting-Username]', $requested_user_details['name'], $subject);
                        $subject = str_replace('[DateTime]', $time_start_full, $subject);

                        $email_message = getGeneralData('request_email');
                        $email_message = str_replace('[Email]', $email, $email_message);
                        $email_message = str_replace('[Invited-Username]', $invited_user_details['name'], $email_message);
                        $email_message = str_replace('[Requesting-Username]', $requested_user_details['name'], $email_message);
                        $email_message = str_replace('[DateTime]', $time_start_full, $email_message);
                        $email_message = str_replace('[Cancel-Link]', $button_cancel, $email_message);
                        $email_message = str_replace('[Reschedule-Link]', $button_reschedule, $email_message);

                        $email_data = [
                            'subject '             => $subject,
                            'message'              => $email_message,
                            'mail_receiver_name'   => $requested_user_details['name'],
                            'mail_receiver_email ' => $email,
                            'success_msg'          => 'booking Email Sent Successfully',
                            'error_msg'            => 'Could not send booking Email. Please Try Again.'
                        ];

                        new_custom_mail($email_data);
                    }

                    //Invited user email
                    {
                        $cancel_link = "[SITEURL]/cancel-booking/$inv_security";
                        $button_cancel = '<a href="' . $cancel_link . '" style="font-weight:bold;color:#ed5565;">Cancel Booking</a>';

                        $reschedule_link = "[SITEURL]/reschedule-booking/$inv_security";
                        $button_reschedule = '<a href="' . $reschedule_link . '" style="font-weight:bold;color:#3369E7;">Reschedule Booking</a>';

                        $email = $invited_user_details['email'];

                        $subject = getGeneralData('booking_subject');
                        $subject = str_replace('[Invited-Username]', $invited_user_details['name'], $subject);
                        $subject = str_replace('[Requesting-Username]', $requested_user_details['name'], $subject);
                        $subject = str_replace('[DateTime]', $time_start_full, $subject);

                        $email_message = getGeneralData('booking_email');
                        $email_message = str_replace('[Email]', $email, $email_message);
                        $email_message = str_replace('[Invited-Username]', $invited_user_details['name'], $email_message);
                        $email_message = str_replace('[Requesting-Username]', $requested_user_details['name'], $email_message);
                        $email_message = str_replace('[DateTime]', $time_start_full, $email_message);
                        $email_message = str_replace('[Cancel-Link]', $button_cancel, $email_message);
                        $email_message = str_replace('[Reschedule-Link]', $button_reschedule, $email_message);

                        $email_data = [
                            'subject '             => $subject,
                            'message'              => $email_message,
                            'mail_receiver_name'   => $invited_user_details['name'],
                            'mail_receiver_email ' => $email,
                            'success_msg'          => 'booking Email Sent Successfully',
                            'error_msg'            => 'Could not send booking Email. Please Try Again.'
                        ];

                        new_custom_mail($email_data);
                    }
                }
            }
        }
    }

}

if (!function_exists('send_reschdule_email')) {

    function send_reschdule_email($booking_id) {
        $current_time = time();

        $booking = Booking::find($booking_id);
        if (!empty($booking)) {

            $security = '';
            do {
                $unique = 1;
                $security = random_number();
                $res = Booking::where('req_security', $security)->orWhere('inv_security', $security)->first();
                if (!empty($res)) {
                    $unique = 0;
                }
            }
            while ($unique == 0);
            $req_security = $security;

            $security = '';
            do {
                $unique = 1;
                $security = random_number();
                $res = Booking::where('req_security', $security)->orWhere('inv_security', $security)->first();
                if (!empty($res)) {
                    $unique = 0;
                }
            }
            while ($unique == 0);
            $inv_security = $security;

            $booking->req_security = $req_security;
            $booking->inv_security = $inv_security;
            $booking->save();

            $session_id = $booking->session_id;

            $invited_user = User::find($booking->user_id);
            $invited_user_details = array();
            $invited_user_details['name'] = ucwords($invited_user->name);
            $invited_user_details['email'] = $invited_user->email;

            $requested_user = User::find($booking->req_user_id);
            $requested_user_details = array();
            $requested_user_details['name'] = ucwords($requested_user->name);
            $requested_user_details['email'] = $requested_user->email;

            $row = Session::find($session_id);
            if (!empty($row)) {
                $time_start = $row->time_start;
                $time_start_full = date("D M j Y H:i A T", $time_start);

                if ($time_start > $current_time) {

                    //requesting user email
                    {
                        $cancel_link = "[SITEURL]/cancel-booking/$req_security";
                        $button_cancel = '<a href="' . $cancel_link . '" style="font-weight:bold;color:#ed5565;">Cancel Booking</a>';

                        $reschedule_link = "[SITEURL]/reschedule-booking/$req_security";
                        $button_reschedule = '<a href="' . $reschedule_link . '" style="font-weight:bold;color:#3369E7;">Reschedule Booking</a>';

                        $email = $requested_user_details['email'];

                        $subject = getGeneralData('reschedule_subject');
                        $subject = str_replace('[Invited-Username]', $invited_user_details['name'], $subject);
                        $subject = str_replace('[Requesting-Username]', $requested_user_details['name'], $subject);
                        $subject = str_replace('[DateTime]', $time_start_full, $subject);

                        $email_message = getGeneralData('reschedule_email');
                        $email_message = str_replace('[Email]', $email, $email_message);
                        $email_message = str_replace('[Invited-Username]', $invited_user_details['name'], $email_message);
                        $email_message = str_replace('[Requesting-Username]', $requested_user_details['name'], $email_message);
                        $email_message = str_replace('[DateTime]', $time_start_full, $email_message);
                        $email_message = str_replace('[Cancel-Link]', $button_cancel, $email_message);
                        $email_message = str_replace('[Reschedule-Link]', $button_reschedule, $email_message);

                        $email_data = [
                            'subject '             => $subject,
                            'message'              => $email_message,
                            'mail_receiver_name'   => $requested_user_details['name'],
                            'mail_receiver_email ' => $email,
                            'success_msg'          => 'Reschedule booking Email Sent Successfully',
                            'error_msg'            => 'Could not send Reschedule booking Email. Please Try Again.'
                        ];

                        new_custom_mail($email_data);
                    }

                    //Invited user email
                    {
                        $cancel_link = "[SITEURL]/cancel-booking/$inv_security";
                        $button_cancel = '<a href="' . $cancel_link . '" style="font-weight:bold;color:#ed5565;">Cancel Booking</a>';

                        $reschedule_link = "[SITEURL]/reschedule-booking/$inv_security";
                        $button_reschedule = '<a href="' . $reschedule_link . '" style="font-weight:bold;color:#3369E7;">Reschedule Booking</a>';

                        $email = $invited_user_details['email'];

                        $subject = getGeneralData('reschedule_subject');
                        $subject = str_replace('[Invited-Username]', $invited_user_details['name'], $subject);
                        $subject = str_replace('[Requesting-Username]', $requested_user_details['name'], $subject);
                        $subject = str_replace('[DateTime]', $time_start_full, $subject);

                        $email_message = getGeneralData('reschedule_email');
                        $email_message = str_replace('[Email]', $email, $email_message);
                        $email_message = str_replace('[Invited-Username]', $invited_user_details['name'], $email_message);
                        $email_message = str_replace('[Requesting-Username]', $requested_user_details['name'], $email_message);
                        $email_message = str_replace('[DateTime]', $time_start_full, $email_message);
                        $email_message = str_replace('[Cancel-Link]', $button_cancel, $email_message);
                        $email_message = str_replace('[Reschedule-Link]', $button_reschedule, $email_message);

                        $email_data = [
                            'subject '             => $subject,
                            'message'              => $email_message,
                            'mail_receiver_name'   => $invited_user_details['name'],
                            'mail_receiver_email ' => $email,
                            'success_msg'          => 'Reschedule booking Email Sent Successfully',
                            'error_msg'            => 'Could not send Reschedule booking Email. Please Try Again.'
                        ];

                        new_custom_mail($email_data);
                    }
                }
            }
        }
    }

}

if (!function_exists('send_cancel_email')) {

    function send_cancel_email($booking_id) {
        $current_time = time();

        $booking = Booking::find($booking_id);
        if (!empty($booking)) {

            $security = '';
            do {
                $unique = 1;
                $security = random_number();
                $res = Booking::where('req_security', $security)->orWhere('inv_security', $security)->first();
                if (!empty($res)) {
                    $unique = 0;
                }
            }
            while ($unique == 0);
            $req_security = $security;

            $security = '';
            do {
                $unique = 1;
                $security = random_number();
                $res = Booking::where('req_security', $security)->orWhere('inv_security', $security)->first();
                if (!empty($res)) {
                    $unique = 0;
                }
            }
            while ($unique == 0);
            $inv_security = $security;

            $booking->req_security = $req_security;
            $booking->inv_security = $inv_security;
            $booking->save();

            $session_id = $booking->session_id;

            $invited_user = User::find($booking->user_id);
            $invited_user_details = array();
            $invited_user_details['name'] = ucwords($invited_user->name);
            $invited_user_details['email'] = $invited_user->email;

            $requested_user = User::find($booking->req_user_id);
            $requested_user_details = array();
            $requested_user_details['name'] = ucwords($requested_user->name);
            $requested_user_details['email'] = $requested_user->email;

            $row = Session::find($session_id);
            if (!empty($row)) {
                $time_start = $row->time_start;
                $time_start_full = date("D M j Y H:i A T", $time_start);

                if ($time_start > $current_time) {

                    //requesting user email
                    {
                        $email = $requested_user_details['email'];

                        $subject = getGeneralData('cancel_subject');
                        $subject = str_replace('[Invited-Username]', $invited_user_details['name'], $subject);
                        $subject = str_replace('[Requesting-Username]', $requested_user_details['name'], $subject);
                        $subject = str_replace('[DateTime]', $time_start_full, $subject);

                        $email_message = getGeneralData('cancel_email');
                        $email_message = str_replace('[Email]', $email, $email_message);
                        $email_message = str_replace('[Invited-Username]', $invited_user_details['name'], $email_message);
                        $email_message = str_replace('[Requesting-Username]', $requested_user_details['name'], $email_message);
                        $email_message = str_replace('[DateTime]', $time_start_full, $email_message);

                        $email_data = [
                            'subject '             => $subject,
                            'message'              => $email_message,
                            'mail_receiver_name'   => $requested_user_details['name'],
                            'mail_receiver_email ' => $email,
                            'success_msg'          => 'Cancel booking Email Sent Successfully',
                            'error_msg'            => 'Could not send Cancel booking Email. Please Try Again.'
                        ];

                        new_custom_mail($email_data);
                    }

                    //Invited user email
                    {
                        $email = $invited_user_details['email'];

                        $subject = getGeneralData('cancel_subject');
                        $subject = str_replace('[Invited-Username]', $invited_user_details['name'], $subject);
                        $subject = str_replace('[Requesting-Username]', $requested_user_details['name'], $subject);
                        $subject = str_replace('[DateTime]', $time_start_full, $subject);

                        $email_message = getGeneralData('cancel_email');
                        $email_message = str_replace('[Email]', $email, $email_message);
                        $email_message = str_replace('[Invited-Username]', $invited_user_details['name'], $email_message);
                        $email_message = str_replace('[Requesting-Username]', $requested_user_details['name'], $email_message);
                        $email_message = str_replace('[DateTime]', $time_start_full, $email_message);

                        $email_data = [
                            'subject '             => $subject,
                            'message'              => $email_message,
                            'mail_receiver_name'   => $invited_user_details['name'],
                            'mail_receiver_email ' => $email,
                            'success_msg'          => 'Cancel booking Email Sent Successfully',
                            'error_msg'            => 'Could not send Cancel booking Email. Please Try Again.'
                        ];

                        new_custom_mail($email_data);
                    }
                }
            }
        }
    }

}

if (!function_exists('get_payment_data')) {

    function get_payment_data($field, $id) {

        $str = '';
        $res = Payment::find($id);
        if (!empty($res)) {
            $str = stripslashes($res->{$field});
        }

        return $str;
    }

}

if (!function_exists('save_payment')) {

    function save_payment($payment_id, $payment_amount, $txn_id) {

        $pay_date = time();
        $res = Payment::where('id', $payment_id)->where('status', 0)->orderby('id', 'desc')->first();
        if (!empty($res)) {
            $amount = $res->amount;
            $paid_amount = $amount;
            $status = 1;
            if ($paid_amount > $payment_amount) {
                $status = 0;
                $paid_amount = $payment_amount;
            }
            $payment = Payment::find($payment_id);
            $payment->paid_amount = $payment_amount;
            $payment->pay_date = $pay_date;
            $payment->transaction_id = $txn_id;
            $payment->status = $status;
            $payment->save();

            $booking = Booking::where('payment_id', $payment_id)->orderby('id', 'desc')->first();
            $booking->status = 2;
            $booking->save();
        }

        $remaining_amount = $payment_amount;

        $booking_id = 0;
        $details = PaymentDetail::where('payment_id', $payment_id)->get();
        if (!empty($details)) {
            foreach ($details as $detail) {
                $booking_id = $detail->booking_id;
                $detail_id = $detail->id;
                $amount = $detail->amount;
                $paid_amount = $amount;
                $status = 1;
                if ($paid_amount > $remaining_amount) {
                    $status = 0;
                    $paid_amount = $remaining_amount;
                }
                $payment = PaymentDetail::find($detail_id);
                $payment->paid_amount = $paid_amount;
                $payment->pay_date = $pay_date;
                $payment->status = $status;
                $payment->save();

                $remaining_amount = ($remaining_amount - $paid_amount);

                $session_id = get_booking_data('session_id', $booking_id);
                $payee_id = get_booking_data('req_user_id', $booking_id);

                update_time_slot_when_booking($payee_id, $session_id, $booking_id);

                send_booking_email($booking_id);
            }
        }
        return $str = 1;
    }

}

if (!function_exists('cal_availability_0')) {

    function cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins) {
        for ($i = $limit_start; $i <= $limit_end; $i++) {
            $timestamp = strtotime("$year_selected-$month_selected-$i 01:00:00");
            $full_day = date('l', $timestamp);
            if ($full_day_selected == $full_day) {
                for ($j = 0; $j < 24; $j++) {
                    $x = ($j + 1);
                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $i, $j, $x, $status, $update, $available_mins);
                    $date_ = date("Y-m-d", strtotime("$year_selected-$month_selected-$i $j:00:00"));
                    if ($bool == 1) {
                        $success = 1;
                        /* $message = array();
                          $message['status'] = 1;
                          $message['text'] = "Updated availability to not available on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>";
                          $success_messages[] = $message; */
                    }
                    else {
                        $message = array();
                        $message['status'] = 0;
                        $message['text'] = "You have a booked slot on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>. Please cancel or reschedule this booking to update your availability";
                        $error_messages[] = $message;
                    }
                }
            }
        }
    }

}

if (!function_exists('cal_availability_0_recurring')) {

    function cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec) {
        for ($i = $limit_start; $i <= $limit_end; $i++) {
            $proceed = 0;
            $timestamp = strtotime("$year_selected-$month_selected-$i 01:00:00");
            $full_day = date('l', $timestamp);
            switch ($full_day) {
                case 'Sunday':
                    if ($sunday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Monday':
                    if ($monday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Tuesday':
                    if ($tuesday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Wednesday':
                    if ($wednesday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Thursday':
                    if ($thursday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Friday':
                    if ($friday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Saturday':
                    if ($saturday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                default:
                    break;
            }

            if ($proceed == 1) {
                for ($j = 0; $j < 24; $j++) {
                    $x = ($j + 1);
                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $i, $j, $x, $status, $update, $available_mins);
                    $date_ = date("'M d, Y", strtotime("$year_selected-$month_selected-$i $j:00:00"));
                    if ($bool == 1) {
                        $success = 1;
                        /* $message = array();
                          $message['status'] = 1;
                          $message['text'] = "Changed availability to not available on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>";
                          $success_messages[] = $message; */
                    }
                    else {
                        $message = array();
                        $message['status'] = 0;
                        $message['text'] = "You have a booked slot on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>. Please cancel or reschedule this booking to update your availability";
                        $error_messages[] = $message;
                    }
                }
            }
        }
    }

}

if (!function_exists('cal_availability_1')) {

    function cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr) {
        for ($i = $limit_start; $i <= $limit_end; $i++) {
            $timestamp = strtotime("$year_selected-$month_selected-$i 01:00:00");
            $full_day = date('l', $timestamp);
            if ($full_day_selected == $full_day) {
                for ($b = 0; $b < $count_arr; $b++) {
                    $j = $from_arr[$b];
                    $x = ($j + 1);
                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $i, $j, $x, $status, $update, $available_mins);
                    $date_ = date("Y-m-d", strtotime("$year_selected-$month_selected-$i $j:00:00"));
                    if ($bool == 1) {
                        $success = 1;
                        /* $message = array();
                          $message['status'] = 1;
                          $message['text'] = "Updated availability to not available on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>";
                          $success_messages[] = $message; */
                    }
                }
            }
        }
    }

}

if (!function_exists('cal_availability_1_recurring')) {

    function cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec) {
        for ($i = $limit_start; $i <= $limit_end; $i++) {
            $proceed = 0;
            $timestamp = strtotime("$year_selected-$month_selected-$i 01:00:00");
            $full_day = date('l', $timestamp);
            switch ($full_day) {
                case 'Sunday':
                    if ($sunday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Monday':
                    if ($monday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Tuesday':
                    if ($tuesday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Wednesday':
                    if ($wednesday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Thursday':
                    if ($thursday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Friday':
                    if ($friday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                case 'Saturday':
                    if ($saturday_rec == 1) {
                        $proceed = 1;
                    }
                    break;

                default:
                    break;
            }

            if ($proceed == 1) {
                for ($b = 0; $b < $count_arr; $b++) {
                    $j = $from_arr[$b];
                    $x = ($j + 1);
                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $i, $j, $x, $status, $update, $available_mins);
                    $date_ = date("'M d, Y", strtotime("$year_selected-$month_selected-$i $j:00:00"));
                    if ($bool == 1) {
                        $success = 1;
                        /* $message = array();
                          $message['status'] = 1;
                          $message['text'] = "Changed non availability to available on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>";
                          $success_messages[] = $message; */
                    }
                }
            }
        }
    }

}

