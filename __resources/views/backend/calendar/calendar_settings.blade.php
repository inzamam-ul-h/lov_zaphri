<?php
$AUTH_USER = Auth::user();
$user_id = $AUTH_USER->id;
$cal_user_id = $user_id;

$available_from = 9;

$available_to = 21;

$sunday_sts = 0;

$monday_sts = 0;

$tuesday_sts = 0;

$wednesday_sts = 0;

$thursday_sts = 0;

$friday_sts = 0;

$saturday_sts = 0;

$date_format = '';

$time_format = '';

$time_zone = '';

if(!empty($UserCalendar)) {

    $available_from = $UserCalendar->available_from;

    $available_to = $UserCalendar->available_to;

    $sunday_sts = $UserCalendar->sunday_sts;

    $monday_sts = $UserCalendar->monday_sts;

    $tuesday_sts = $UserCalendar->tuesday_sts;

    $wednesday_sts = $UserCalendar->wednesday_sts;

    $thursday_sts = $UserCalendar->thursday_sts;

    $friday_sts = $UserCalendar->friday_sts;

    $saturday_sts = $UserCalendar->saturday_sts;

    $date_format = $UserCalendar->date_format;

    $time_format = $UserCalendar->time_format;

    $time_zone = $UserCalendar->time_zone;

    $time_zone = get_timezone_data('name', $time_zone);
}



if ($time_zone == '') {

    $time_zone = 'Asia/Karachi';
}



if ($date_format == 'MM/DD/YYYY') {
    
}
else {
    
}



$ampm = '';

$available_from_str = '';

$available_to_str = '';

$available_str = '';

if ($time_format == '24h') {

    $available_from_str = $available_from;

    $available_to_str = $available_to;
}
else {

    if ($available_from <= 11) {

        $ampm = 'AM';

        if ($available_from == 0) {

            $available_from = 12;
        }
    }
    else {

        $ampm = 'PM';

        if ($available_from > 12) {

            $available_from = ($available_from % 12);
        }
    }

    $available_from = sprintf('%02d', $available_from);

    $available_from_str = $available_from . ' ' . $ampm;

    if ($available_to <= 11) {

        $ampm = 'AM';

        if ($available_to == 0) {

            $available_to = 12;
        }
    }
    else {

        $ampm = 'PM';

        if ($available_to > 12) {

            $available_to = ($available_to % 12);
        }
    }

    $available_to = sprintf('%02d', $available_to);

    $available_to_str = $available_to . ' ' . $ampm;
}

$available_str = 'Available ' . $available_from_str . ' - ' . $available_to_str;
?>



<input type="hidden" id="cal_user_id" name="cal_user_id" value="<?php echo $cal_user_id; ?>" />



<input type="hidden" id="sunday_sts" name="sunday_sts" value="<?php echo $sunday_sts; ?>" />

<input type="hidden" id="monday_sts" name="monday_sts" value="<?php echo $monday_sts; ?>" />

<input type="hidden" id="tuesday_sts" name="tuesday_sts" value="<?php echo $tuesday_sts; ?>" />

<input type="hidden" id="wednesday_sts" name="wednesday_sts" value="<?php echo $wednesday_sts; ?>" />

<input type="hidden" id="thursday_sts" name="thursday_sts" value="<?php echo $thursday_sts; ?>" />

<input type="hidden" id="friday_sts" name="friday_sts" value="<?php echo $friday_sts; ?>" />

<input type="hidden" id="saturday_sts" name="saturday_sts" value="<?php echo $saturday_sts; ?>" />



<input type="hidden" id="time_zone" name="time_zone" value="<?php echo $time_zone; ?>" />



<input type="hidden" id="available_str" name="available_str" value="<?php echo $available_str; ?>" />



<input type="hidden" id="curr_day" name="curr_day" value="" />

<input type="hidden" id="curr_month" name="curr_month" value="" />

<input type="hidden" id="curr_year" name="curr_year" value="" />