<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\UserPersonal;
use App\Models\UserEducation;
use App\Models\UserProfessional;

// this function currently not in use
if (!function_exists('update_all_bookings')) {

    function update_all_bookings($session_end = 0) {
        $session_end = time(); //Carbon::now()->timestamp;

        $bookings = Booking::join('sessions as session', 'session.id', '=', 'bookings.session_id')
                ->where('session.time_end', '<=', $session_end)
                ->where('session.booked', 1)
                ->whereIn('bookings.status', [1, 2, 7])
                ->select('session.time_end', 'bookings.id', 'bookings.status', 'bookings.coach_feedback', 'bookings.player_feedback')
                ->get();

        foreach ($bookings as $booking) {
            $time_end = $booking->time_end;
            if ($time_end <= $session_end) {
                $booking_id = $booking->id;
                $status = $booking->status;

                if ($status == 1) {
                    update_booking(10, $booking_id);
                }
                elseif ($status == 2) {
                    update_booking(7, $booking_id);
                }
                elseif ($status == 7) {
                    $coach_feedback = $booking->coach_feedback;
                    $player_feedback = $booking->player_feedback;
                    if ($coach_feedback == 1 && $player_feedback == 1) {
                        update_booking(8, $booking_id);
                    }
                }
            }
        }
    }

}

// this function currently not in use
if (!function_exists('update_booking')) {

    function update_booking($status, $booking_id) {
        Booking::where('id', $booking_id)->update(['status' => $status]);
    }

}


if (!function_exists('get_sessesion_status_array')) {

    function get_sessesion_status_array($user_type, $type) {
        $status_array = [];
        switch ($user_type) {
            case 0://admin
                if ($type == 'dashboard_upcoming') {
                    $status_array = [1, 2, 3, 4, 5, 6]; //In                   
                }
                elseif ($type == 'dashboard_all') {
                    $status_array = [NULL, 0, 1, 2];  //NotIn                  
                }
                elseif ($type == 'session_upcoming') {
                    $status_array = [NULL, 0, 1, 2, 3, 4, 5, 6]; //In
                }
                elseif ($type == 'session_all') {
                    $status_array = [NULL, 0, 1]; //NotIn 
                }
                break;
            case 1://coach
                if ($type == 'dashboard_upcoming') {
                    $status_array = [1, 2, 4, 6]; //In               
                }
                elseif ($type == 'dashboard_all') {
                    $status_array = [NULL, 0, 1, 2, 3, 9, 10];  //NotIn                   
                }
                elseif ($type == 'session_upcoming') {
                    $status_array = [NULL, 0, 1, 2, 4, 6]; //In
                }
                elseif ($type == 'session_all') {
                    $status_array = [0, 1, 2, 3]; //NotIn //0, 1, 
                }
                break;
            case 2://player
                if ($type == 'dashboard_upcoming') {
                    $status_array = [1, 2, 3, 5]; //In                   
                }
                elseif ($type == 'dashboard_all') {
                    $status_array = [NULL, 0, 1, 2, 4, 9, 10];  //NotIn                  
                }
                elseif ($type == 'session_upcoming') {
                    $status_array = [1, 2, 3, 5]; //In
                }
                elseif ($type == 'session_all') {
                    $status_array = [0, 1, 2, 4]; //NotIn 
                }
                break;
            case 4://parent
                if ($type == 'dashboard_unpaid') {
                    $status_array = [1]; //In                    
                }
                elseif ($type == 'dashboard_paid') {
                    $status_array = [2, 7, 8, 9];  //In                   
                }
                break;
            default: break;
        }
        return $status_array;
    }

}

if (!function_exists('get_booking_status_details')) {

    function get_booking_status_details($status) {
        $str = '';
        switch ($status) {
            case 0: $str = 'Available';
                break;
            case 1: $str = 'Payment Pending';
                break;
            case 2: $str = 'Payment Confirmed';
                break;
            case 3: $str = 'Booked & Canceled by Coach';
                break;
            case 4: $str = 'Booked & Canceled by Player';
                break;
            case 5: $str = 'Confirmed & Canceled by Coach';
                break;
            case 6: $str = 'Confirmed & Canceled by Player';
                break;
            case 7: $str = 'Feedback Pending';
                break;
            case 8: $str = 'Delivered';
                break;
            case 9: $str = 'Expired';
                break;
            case 10: $str = 'Not Confirmed & Expired';
                break;
            default: $str = 'others';
                break;
        }
        return $str;
    }

}






if (!function_exists('get_user_type_role')) {

    function get_user_type_role($user_type) {
        $str = 'super_admin';
        switch ($user_type) {
            case 1: $str = 'coach';
                break;
            case 2: $str = 'player';
                break;
            case 3: $str = 'club';
                break;
            case 4: $str = 'parent';
                break;
            default: break;
        }
        return $str;
    }

}

if (!function_exists('test_input')) {

    function test_input($str) {
        $str = rtrim(ltrim($str));
        return $str;
    }

}

if (!function_exists('generate_club_profile')) {

    function generate_club_profile($user) {
        $user_id = $user->id;
        generate_personal_profiles($user_id);
        generate_eductaional_profiles($user_id);
        generate_professional_profiles($user_id);
    }

}

if (!function_exists('generate_coach_profile')) {

    function generate_coach_profile($user) {
        $user_id = $user->id;
        generate_personal_profiles($user_id);
        generate_eductaional_profiles($user_id);
        generate_professional_profiles($user_id);
    }

}

if (!function_exists('generate_parent_profile')) {

    function generate_parent_profile($user) {
        $user_id = $user->id;
        generate_personal_profiles($user_id);
        generate_eductaional_profiles($user_id);
        generate_professional_profiles($user_id);
    }

}

if (!function_exists('generate_player_profile')) {

    function generate_player_profile($user) {
        $user_id = $user->id;
        generate_personal_profiles($user_id);
        generate_eductaional_profiles($user_id);
        generate_professional_profiles($user_id);
    }

}




if (!function_exists('generate_personal_profiles')) {

    function generate_personal_profiles($user_id) {
        $first_name = '';
        $last_name = '';
        $User = User::find($user_id);
        if (!empty($User->name)) {
            $name_arr = explode(' ', $User->name);
            $first_name = $name_arr[0];
            $last_name = trim(str_replace($first_name, '', $User->name));
        }

        $model = new UserPersonal();
        $model->user_id = $user_id;
        $model->first_name = $first_name;
        $model->last_name = $last_name;
        $model->save();
    }

}

if (!function_exists('generate_eductaional_profiles')) {

    function generate_eductaional_profiles($user_id) {
        $model = new UserEducation();
        $model->user_id = $user_id;
        $model->save();
    }

}


if (!function_exists('generate_professional_profiles')) {

    function generate_professional_profiles($user_id) {
        // $user_id = $user->id;

        $model = new UserProfessional();
        $model->user_id = $user_id;
        $model->save();
    }

}

if (!function_exists('front_pagination')) {

    function front_pagination($pagination_data, $records) {
        $firstRecord = $pagination_data->firstItem();
        $lastRecord = $pagination_data->lastItem();
        $totalRecords = $pagination_data->total();
        $show = '<span class="pxp-text-light">' . translate_it('Showing') . ' </span>';
        //$other = '<span class="pxp-text-light">'. translate_it($records) .' </span>';
        $other = '<span class="pxp-text-light">' . $records . ' </span>';

        $pagination = $show . ' ' . $firstRecord . ' - ' . ' ' . $lastRecord . ' ' . translate_it('of') . ' ' . ($totalRecords) . ' ' . $other;

        return $pagination;
    }

}

if (!function_exists('translate_it')) {

    function translate_it($str, $file = '') {
        $return_str = '';

        if ($file != '') {
            $str = $file . '.' . $str;
        }
        $str = trim($str);
        if ($str != '') {
            $return_str = __($str);
        }
        return $return_str;
    }

}

if (!function_exists('random_number')) {

    function random_number($chars = 11, $type = 0) {
        $min_chars = $chars;
        $max_chars = $chars;
        $use_chars = '';
        if ($type == 0)
            $use_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        elseif ($type == 1)
            $use_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($type == 2)
            $use_chars = '0123456789';

        $num_chars = rand($min_chars, $max_chars);
        $num_usable = strlen($use_chars) - 1;
        $string = '';
        for ($i = 0; $i < $num_chars; $i++) {
            $rand_char = rand(0, $num_usable);
            $string .= $use_chars[$rand_char];
        }
        return $string;
    }

}

if (!function_exists('asset_url')) {

    function asset_url($path, $secure = null) {
        //$path = '/assets/frontend/'.$path;
        //$url = app('url')->asset($path, $secure);
        $url = env('APP_URL') . '/assets/frontend/' . $path;
        return $url;
    }

}

if (!function_exists('uploads')) {

    function uploads($path, $secure = null) {
        $path = '/uploads/' . $path;
        $url = app('url')->asset($path, $secure);
        return $url;
    }

}

if (!function_exists('seeker_document')) {

    function seeker_document($path, $source, $secure = null) {
        $Auth_User = Auth::user();

        if ($source == 0) {
            $path = '/uploads/users/' . $path;
            $url = app('url')->asset($path, $secure);
        }
        else {
            $url = env('Portal_URL') . '/uploads/seekers/' . $Auth_User->refer_id . '/' . $path;
        }
        return $url;
    }

}

if (!function_exists('chat_asset_url')) {

    function chat_asset_url($path, $secure = null) {

        $path = '/assets/chat/' . $path;

        $url = env('APP_URL') . $path;

        return $url;
    }

}

if (!function_exists('portal_managed_url')) {

    function portal_managed_url($path, $secure = null) {
        $path = '/assets/managed/' . $path;
        $url = env('APP_URL') . $path;
        return $url;
    }

}

if (!function_exists('upload_url')) {

    function upload_url($path, $secure = null) {
        $path = '/uploads/' . $path;
        $url = env('APP_URL') . $path;
        return $url;
    }

}

if (!function_exists('createSlug')) {

    function createSlug($str, $delimiter = '-', $secure = null) {
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));

        return $slug;
    }

}

if (!function_exists('rephraseTime')) {

    function rephraseTime($time_value, $timestamp = 1) {
        if ($timestamp == 0) {
            $time_value = strtotime($time_value);
        }

        $current_time = time();
        $last_hour_time = ($current_time - (60 * 60));
        $last_day_time = strtotime(date('d-m-Y'));
        $last_year_time = strtotime(date('1-1-Y'));

        $str = '';
        if ($time_value > $last_hour_time) {
            $str = date('H:i', $time_value) . ' (' . calExpiryDay($time_value) . ')';
        }
        elseif ($time_value > $last_day_time) {
            $str = 'Today at ' . date('H:i', $time_value);
        }
        elseif ($time_value > $last_year_time) {
            $str = date('M d, H:i', $time_value);
        }
        else {
            $str = date('M d, Y H:i', $time_value);
        }
        return $str;
    }

}

if (!function_exists('calExpiryDay')) {

    function calExpiryDay($time, $ago = "ago") {
        $current = strtotime(date("Y-m-d H:i:s"));

        $time = ($current - $time); // to get the time since that moment

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

if (!function_exists('get_lang_field_data')) {

    function get_lang_field_data($Model, $field) {
        $str = '';
        $title_var = $field . '_' . trans('backLang.boxCode'); //en/fr
        $title_var2 = $field . '_' . trans('backLang.boxCodeOther'); //fr/en

        if ($Model->$title_var != "") {
            $str = $Model->$title_var;
        }
        else {
            $str = $Model->$title_var2;
        }
        return $str;
    }

}

if (!function_exists('key_exist')) {

    function key_exist($id, $data_array) {
        $field = false;
        if ($id != 0 && $id != null) {
            $field = (!empty($data_array[$id])) ? true : false;
        }
        return $field;
    }

}