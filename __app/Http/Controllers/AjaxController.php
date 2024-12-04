<?php

namespace App\Http\Controllers;

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
use App\Models\UserPersonal;
use App\Models\UserCalendar;
use App\Models\UserProfessional;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\SessionType;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;

class AjaxController extends MainController {

    public function calendar_calls(Request $request) {
        $AUTH_USER = Auth::user();
        $user_id = $AUTH_USER->id;
        $cal_user_id = $user_id;
        $status = $AUTH_USER->profile_status;
        $logged_in_user_id = $user_id;
        //set_user_timezone($logged_in_user_id);
        $SITE_URL = env('APP_URL');

        if (isset($request->type)) {
            $type = $request->type;

            if ($logged_in_user_id != 0) {
                $user_type = $AUTH_USER->user_type;
                $logged_in_type = $user_type;
                $cal_user_id = $request->user_id;

                switch ($type) {

                    case 'check_sessions': {
                            $exists = 0;
                            $current_time = time();
                            $session_start = time();
                            $session_end = (time() + (10 * 60));
                            $booking = Session::Join('bookings', 'sessions.id', '=', 'bookings.session_id');
                            $booking = $booking->select(['sessions.user_id', 'sessions.type', 'sessions.time_start', 'sessions.time_end', 'bookings.req_user_id']);
                            if ($user_type == $this->_COACH_USER) {
                                $booking = $booking->where('bookings.user_id', $user_id);
                            }
                            else {
                                $booking = $booking->where('bookings.req_user_id', $user_id);
                            }
                            $booking = $booking->where('sessions.time_start', '>=', $current_time)->where('sessions.time_start', '<=', $session_end)->where('bookings.status', 2);

                            $booking = $booking->first();
                            if (!empty($booking)) {
                                $exists = 1;

                                $booking_user_id = $booking->req_user_id;
                                $session_type = $aval_type = get_session_type($booking->type);
                                $session_user_id = stripslashes($booking->user_id);

                                $time_start = $booking->time_start;
                                $time_end = $booking->time_end;

                                $time_to_go = ($time_start - $current_time);
                                $time_mins = ($time_to_go / 60);
                                $time_secs = ($time_to_go % 60);
                                $time_secs = sprintf('%02d', $time_secs);
                                $remaining = "$time_mins:$time_secs";

                                $public_url = get_user_profile_data('meetinglink', $session_user_id);
                                ?>
                                <div class="check_modal_card">

                                    <div class="container">

                                        <div class="details" style=" width:90%; margin:0 auto;">

                                            <h3>Session Details:</h3>

                                            <?php if ($logged_in_type == 1) :
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-4">Player: </div>
                                                    <div class="col-sm-8">
                                                        <?php echo ucwords(get_user_name($booking_user_id)); ?>
                                                    </div>
                                                </div>
                                                <?php
                                            else:
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-4">Coach: </div>
                                                    <div class="col-sm-8">
                                                        <?php echo ucwords(get_user_name($session_user_id)); ?>
                                                    </div>
                                                </div>
                                            <?php
                                            endif;
                                            ?>

                                            <div class="row mt-10">
                                                <div class="col-sm-4">Session: </div>
                                                <div class="col-sm-8">
                                                    <?php echo $session_type; ?>
                                                </div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-sm-4">Date: </div>
                                                <div class="col-sm-8"><?php echo date('M d, Y', $time_start); ?></div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-sm-4">Time: </div>
                                                <div class="col-sm-8">
                                                    <?php echo date('h:i A', $time_start); ?>
                                                </div>
                                            </div>

                                            <div class="row mt-40">
                                                <div class="col-sm-12">                            
                                                    Test your audio video connections 
                                                    <span style="color:red">?</span>
                                                </div>
                                            </div>

                                            <div class="row mt-40 mb-10">
                                                <div class="col-sm-12">                                
                                                    <a href="<?php echo $public_url; ?>" class="btn btn-primary btn-join" target="_blank" title="Join Session NOW">JOIN NOW</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="count_down_timer" value="<?php echo $remaining; ?>" />
                                <?php
                            }
                            if ($exists == 0) {
                                echo $exists = 0;
                            }
                        }
                        break;

                    case 'add_availability': {
                            $proceed = 0;
                            $start_hour_time = 0;
                            $color = '';

                            $date_req = strtotime($request->date_str);
                            $date_req_full = date('Y-m-d', strtotime($request->date_str));

                            $current_date_full = date('Y-m-d');
                            $current = strtotime($current_date_full);

                            if ($current_date_full == $date_req_full) {
                                $proceed = 1;
                                $start_hour_time = date('H');
                            }
                            elseif ($date_req >= time()) {
                                $proceed = 1;
                            }

                            if ($proceed == 1):
                                $available_from = 9;
                                $available_to = 10;
                                $available_mins = 0;

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

                                $last_session_price = get_last_session_price($cal_user_id);

                                $UserCalendar = UserCalendar::where('user_id', $cal_user_id)->first();
                                if (!empty($UserCalendar)) {
                                    $row = $UserCalendar;
                                    $available_from = $row->available_from;
                                    $available_to = $row->available_to;

                                    $sunday_sts = $row->sunday_sts;
                                    $monday_sts = $row->monday_sts;
                                    $tuesday_sts = $row->tuesday_sts;
                                    $wednesday_sts = $row->wednesday_sts;
                                    $thursday_sts = $row->thursday_sts;
                                    $friday_sts = $row->friday_sts;
                                    $saturday_sts = $row->saturday_sts;

                                    $date_format = $row->date_format;
                                    $time_format = $row->time_format;
                                    $time_zone = $row->time_zone;
                                    $time_zone = get_timezone_data('name', $time_zone);
                                }

                                if ($time_zone == '') {
                                    $time_zone = 'Asia/Karachi';
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

                                $slot_exists = 0;
                                $date_req_start = strtotime(date('Y-m-d', $date_req));
                                $date_req_end = ($date_req_start + (24 * 60 * 60));
                                $rows = Session::where('user_id', $cal_user_id)->where('time_start', '>=', $date_req_start)->where('time_start', '<=', $date_req_end)->where('status', 1)->orderby('time_start', 'asc')->first();
                                if (!empty($rows)) {
                                    $slot_exists++;
                                }

                                $apply_to_recurring_day = 'All ' . date('l', $date_req) . 's Only'; //Apply to 


                                $display_date = date('d M Y', $date_req);

                                $apply_to_single_day = date('d M Y', $date_req) . ' Only'; //'Apply to '.
                                if ($date_format == 'MM/DD/YYYY') {
                                    $display_date = date('M d, Y', $date_req);
                                    $apply_to_single_day = date('M d Y', $date_req) . ' Only'; //'Apply to '.
                                } {
                                    if ($slot_exists > 0):
                                        ?>
                                        <div class="increment_times times_row_Avl">
                                            <?php
                                            $count = 0;
                                            $rows = Session::where('user_id', $cal_user_id)->where('time_start', '>=', $date_req_start)->where('time_start', '<=', $date_req_end)->where('status', 1)->orderby('time_start', 'asc')->get();
                                            if (!$rows->isEmpty()) {
                                                foreach ($rows as $row) {
                                                    $count++;

                                                    $disabled = 'disabled="disabled"';

                                                    $time_slot_start = date('h:i A', $row->time_start);
                                                    $time_slot_end = date('h:i A', $row->time_end);
                                                    if ($time_format == '24h') {
                                                        $time_slot_start = date('H:i', $row->time_start);
                                                        $time_slot_end = date('H:i', $row->time_end);
                                                    }
                                                    $session_color = $row->color;
                                                    $session_type = $row->type;
                                                    $session_type = get_session_type($session_type);

                                                    $session_id = $row->id;
                                                    $time_slot_string = '';
                                                    $slot_status = get_slot_status($session_id);
                                                    switch ($slot_status) {
                                                        case 0: echo 'Session Created';
                                                            break;
                                                        case 1: echo 'Session Booked';
                                                            break;
                                                        case 2: echo 'Session Confirmed';
                                                            break;
                                                        case 3: echo 'Session Booked & Canceled by Coach';
                                                            break;
                                                        case 4: echo 'Session Booked & Canceled by Player';
                                                            break;
                                                        case 5: echo 'Session Confirmed & Canceled by Coach';
                                                            break;
                                                        case 6: echo 'Session Confirmed & Canceled by Player';
                                                            break;
                                                        case 7: echo 'Session Feedback Pending';
                                                            break;
                                                        case 8: echo 'Session Delivered';
                                                            break;
                                                        case 9: echo 'Session Expired';
                                                            break;
                                                        case 10: echo 'Session Not Confirmed & Expired';
                                                            break;
                                                        default: echo 'others';
                                                            break;
                                                    }
                                                    ?>
                                                    <div class="row mt-10 <?php echo $session_color; ?>">

                                                        <label class="col-sm-5 control-label"><?php echo $count; ?>. <?php echo $session_type; ?></label>

                                                        <label class="col-sm-7 control-label"><?php echo $time_slot_string; ?> <?php echo $time_slot_start; ?> - <?php echo $time_slot_end; ?></label>

                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?> 
                                        </div>
                                        <?php
                                    endif; {
                                        $availability = 1;
                                        ?>
                                        <div class="increment_times times_row_Avl">

                                            <div class="row">

                                                <div class="col-lg-7 col-md-7 col-sm-6 smcs mt-10">

                                                    <span class="col-sm-2 smcs-label lebel-left">
                                                        <label class="control-label">Time</label>
                                                    </span>

                                                    <span class="col-sm-6 smcs-control">
                                                        <select class="form-control available_from" required="required" >
                                                            <?php
                                                            $ampm = 'AM';
                                                            for ($i = $start_hour_time; $i < 24; $i++) {
                                                                $hour = $i;
                                                                if ($i == 0) {
                                                                    $hour = 12;
                                                                }
                                                                elseif ($i == 12) {
                                                                    $ampm = 'PM';
                                                                }
                                                                elseif ($i > 12) {
                                                                    $hour = ($i % 12);
                                                                    $ampm = 'PM';
                                                                }
                                                                $hour = sprintf('%02d', $hour);
                                                                $from_str = "$hour $ampm";
                                                                ?>
                                                                <option value="<?php echo $i; ?>" <?php if ($available_from == $i) { ?> selected="selected" <?php } ?>>
                                                                    <?php echo $from_str; ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>

                                                    <span class="col-sm-4 smcs-control">
                                                        <select class="form-control available_mins" required="required">
                                                            <?php
                                                            for ($mins = 0; $mins <= 55; $mins++):
                                                                $mins_display = sprintf('%02d', $mins);
                                                                ?>
                                                                <option value="<?php echo $mins; ?>" <?php if ($available_mins == $mins) { ?> selected="selected" <?php } ?>>
                                                                    <?php echo $mins_display; ?>
                                                                </option>
                                                                <?php
                                                                $mins = ($mins + 4);
                                                            endfor;
                                                            ?>
                                                        </select>
                                                    </span>

                                                </div>

                                                <div class="col-lg-5 col-md-5 col-sm-6 smcs mt-10">

                                                    <span class="col-sm-4 smcs-label">
                                                        <label class="control-label">Type</label>
                                                    </span>

                                                    <span class="col-sm-8 smcs-control">
                                                        <select class="form-control" name="type" id="ses_type" required="" >
                                                            <?php
                                                            $type = 1;
                                                            $session_types = SessionType::where('id', '>', 0)->get();
                                                            foreach ($session_types as $session_type) {
                                                                ?>
                                                                <option value="<?php echo $session_type->id; ?>" <?php if ($type == $session_type->id) { ?> selected <?php } ?>>
                                                                    <?php echo $session_type->name; ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>                                            
                                                        </select>
                                                    </span>

                                                </div>

                                            </div>                                            

                                            <div class="row">

                                                <span class="col-lg-2 col-md-3 col-sm-4 smcs-label mt-10">
                                                    <label class="control-label">Price ($)</label>
                                                </span>

                                                <span class="col-lg-10 col-md-9 col-sm-8 smcs-control-full mt-10">
                                                    <input type="number" id="ses_price" class="form-control" name="price" value="<?php echo $last_session_price; ?>">                            
                                                </span>

                                            </div>                                           

                                            <div class="row">

                                                <span class="col-lg-2 col-md-3 col-sm-4 smcs-label mt-10">
                                                    <label class="control-label">Description</label>
                                                </span>

                                                <span class="col-lg-10 col-md-9 col-sm-8 smcs-control-full mt-10">
                                                    <textarea id="ses_description" class="form-control area-control" name="description" rows="3"></textarea>                            
                                                </span>

                                            </div>                                            

                                            <div class="row mt-10 hides">

                                                <label class="col-sm-2 control-label">Color</label>

                                                <div class="col-sm-10">

                                                    <ul class="colors-list js-color-list">

                                                        <?php
                                                        $dclass = 'cust_cl_1';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#ee5353';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_2';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#f778b4';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_3';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#e27eff';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_4';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#8989fc';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_5';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#4a91e9';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_6';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#0cc0d7';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_7';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#34c76e';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_8';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#67c820';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_9';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#dfc12d';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                        <?php
                                                        $dclass = 'cust_cl_10';
                                                        $class = 'colors_list_li';
                                                        $color_value = '#f49a31';
                                                        if ($color == $color_value) {
                                                            $class .= ' fa current';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>
                                                    </ul>


                                                </div>

                                            </div> 

                                        </div>

                                        <div class="row times_row_nAvl hide">
                                            <div class="col-sm-12 text-center">
                                                I am not Available
                                            </div>
                                        </div> 

                                        <div class="row mt-10 hide">
                                            <div class="col-sm-4">
                                                <button class="btn btn-success unavailable" type="button">
                                                    I am not Available
                                                </button>
                                                <button class="btn btn-success available hide" type="button">
                                                    I am Available
                                                </button>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?> 


                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            <hr />
                                        </div>
                                    </div>

                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            <strong>Apply To</strong>
                                        </div>
                                    </div>    

                                    <div class="row mt-10">
                                        <div class="col-sm-4 hide">
                                            <button class="btn btn-out apply_to_single_day btn-success" type="button">
                                                Single<?php //echo $apply_to_single_day;          ?>
                                            </button>
                                        </div>
                                        <div class="col-sm-4 hide">
                                            <button class="btn btn-out apply_to_recurring_day btn-success btn-outline" type="button">
                                                <?php echo $apply_to_recurring_day; ?>
                                            </button>
                                        </div>
                                        <div class="col-sm-4">
                                            <button class="btn btn-out apply_to_multiple_dates btn-success btn-outline" type="button">
                                                Recurrent
                                            </button>
                                        </div>
                                    </div>   

                                    <div class="row mt-10 select_week_months">                            
                                        <div class="col-sm-12">						
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_one_week btn-success" type="button">
                                                        1 Week Only
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_two_week btn-success btn-outline" type="button">
                                                        2 Weeks Only
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_one_month btn-success btn-outline" type="button">
                                                        1 Month Only
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_two_month btn-success btn-outline" type="button">
                                                        2 Months Only
                                                    </button>
                                                </div>
                                            </div>   
                                        </div>

                                    </div>   

                                    <div class="row mt-10 multiple_days">

                                        <div class="col-sm-12 mt-10">			
                                            <table width="100%">
                                                <tr>
                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $sunday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="sunday">Sun</td>

                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $monday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="monday">Mon</td>

                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $tuesday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="tuesday">Tue</td>

                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $wednesday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="wednesday">Wed</td>

                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $thursday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="thursday">Thu</td>

                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $friday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="friday">Fri</td>

                                                    <?php
                                                    $td_class = 'avldays_cell';
                                                    $day_sts = $saturday_sts;
                                                    if ($day_sts == 1) {
                                                        $td_class .= ' avldays_active';
                                                    }
                                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="saturday">Sat</td>
                                                </tr>
                                            </table>   
                                        </div>   

                                    </div>

                                    <div class="row mt-10">
                                        <div class="col-sm-12">

                                            <div id="set_avl_msg_success" class="msg-gr"></div>
                                            <div id="set_avl_msg_error" class="msg-rd mt-20"></div>
                                        </div>
                                    </div> 


                                    <input type="hidden" id="display_date" value="Create Session: <?php echo $display_date; ?>" />
                                    <input type="hidden" id="availability" value="<?php echo $availability; ?>" />
                                    <input type="hidden" id="days_selection" value="1" />
                                    <input type="hidden" id="w_m_selection" value="1M" />

                                    <input type="hidden" id="sunday_rec" value="<?php echo $sunday_sts; ?>" />
                                    <input type="hidden" id="monday_rec" value="<?php echo $monday_sts; ?>" />
                                    <input type="hidden" id="tuesday_rec" value="<?php echo $tuesday_sts; ?>" />
                                    <input type="hidden" id="wednesday_rec" value="<?php echo $wednesday_sts; ?>" />
                                    <input type="hidden" id="thursday_rec" value="<?php echo $thursday_sts; ?>" />
                                    <input type="hidden" id="friday_rec" value="<?php echo $friday_sts; ?>" />
                                    <input type="hidden" id="saturday_rec" value="<?php echo $saturday_sts; ?>" />

                                    <input type="hidden" id="selected_color" name="color" value="<?php echo $color; ?>" />
                                    <input type="hidden" id="slot_color" name="slot_color" value="<?php echo $color; ?>" />
                                    <?php
                                }
                            endif;
                            exit;
                        }
                        break;

                    case 'set_availability': {

                            $result = array();

                            $result['response_Code'] = "0";
                            $result['response_Status'] = "error";
                            $result['response_Text'] = "Please provide all valid information";

                            $error_messages = array();
                            $success = 0;

                            $date_selected = '';
                            if (isset($request->date_selected) && $request->date_selected != '') {

                                $date_selected = $request->date_selected;

                                $ses_type = 1;
                                if (isset($request->ses_type) && is_numeric($request->ses_type)) {
                                    $ses_type = $request->ses_type;
                                }

                                $ses_name = '';
                                if (isset($request->ses_name) && $request->ses_name != '') {
                                    $ses_name = addslashes(ltrim(rtrim($request->ses_name)));
                                }

                                $ses_description = '';
                                if (isset($request->ses_description) && $request->ses_description != '') {
                                    $ses_description = addslashes(ltrim(rtrim($request->ses_description)));
                                }


                                $ses_price = 0;
                                if (isset($request->ses_price) && is_numeric($request->ses_price)) {
                                    $ses_price = $request->ses_price;
                                }

                                $slot_color = 'cust_cl_10';
                                if (isset($request->slot_color) && $request->slot_color != '') {
                                    $slot_color = $request->slot_color;
                                }
                                //$slot_color = 'cust_cl_0';

                                $created_at = date('Y-m-d h:i:s', time());

                                $ses_type_id = $ses_type;

                                $days_selection = 1;
                                if (isset($request->days_selection) && is_numeric($request->days_selection)) {
                                    $days_selection = $request->days_selection;
                                }

                                $w_m_selection = '1W';
                                if (isset($request->w_m_selection) && $request->w_m_selection != '') {
                                    $w_m_selection = $request->w_m_selection;
                                }

                                $sunday_rec = 0;
                                $monday_rec = 0;
                                $tuesday_rec = 0;
                                $wednesday_rec = 0;
                                $thursday_rec = 0;
                                $friday_rec = 0;
                                $saturday_rec = 0;
                                if ($days_selection == 3) {
                                    if (isset($request->sunday_rec) && is_numeric($request->sunday_rec)) {
                                        $sunday_rec = $request->sunday_rec;
                                    }
                                    if (isset($request->monday_rec) && is_numeric($request->monday_rec)) {
                                        $monday_rec = $request->monday_rec;
                                    }
                                    if (isset($request->tuesday_rec) && is_numeric($request->tuesday_rec)) {
                                        $tuesday_rec = $request->tuesday_rec;
                                    }
                                    if (isset($request->wednesday_rec) && is_numeric($request->wednesday_rec)) {
                                        $wednesday_rec = $request->wednesday_rec;
                                    }
                                    if (isset($request->thursday_rec) && is_numeric($request->thursday_rec)) {
                                        $thursday_rec = $request->thursday_rec;
                                    }
                                    if (isset($request->friday_rec) && is_numeric($request->friday_rec)) {
                                        $friday_rec = $request->friday_rec;
                                    }
                                    if (isset($request->saturday_rec) && is_numeric($request->saturday_rec)) {
                                        $saturday_rec = $request->saturday_rec;
                                    }
                                }

                                $update = 1;
                                $availability = 1;
                                $mins_arr = 0; //array();
                                $from_arr = 0; //array();
                                if (isset($request->available_from) && $request->available_from != '') {
                                    $available_from = ltrim(rtrim($request->available_from));
                                    //$available_from = sprintf('%02d',$available_from);	
                                    $from_arr = $available_from;
                                }
                                if (isset($request->available_mins) && $request->available_mins != '') {
                                    $available_mins = ltrim(rtrim($request->available_mins));
                                    //$available_mins = sprintf('%02d',$available_mins);	
                                    $mins_arr = $available_mins;
                                }

                                $new_from_arr = array();
                                $available_from = $from_arr;
                                $available_to = ($available_from + 1);
                                $available_mins = $mins_arr;

                                for ($from = $available_from; $from < $available_to; $from++) {
                                    $new_from_arr[] = $from;
                                }
                                $from_arr = $new_from_arr;
                                $count_arr = count($from_arr);

                                $status = 0; //empty slot without availability
                                $update = 0; // update slot if exits

                                if (isset($request->availability) && is_numeric($request->availability)) {
                                    $availability = $request->availability;
                                }
                                $status = $availability;

                                $created_at = time();
                                $modified_at = time();

                                $timestamp_selected = strtotime($date_selected);

                                $year_selected = date('Y', $timestamp_selected);
                                $month_selected = date('m', $timestamp_selected);
                                $day_selected = date('d', $timestamp_selected);
                                $full_day_selected = date('l', $timestamp_selected);

                                $start_day_of_month = 1;
                                $last_day_of_month = date('t', $timestamp_selected);

                                // if user is not available
                                if ($availability == 0) {
                                    switch ($days_selection) {
                                        case 1:// update selected date availability
                                            {
                                                for ($j = 0; $j < 24; $j++) {
                                                    $x = ($j + 1);
                                                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $day_selected, $j, $x, $status, $update, $available_mins);
                                                    $date_ = date("Y-m-d", strtotime("$year_selected-$month_selected-$day_selected $j:00:00"));
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
                                            break;

                                        case 2:// update recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 6;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 13;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        case 3:// update multiple recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 6;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 13;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        default:
                                            break;
                                    }
                                }
                                // if user is available
                                elseif ($availability == 1) {
                                    switch ($days_selection) {
                                        case 1:// update selected date availability
                                            {
                                                for ($b = 0; $b < $count_arr; $b++) {
                                                    $j = $from_arr[$b];
                                                    $x = ($j + 1);
                                                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $day_selected, $j, $x, $status, $update, $available_mins);
                                                    $date_ = date("Y-m-d", strtotime("$year_selected-$month_selected-$day_selected $j:00:00"));
                                                    if ($bool == 1) {
                                                        $success = 1;
                                                        /* $message = array();
                                                          $message['status'] = 1;
                                                          $message['text'] = "Updated non availability to available on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>";
                                                          $success_messages[] = $message; */
                                                    }
                                                }
                                            }
                                            break;

                                        case 2:// update recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 7;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = (($limit_end - $limit_start) + 1);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 14;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = (($limit_end - $limit_start) + 1);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        case 3:// update multiple recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 6;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 13;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        default:
                                            break;
                                    }
                                }


                                $result['response_Code'] = "1";
                                $result['response_Status'] = "success";
                                $result['response_Text'] = "Availability saved";
                            }

                            $success_messages = [];
                            if ($success == 1) {
                                $message = array();
                                $message['status'] = 1;
                                $message['text'] = "Successfully saved availability";
                                $success_messages[] = $message;
                            }

                            $result['success_messages'] = $success_messages;
                            $result['count_success'] = count($success_messages);
                            $result['error_messages'] = $error_messages;
                            $result['count_error'] = count($error_messages);

                            echo json_encode($result);

                            exit;
                        }
                        break;

                    case 'change_availability': {
                            $slot_id = $request->slot_id;

                            $date_req = 0;
                            $row = Session::where('id', $slot_id)->where('user_id', $cal_user_id)->first();
                            if (!empty($row)) {
                                $row->time_start = strtotime(date('Y-m-d H:i:s', $row->time_start));
                                $row->time_end = strtotime(date('Y-m-d H:i:s', $row->time_end));
                                $date_req = date('Y-m-d', $row->time_start);
                            }
                            $modal_display_date = $date_req;
                            $date_req = strtotime($date_req);

                            $current = strtotime(date('Y-m-d'));

                            //if($date_req > time())
                            {

                                $available_from = 9;
                                $available_to = 10;
                                $available_mins = 0;

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

                                $last_session_price = get_last_session_price($cal_user_id);

                                $UserCalendar = UserCalendar::where('user_id', $cal_user_id)->first();
                                if (!empty($UserCalendar)) {
                                    $row = $UserCalendar;
                                    $available_from = $row->available_from;
                                    $available_to = $row->available_to;

                                    $sunday_sts = $row->sunday_sts;
                                    $monday_sts = $row->monday_sts;
                                    $tuesday_sts = $row->tuesday_sts;
                                    $wednesday_sts = $row->wednesday_sts;
                                    $thursday_sts = $row->thursday_sts;
                                    $friday_sts = $row->friday_sts;
                                    $saturday_sts = $row->saturday_sts;

                                    $date_format = $row->date_format;
                                    $time_format = $row->time_format;
                                    $time_zone = $row->time_zone;
                                    $time_zone = get_timezone_data('name', $time_zone);
                                }

                                if ($time_zone == '') {
                                    $time_zone = 'Asia/Karachi';
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

                                $slot_exists = 0;
                                $date_req_start = strtotime(date('Y-m-d', $date_req));
                                $date_req_end = ($date_req_start + (24 * 60 * 60));
                                $row = Session::where('id', '<>', $slot_id)->where('user_id', $cal_user_id)->where('time_start', '>=', $date_req_start)->where('time_start', '<=', $date_req_end)->first();
                                if (!empty($row)) {
                                    $slot_exists++;
                                }

                                $apply_to_recurring_day = 'All ' . date('l', $date_req) . 's Only'; //Apply to 


                                $display_date = date('d M Y', $date_req);

                                $apply_to_single_day = date('d M Y', $date_req) . ' Only'; //'Apply to '.
                                if ($date_format == 'MM/DD/YYYY') {
                                    $display_date = date('M d, Y', $date_req);
                                    $apply_to_single_day = date('M d Y', $date_req) . ' Only'; //'Apply to '.
                                } {
                                    if ($slot_exists > 0) {
                                        ?>
                                        <div class="increment_times times_row_Avl">
                                            <?php
                                            $count = 0;
                                            $rows = Session::where('id', '<>', $slot_id)->where('user_id', $cal_user_id)->where('time_start', '>=', $date_req_start)->where('time_start', '<=', $date_req_end)->where('status', 1)->orderby('time_start', 'asc')->get();
                                            if (!$rows->isEmpty()) {
                                                foreach ($rows as $row) {
                                                    $count++;
                                                    $row->time_start = strtotime(date('Y-m-d H:i:s', $row->time_start));
                                                    $row->time_end = strtotime(date('Y-m-d H:i:s', $row->time_end));

                                                    $disabled = 'disabled="disabled"';

                                                    $time_slot_start = date('h:i A', $row->time_start);
                                                    $time_slot_end = date('h:i A', $row->time_end);
                                                    if ($time_format == '24h') {
                                                        $time_slot_start = date('H:i', $row->time_start);
                                                        $time_slot_end = date('H:i', $row->time_end);
                                                    }

                                                    $session_type = get_session_type($row->type);
                                                    $session_status = $row->status;
                                                    $session_color = $row->color;

                                                    $session_id = $row->id;
                                                    $time_slot_string = '';
                                                    $slot_status = get_slot_status($session_id);
                                                    switch ($slot_status) {
                                                        case 0: echo 'Session Created';
                                                            break;
                                                        case 1: echo 'Session Booked';
                                                            break;
                                                        case 2: echo 'Session Confirmed';
                                                            break;
                                                        case 3: echo 'Session Booked & Canceled by Coach';
                                                            break;
                                                        case 4: echo 'Session Booked & Canceled by Player';
                                                            break;
                                                        case 5: echo 'Session Confirmed & Canceled by Coach';
                                                            break;
                                                        case 6: echo 'Session Confirmed & Canceled by Player';
                                                            break;
                                                        case 7: echo 'Session Feedback Pending';
                                                            break;
                                                        case 8: echo 'Session Delivered';
                                                            break;
                                                        case 9: echo 'Session Expired';
                                                            break;
                                                        case 10: echo 'Session Not Confirmed & Expired';
                                                            break;
                                                        default: echo 'others';
                                                            break;
                                                    }
                                                    ?>
                                                    <div class="row mt-10 <?php echo $session_color; ?>">

                                                        <label class="col-sm-5 control-label"><?php echo $count; ?>. <?php echo $session_type; ?></label>

                                                        <label class="col-sm-7 control-label"><?php echo $time_slot_string; ?> <?php echo $time_slot_start; ?> - <?php echo $time_slot_end; ?></label>

                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?> 
                                        </div>
                                        <?php
                                    }


                                    $rows_av = Session::where('id', $slot_id)->where('user_id', $cal_user_id)->orderby('time_start', 'asc')->get();
                                    if (!$rows_av->isEmpty()) {
                                        foreach ($rows_av as $row_av) {
                                            $row_av->time_start = strtotime(date('Y-m-d H:i:s', $row_av->time_start));
                                            $row_av->time_end = strtotime(date('Y-m-d H:i:s', $row_av->time_end));
                                            $date_req = date('Y-m-d', $row_av->time_start);
                                            $start_hour = date('H', $row_av->time_start);
                                            $start_min = date('i', $row_av->time_start);
                                            $color = $row_av->color;
                                            $_price = $row_av->price;
                                            $_type = $row_av->type;
                                            $_description = $row_av->description; {
                                                $availability = 1;
                                                ?>
                                                <div class="increment_times times_row_Avl">

                                                    <div class="row">

                                                        <div class="col-lg-7 col-md-7 col-sm-6 smcs mt-10">

                                                            <span class="col-sm-3 smcs-label lebel-left">
                                                                <label class="control-label">Time</label>
                                                            </span>

                                                            <span class="col-sm-5 smcs-control">
                                                                <select class="form-control available_from" required="required" >
                                                <?php
                                                $ampm = 'AM';
                                                for ($i = 0; $i < 24; $i++) {
                                                    $hour = $i;
                                                    if ($i == 0) {
                                                        $hour = 12;
                                                    }
                                                    elseif ($i == 12) {
                                                        $ampm = 'PM';
                                                    }
                                                    elseif ($i > 12) {
                                                        $hour = ($i % 12);
                                                        $ampm = 'PM';
                                                    }
                                                    $hour = sprintf('%02d', $hour);
                                                    $from_str = "$hour $ampm";
                                                    ?>
                                                                        <option value="<?php echo $i; ?>" <?php if ($start_hour == $i) { ?> selected="selected" <?php } ?>>
                                                                        <?php echo $from_str; ?>
                                                                        </option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                </select>
                                                            </span>

                                                            <span class="col-sm-4 smcs-control">
                                                                <select class="form-control available_mins" required="required">
                                                <?php
                                                for ($mins = 0; $mins <= 55; $mins++) {
                                                    $mins_display = sprintf('%02d', $mins);
                                                    ?>
                                                                        <option value="<?php echo $mins; ?>" <?php if ($start_min == $mins) { ?> selected="selected" <?php } ?>>
                                                                        <?php echo $mins_display; ?>
                                                                        </option>
                                                                            <?php
                                                                            $mins = ($mins + 4);
                                                                        }
                                                                        ?>
                                                                </select>
                                                            </span>

                                                        </div>

                                                        <div class="col-lg-5 col-md-5 col-sm-6 smcs mt-10">

                                                            <span class="col-sm-4 smcs-label">
                                                                <label class="control-label">Type</label>
                                                            </span>

                                                            <span class="col-sm-8 smcs-control">
                                                                <select class="form-control" name="type" id="ses_type" required="" >
                                                <?php
                                                $type = $_type;
                                                $session_types = SessionType::where('id', '>', 0)->get();
                                                foreach ($session_types as $session_type) {
                                                    ?>
                                                                        <option value="<?php echo $session_type->id; ?>" <?php if ($type == $session_type->id) { ?> selected <?php } ?>>
                                                                        <?php echo $session_type->name; ?>
                                                                        </option>
                                                                            <?php
                                                                        }
                                                                        ?>                                            
                                                                </select>
                                                            </span>

                                                        </div>

                                                    </div>                                            

                                                    <div class="row">

                                                        <span class="col-lg-2 col-md-3 col-sm-4 smcs-label mt-10">
                                                            <label class="control-label">Price ($)</label>
                                                        </span>

                                                        <span class="col-lg-10 col-md-9 col-sm-8 smcs-control-full mt-10">
                                                            <input type="number" id="ses_price" class="form-control" name="price" value="<?php echo $_price; ?>">                            
                                                        </span>

                                                    </div>                                           

                                                    <div class="row">

                                                        <span class="col-lg-2 col-md-3 col-sm-4 smcs-label mt-10">
                                                            <label class="control-label">Description</label>
                                                        </span>

                                                        <span class="col-lg-10 col-md-9 col-sm-8 smcs-control-full mt-10">
                                                            <textarea id="ses_description" class="form-control area-control" name="description" rows="3"><?php echo $_description; ?></textarea>                            
                                                        </span>

                                                    </div>                                            

                                                    <div class="row mt-10 hides">

                                                        <label class="col-sm-2 control-label">Color</label>

                                                        <div class="col-sm-10">

                                                            <ul class="colors-list js-color-list">

                                                <?php
                                                $dclass = 'cust_cl_1';
                                                $class = 'colors_list_li';
                                                $color_value = '#ee5353';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_2';
                                                $class = 'colors_list_li';
                                                $color_value = '#f778b4';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_3';
                                                $class = 'colors_list_li';
                                                $color_value = '#e27eff';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_4';
                                                $class = 'colors_list_li';
                                                $color_value = '#8989fc';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_5';
                                                $class = 'colors_list_li';
                                                $color_value = '#4a91e9';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_6';
                                                $class = 'colors_list_li';
                                                $color_value = '#0cc0d7';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_7';
                                                $class = 'colors_list_li';
                                                $color_value = '#34c76e';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_8';
                                                $class = 'colors_list_li';
                                                $color_value = '#67c820';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_9';
                                                $class = 'colors_list_li';
                                                $color_value = '#dfc12d';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>

                                                <?php
                                                $dclass = 'cust_cl_10';
                                                $class = 'colors_list_li';
                                                $color_value = '#f49a31';
                                                if ($color == $color_value) {
                                                    $class .= ' fa current';
                                                }
                                                ?>
                                                                <li class="<?php echo $class; ?>" style="background-color: <?php echo $color_value; ?>" data-color="<?php echo $color_value; ?>" data-class="<?php echo $dclass; ?>"></li>
                                                            </ul>


                                                        </div>

                                                    </div> 

                                                </div>

                                                <div class="row times_row_nAvl hide">
                                                    <div class="col-sm-12 text-center">
                                                        I am not Available
                                                    </div>
                                                </div> 

                                                <div class="row mt-10 hide">
                                                    <div class="col-sm-4">
                                                        <button class="btn btn-success unavailable" type="button">
                                                            I am not Available
                                                        </button>
                                                        <button class="btn btn-success available hide" type="button">
                                                            I am Available
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?> 


                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            <hr />
                                        </div>
                                    </div>

                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            <strong>Apply To</strong>
                                        </div>
                                    </div>    

                                    <div class="row mt-10">
                                        <div class="col-sm-4 hide">
                                            <button class="btn btn-out apply_to_single_day btn-success" type="button">
                                                Single<?php //echo $apply_to_single_day;          ?>
                                            </button>
                                        </div>
                                        <div class="col-sm-4 hide">
                                            <button class="btn btn-out apply_to_recurring_day btn-success btn-outline" type="button">
                                    <?php echo $apply_to_recurring_day; ?>
                                            </button>
                                        </div>
                                        <div class="col-sm-4">
                                            <button class="btn btn-out apply_to_multiple_dates btn-success btn-outline" type="button">
                                                Recurrent
                                            </button>
                                        </div>
                                    </div>   

                                    <div class="row mt-10 select_week_months">                            
                                        <div class="col-sm-12">						
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_one_week btn-success" type="button">
                                                        1 Week Only
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_two_week btn-success btn-outline" type="button">
                                                        2 Weeks Only
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_one_month btn-success btn-outline" type="button">
                                                        1 Month Only
                                                    </button>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button class="btn btn-wm apply_to_two_month btn-success btn-outline" type="button">
                                                        2 Months Only
                                                    </button>
                                                </div>
                                            </div>   
                                        </div>

                                    </div>   

                                    <div class="row mt-10 multiple_days">

                                        <div class="col-sm-12 mt-10">			
                                            <table width="100%">
                                                <tr>
                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $sunday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="sunday">Sun</td>

                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $monday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="monday">Mon</td>

                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $tuesday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="tuesday">Tue</td>

                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $wednesday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="wednesday">Wed</td>

                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $thursday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="thursday">Thu</td>

                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $friday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="friday">Fri</td>

                                    <?php
                                    $td_class = 'avldays_cell';
                                    $day_sts = $saturday_sts;
                                    if ($day_sts == 1) {
                                        $td_class .= ' avldays_active';
                                    }
                                    ?>
                                                    <td class="<?php echo $td_class ?>" data_day_attr="saturday">Sat</td>
                                                </tr>
                                            </table>   
                                        </div>   

                                    </div>

                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            <div id="set_avl_msg_success" class="msg-gr"></div>
                                            <div id="set_avl_msg_error" class="msg-rd mt-20"></div>
                                        </div>
                                    </div>

                                    <div class="row mt-10 row_delete hide">
                                        <div class="col-sm-12">
                                            <strong>Are you sure you want to delete this session?</strong>
                                        </div>
                                        <div class="col-sm-12">
                                            <strong>
                                                <a class="btn btn-danger" href="<?php echo route('delete_availability', $slot_id); ?>">
                                                    Yes
                                                </a>
                                            </strong>
                                        </div>
                                    </div>  


                                    <input type="hidden" id="display_date" value="Update Session: <?php echo $display_date; ?>" />
                                    <input type="hidden" id="availability" value="<?php echo $availability; ?>" />
                                    <input type="hidden" id="days_selection" value="1" />
                                    <input type="hidden" id="w_m_selection" value="1M" />

                                    <input type="hidden" id="sunday_rec" value="<?php echo $sunday_sts; ?>" />
                                    <input type="hidden" id="monday_rec" value="<?php echo $monday_sts; ?>" />
                                    <input type="hidden" id="tuesday_rec" value="<?php echo $tuesday_sts; ?>" />
                                    <input type="hidden" id="wednesday_rec" value="<?php echo $wednesday_sts; ?>" />
                                    <input type="hidden" id="thursday_rec" value="<?php echo $thursday_sts; ?>" />
                                    <input type="hidden" id="friday_rec" value="<?php echo $friday_sts; ?>" />
                                    <input type="hidden" id="saturday_rec" value="<?php echo $saturday_sts; ?>" />

                                    <input type="hidden" id="selected_color" name="color" value="<?php echo $color; ?>" />
                                    <input type="hidden" id="slot_color" name="slot_color" value="<?php echo $color; ?>" />
                                    <input type="hidden" id="modal_display_date" name="modal_display_date" value="<?php echo $modal_display_date; ?>" />
                                    <?php
                                }
                            }
                            exit;
                        }
                        break;

                    case 'update_availability': {

                            $result = array();

                            $result['response_Code'] = "0";
                            $result['response_Status'] = "error";
                            $result['response_Text'] = "Please provide all valid information";

                            $error_messages = array();
                            $success = 0;

                            $date_selected = '';
                            if (isset($request->date_selected) && $request->date_selected != '' && isset($request->slot_id) && $request->slot_id != '') {

                                $date_selected = $request->date_selected;

                                $slot_id = $request->slot_id;
                                $res = Session::where('id', $slot_id)->where('user_id', $cal_user_id)->orderby('id', 'desc')->first();
                                if (!empty($res)) {
                                    $session = Session::find($slot_id);
                                    $session->delete();
                                }

                                $ses_type = 1;
                                if (isset($request->ses_type) && is_numeric($request->ses_type)) {
                                    $ses_type = $request->ses_type;
                                }

                                $ses_name = '';
                                if (isset($request->ses_name) && $request->ses_name != '') {
                                    $ses_name = addslashes(ltrim(rtrim($request->ses_name)));
                                }

                                $ses_description = '';
                                if (isset($request->ses_description) && $request->ses_description != '') {
                                    $ses_description = addslashes(ltrim(rtrim($request->ses_description)));
                                }


                                $ses_price = 0;
                                if (isset($request->ses_price) && is_numeric($request->ses_price)) {
                                    $ses_price = $request->ses_price;
                                }

                                $slot_color = 'cust_cl_10';
                                if (isset($request->slot_color) && $request->slot_color != '') {
                                    $slot_color = $request->slot_color;
                                }
                                //$slot_color = 'cust_cl_0';

                                $created_at = date('Y-m-d h:i:s', time());

                                $ses_type_id = $ses_type;

                                $days_selection = 1;
                                if (isset($request->days_selection) && is_numeric($request->days_selection)) {
                                    $days_selection = $request->days_selection;
                                }

                                $w_m_selection = '1W';
                                if (isset($request->w_m_selection) && $request->w_m_selection != '') {
                                    $w_m_selection = $request->w_m_selection;
                                }

                                $sunday_rec = 0;
                                $monday_rec = 0;
                                $tuesday_rec = 0;
                                $wednesday_rec = 0;
                                $thursday_rec = 0;
                                $friday_rec = 0;
                                $saturday_rec = 0;
                                if ($days_selection == 3) {
                                    if (isset($request->sunday_rec) && is_numeric($request->sunday_rec)) {
                                        $sunday_rec = $request->sunday_rec;
                                    }
                                    if (isset($request->monday_rec) && is_numeric($request->monday_rec)) {
                                        $monday_rec = $request->monday_rec;
                                    }
                                    if (isset($request->tuesday_rec) && is_numeric($request->tuesday_rec)) {
                                        $tuesday_rec = $request->tuesday_rec;
                                    }
                                    if (isset($request->wednesday_rec) && is_numeric($request->wednesday_rec)) {
                                        $wednesday_rec = $request->wednesday_rec;
                                    }
                                    if (isset($request->thursday_rec) && is_numeric($request->thursday_rec)) {
                                        $thursday_rec = $request->thursday_rec;
                                    }
                                    if (isset($request->friday_rec) && is_numeric($request->friday_rec)) {
                                        $friday_rec = $request->friday_rec;
                                    }
                                    if (isset($request->saturday_rec) && is_numeric($request->saturday_rec)) {
                                        $saturday_rec = $request->saturday_rec;
                                    }
                                }

                                $update = 1;
                                $availability = 1;
                                $mins_arr = 0; //array();
                                $from_arr = 0; //array();
                                if (isset($request->available_from) && $request->available_from != '') {
                                    $available_from = ltrim(rtrim($request->available_from));
                                    //$available_from = sprintf('%02d',$available_from);	
                                    $from_arr = $available_from;
                                }
                                if (isset($request->available_mins) && $request->available_mins != '') {
                                    $available_mins = ltrim(rtrim($request->available_mins));
                                    //$available_mins = sprintf('%02d',$available_mins);	
                                    $mins_arr = $available_mins;
                                }

                                $new_from_arr = array();
                                $available_from = $from_arr;
                                $available_to = ($available_from + 1);
                                $available_mins = $mins_arr;

                                for ($from = $available_from; $from < $available_to; $from++) {
                                    $new_from_arr[] = $from;
                                }
                                $from_arr = $new_from_arr;
                                $count_arr = count($from_arr);

                                $status = 0; //empty slot without availability
                                $update = 0; // update slot if exits

                                if (isset($request->availability) && is_numeric($request->availability)) {
                                    $availability = $request->availability;
                                }
                                $status = $availability;

                                $created_at = time();
                                $modified_at = time();

                                $timestamp_selected = strtotime($date_selected);

                                $year_selected = date('Y', $timestamp_selected);
                                $month_selected = date('m', $timestamp_selected);
                                $day_selected = date('d', $timestamp_selected);
                                $full_day_selected = date('l', $timestamp_selected);

                                $start_day_of_month = 1;
                                $last_day_of_month = date('t', $timestamp_selected);

                                // if user is not available
                                if ($availability == 0) {
                                    switch ($days_selection) {
                                        case 1:// update selected date availability
                                            {
                                                for ($j = 0; $j < 24; $j++) {
                                                    $x = ($j + 1);
                                                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $day_selected, $j, $x, $status, $update, $available_mins);
                                                    $date_ = date("Y-m-d", strtotime("$year_selected-$month_selected-$day_selected $j:00:00"));
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
                                            break;

                                        case 2:// update recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 6;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 13;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins);
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        case 3:// update multiple recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 6;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 13;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_0_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        default:
                                            break;
                                    }
                                }
                                // if user is available
                                elseif ($availability == 1) {
                                    switch ($days_selection) {
                                        case 1:// update selected date availability
                                            {
                                                for ($b = 0; $b < $count_arr; $b++) {
                                                    $j = $from_arr[$b];
                                                    $x = ($j + 1);
                                                    $bool = set_time_slot($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $year_selected, $month_selected, $day_selected, $j, $x, $status, $update, $available_mins);
                                                    $date_ = date("Y-m-d", strtotime("$year_selected-$month_selected-$day_selected $j:00:00"));
                                                    if ($bool == 1) {
                                                        $success = 1;
                                                        /* $message = array();
                                                          $message['status'] = 1;
                                                          $message['text'] = "Updated non availability to available on <strong>$date_</strong> from <strong>$j:00</strong> to <strong>$x:00</strong>";
                                                          $success_messages[] = $message; */
                                                    }
                                                }
                                            }
                                            break;

                                        case 2:// update recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 7;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = (($limit_end - $limit_start) + 1);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 14;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = (($limit_end - $limit_start) + 1);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                            ;

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $full_day_selected, $status, $update, $available_mins, $count_arr, $from_arr);
                                                                ;
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        case 3:// update multiple recurring days availability
                                            {
                                                switch ($w_m_selection) {
                                                    case '1W': {
                                                            //for current month
                                                            $duration = 6;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2W': {
                                                            //for current month 
                                                            $duration = 13;
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $diff = ($limit_end - $limit_start);
                                                            $next_month = 0;
                                                            if ($diff < $duration) {
                                                                $next_month = 1;
                                                            }
                                                            else {
                                                                $limit_end = ($limit_start + $duration);
                                                            }
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = ($duration - $diff);
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '1M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    case '2M': {
                                                            //for current month
                                                            $limit_start = $day_selected;
                                                            $limit_end = $last_day_of_month;
                                                            $next_month = 1;
                                                            cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);

                                                            //for next month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $last_day_of_month;
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }


                                                            //for other month
                                                            if ($next_month == 1) {
                                                                $timestamp_selected = strtotime("+1 month", $timestamp_selected);

                                                                $year_selected = date('Y', $timestamp_selected);
                                                                $month_selected = date('m', $timestamp_selected);
                                                                $day_selected = date('d', $timestamp_selected);

                                                                $start_day_of_month = 1;
                                                                $last_day_of_month = date('t', $timestamp_selected);

                                                                $limit_start = $start_day_of_month;
                                                                $limit_end = $day_selected;
                                                                cal_availability_1_recurring($cal_user_id, $ses_type_id, $ses_price, $ses_description, $slot_color, $limit_start, $limit_end, $year_selected, $month_selected, $status, $update, $available_mins, $count_arr, $from_arr, $sunday_rec, $monday_rec, $tuesday_rec, $wednesday_rec, $thursday_rec, $friday_rec, $saturday_rec);
                                                            }
                                                        }
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                            break;

                                        default:
                                            break;
                                    }
                                }


                                $result['response_Code'] = "1";
                                $result['response_Status'] = "success";
                                $result['response_Text'] = "Availability saved";
                            }

                            $success_messages = [];
                            if ($success == 1) {
                                $message = array();
                                $message['status'] = 1;
                                $message['text'] = "Successfully saved availability";
                                $success_messages[] = $message;
                            }

                            $result['success_messages'] = $success_messages;
                            $result['count_success'] = count($success_messages);
                            $result['error_messages'] = $error_messages;
                            $result['count_error'] = count($error_messages);

                            echo json_encode($result);

                            exit;
                        }
                        break;

                    case 'update_slot_selection': {

                            $new_array = array();
                            $new_array[] = 0;

                            $id = $request->id;
                            $exists = $request->exists;
                            $selected_slots = $request->selected_slots;
                            if (!empty($selected_slots)) {
                                $selected_slots = explode(',', $selected_slots);
                                foreach ($selected_slots as $slot) {
                                    if ($slot != $id && $slot != '') {
                                        $new_array[] = $slot;
                                    }
                                }
                                if ($exists == 0) {
                                    $new_array[] = $id;
                                }
                            }
                            else {
                                $new_array[] = $id;
                            }
                            $result = implode(',', $new_array);
                            $result = str_replace(',,', ',', $result);
                            $result = str_replace('0,0,', '', $result);

                            $current_time = strtotime(date('Y-m-d H:i'));
                            $current = strtotime(date('Y-m-d'));
                            $next_day = ($current + (24 * 60 * 60));
                            $next_month = ($next_day + (30 * 24 * 60 * 60));

                            $selected_slots_str = $result;
                            if ($selected_slots_str != '' && $selected_slots_str != '0') {// && $logged_in_user_id != $cal_user_id
                                $requested_array = array();

                                $selected_slots = explode(',', $selected_slots_str);
                                sort($selected_slots); { {
                                        $in = 0;
                                        $rows = Session::whereIn('id', $selected_slots)->where('time_start', '>=', $current_time)->orderby('time_start', 'asc')->get();
                                        if (count($rows)) {
                                            foreach ($rows as $row) {
                                                $in = 1;
                                                $session_id = $row->id;
                                                $bool = get_slot_availability($session_id);
                                                if ($bool == 0) {
                                                    $requested_array[$session_id] = 1;
                                                }
                                                else {
                                                    $requested_array[$session_id] = 2;
                                                }
                                            }
                                        }
                                    }
                                } {
                                    ?>
                                    <div id="selected_slots_ajax" class="hide"><?php echo $selected_slots_str; ?></div>
                                    <div class="increment_times">
                                    <?php
                                    $checks = 0;
                                    $count = 0;
                                    $price_total = 0;
                                    foreach ($requested_array as $session_id => $status) {
                                        switch ($status) {
                                            case 1: {
                                                    $row = Session::find($session_id);
                                                    if (!empty($row)) {
                                                        $session_id = $row->id;

                                                        $time_start = $row->time_start;
                                                        $time_end = $row->time_end;

                                                        $session_type_id = $row->type;
                                                        $session_type = get_session_type($session_type_id);
                                                        $session_price = $row->price;
                                                        $session_status = $row->status;
                                                        $session_color = $row->color;

                                                        $slot_user_id = $row->user_id;
                                                        $slot_user_name = get_user_name($slot_user_id);
                                                        $slot_user_name = get_user_data('public_url', $slot_user_id);

                                                        $bool = get_slot_user_availability($session_id, $logged_in_user_id);
                                                        switch ($bool) {
                                                            case 1: {
                                                                    $checks++;
                                                                    $price_total += $session_price;
                                                                    ?>
                                                                        <div class="row times_row mt-5" id="rem_slot_row_<?php echo $session_id; ?>">
                                                                            <div class="col-sm-7 col-xs-12">																							
                                                                                <label class="control-label">
                                                                        <?php echo date('M d, Y h:i A', $time_start); ?> - <?php echo date('h:i A', $time_end); ?> 
                                                                                </label>																						
                                                                                <label class="control-label">
                                                                                    <i><?php echo $session_type; ?></i> by <i><?php echo $slot_user_name; ?></i>
                                                                                </label>																					
                                                                                <label class="control-label">
                                                                                    at <i><?php echo $session_price; ?></i>
                                                                                </label>
                                                                                <input type="checkbox" name="slots[]" class="avl_slots hide" value="<?php echo $session_id; ?>" checked="checked" />
                                                                            </div>
                                                                            <div class="col-sm-3 col-xs-9">
                                                                    <?php
                                                                    if ($session_type_id > 1) {
                                                                        ?>
                                                                                    <input type="hidden" id="ses_type_<?php echo $session_id; ?>" value="<?php echo $session_type_id; ?>" />
                                                                                    <select class="form-control select_type_id" name="type" <?php if ($session_type_id > 1) { ?> disabled="disabled"<?php } ?>>
                                                                                    <?php
                                                                                    $session_types = SessionType::where('id', '>', 0)->get();
                                                                                    foreach ($session_types as $session_type) {
                                                                                        ?>
                                                                                            <option value="<?php echo $session_type->id; ?>" <?php if ($session_type_id == $session_type->id) { ?> selected <?php } ?>>
                                                                                            <?php echo $session_type->name; ?>
                                                                                            </option>
                                                                                            <?php
                                                                                        }
                                                                                        ?>                                            
                                                                                    </select>
                                                                                            <?php
                                                                                        }
                                                                                        else {
                                                                                            ?>
                                                                                    <p>Please Select Skill You Want To Improve</p>
                                                                                    <select class="form-control select_type_id" name="type" id="ses_type_<?php echo $session_id; ?>">
                                                                                    <?php
                                                                                    $session_types = SessionType::where('id', '>', 0)->get();
                                                                                    foreach ($session_types as $session_type) {
                                                                                        ?>
                                                                                            <option value="<?php echo $session_type->id; ?>" <?php if ($session_type_id == $session_type->id) { ?> selected <?php } ?>>
                                                                                            <?php echo $session_type->name; ?>
                                                                                            </option>
                                                                                            <?php
                                                                                        }
                                                                                        ?>                                            
                                                                                    </select>
                                                                                            <?php
                                                                                        }
                                                                                        ?>
                                                                            </div>
                                                                            <div class="col-sm-2 col-xs-3 row_delete">		

                                                                                <button class="btn btn-danger btn_rem_slot" type="button" btn_slot_id="<?php echo $session_id; ?>" onclick="call_rem_slots(<?php echo $session_id; ?>)">
                                                                                    <i class="fa fa-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div> 	
                                                                        <hr>
                                                                    <?php
                                                                }
                                                                break;

                                                            case 2: {
                                                                    ?> 	
                                                                        <div class="row mt-5">
                                                                            <div class="col-sm-12">																							
                                                                                <label class="control-label">
                                                                                    Waiting for approval [<?php echo date('H:i', $time_start); ?> - <?php echo date('H:i', $time_end); ?>]
                                                                                </label>
                                                                            </div>
                                                                        </div> 
                                                                    <?php
                                                                }
                                                                break;

                                                            case 3: {
                                                                    ?> 	
                                                                        <div class="row mt-5">
                                                                            <div class="col-sm-12">																							
                                                                                <label class="control-label">
                                                                                    Booking Request is approved [<?php echo date('H:i', $time_start); ?> - <?php echo date('H:i', $time_end); ?>]
                                                                                </label>
                                                                            </div>
                                                                        </div> 
                                                                    <?php
                                                                }
                                                                break;

                                                            case 4: {
                                                                    ?> 	
                                                                        <div class="row mt-5">
                                                                            <div class="col-sm-12">

                                                                                <label class="control-label">
                                                                                    Booking Request is declined [<?php echo date('H:i', $time_start); ?> - <?php echo date('H:i', $time_end); ?>]
                                                                                </label>

                                                                            </div>
                                                                        </div>
                                                                    <?php
                                                                }
                                                                break;

                                                            default:
                                                                break;
                                                        }
                                                    }
                                                }
                                                break;

                                            case 2: {
                                                    
                                                }
                                                break;

                                            case 3: {
                                                    
                                                }
                                                break;

                                            default:
                                                break;
                                        }
                                    }
                                    ?> 
                                    </div> 

                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            &nbsp;
                                        </div>
                                    </div>

                                    <?php
                                    if ($checks > 0) {
                                        ?>						
                                        <div class="row mt-10">
                                            <div class="col-sm-6">
                                                <label class="control-label">
                                                    Total Slots Selected [<?php echo $checks; ?>]
                                                </label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="control-label">
                                                    Total Amount [<?php echo $price_total; ?>]
                                                </label>
                                            </div>
                                        </div>					
                                        <div class="row mt-10">
                                            <div class="col-sm-12">
                                                <div id="book_message"></div>
                                            </div>
                                        </div>				
                                        <div class="row mt-10 row_book">
                                            <div class="col-sm-6">
                                                <button type="button" class="btn btn-primary btn_book">
                                                    Book Selected
                                                </button>
                                            </div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn btn-primary btn_reload hide">
                                                    Reset Calendar
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row mt-10 row_checkout hide">
                                            <div class="col-sm-12">
                                                <button type="button" class="btn btn-primary btn_checkout">
                                                    Pay Now
                                                </button>

                                                <form id="proceed_payment" method="post" action="<?php echo url('/pay/pay_checkout'); ?>" style="display:none">
                                                    @csrf
                                                    <textarea name="slots" id="textarea_slots"></textarea>
                                                </form>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            exit;
                        }
                        break;

                    case 'booking_create': {

                            $result = array();
                            $messages = array();
                            $msg_count = 0;

                            $created_at = time();
                            $current = strtotime(date('Y-m-d H:i'));
                            $next_day = ($current + (24 * 60 * 60));
                            $next_month = ($next_day + (30 * 24 * 60 * 60));

                            $book_types = ltrim(rtrim($request->book_types));
                            $slots_str = ltrim(rtrim($request->slots_str));
                            if ($slots_str != '') {// && $logged_in_user_id != $cal_user_id
                                $types_arr = explode(',', $book_types);
                                $slots_arr = explode(',', $slots_str);
                                $slot_ind = 0;
                                foreach ($slots_arr as $session_id) {
                                    $slot_exists = 0;
                                    $row = Session::where('id', $session_id)->where('status', 1)->first();
                                    if (!empty($row)) {
                                        $slot_exists = 1;
                                        $cal_user_id = $row->user_id;
                                        $time_start = $row->time_start;
                                        $time_end = $row->time_end;

                                        if ($time_start < $current) {
                                            $messages[] = "You can not make booking in old dates.";
                                            $msg_count++;
                                        }
                                        else {
                                            $modified_at = time();
                                            $session_type_id = $types_arr[$slot_ind];
                                            $session = Session::find($session_id);
                                            $session->type = $session_type_id;
                                            $session->booked = 1;
                                            $session->save();

                                            $bool = get_slot_user_availability($session_id, $logged_in_user_id);
                                            switch ($bool) {
                                                case 1: {
                                                        $booking = new Booking();
                                                        $booking->user_id = $cal_user_id;
                                                        $booking->req_user_id = $logged_in_user_id;
                                                        $booking->session_id = $session_id;
                                                        $booking->status = 1;
                                                        $booking->save();
                                                        $booking_id = $booking->id;

                                                        $messages[] = "Booked [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]";
                                                        $msg_count++;
                                                    }
                                                    break;

                                                case 2: {
                                                        $messages[] = "Waiting for approval [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]";
                                                        $msg_count++;
                                                    }
                                                    break;

                                                case 3: {
                                                        $messages[] = "Booked Already [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]";
                                                        $msg_count++;
                                                    }
                                                    break;

                                                case 4: {
                                                        $messages[] = "Booking Request is declined [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]";
                                                        $msg_count++;
                                                    }
                                                    break;

                                                default: {
                                                        $messages[] = "Not Available [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]";
                                                        $msg_count++;
                                                    }
                                                    break;
                                            }
                                        }
                                    }
                                    if ($slot_exists == 0) {
                                        $messages[] = "Time slot is no more available";
                                        $msg_count++;
                                    }
                                    $slot_ind++;
                                }
                            }
                            else {
                                if ($logged_in_user_id == $cal_user_id) {
                                    $messages[] = "You can not create booking";
                                    $msg_count++;
                                }
                                else {
                                    $messages[] = "Please select at least one time slot";
                                    $msg_count++;
                                }
                            }
                            $result['slots_str'] = $slots_str;
                            $result['messages'] = $messages;
                            $result['count'] = $msg_count;

                            echo json_encode($result);

                            exit;
                        }
                        break;

                    case 'grid_calendar': {
                            include_once('shared/inc_grid_common_1_c.php');

                            $option_8hrs = $request->option_8hrs;

                            include_once('shared/inc_grid_common_2_c.php');

                            exit;
                        }
                        break;

                    case 'show_booking_details': {
                            $current_time = strtotime(date('Y-m-d H:i'));
                            $current = strtotime(date('Y-m-d'));
                            $next_day = ($current + (24 * 60 * 60));
                            $next_month = ($next_day + (30 * 24 * 60 * 60));

                            $option = 0;
                            if ($request->option) {
                                $option = $request->option;
                            }

                            $session_id = $request->id;
                            $booking_id = get_booking_data_by_vail_id('id', $session_id);
                            if ($booking_id != '') {
                                ?>
                                <div class="increment_times">
                                <?php
                                $checks = 0;
                                $count = 0;
                                $booking = Booking::find($booking_id);
                                if (!empty($booking)) {
                                    $session_id = $booking->session_id;

                                    $requested_user_id = $booking->req_user_id;
                                    $requested_user_name = ucwords(get_user_name($requested_user_id));

                                    $invited_user_id = $booking->user_id;
                                    $invited_user_name = ucwords(get_user_name($invited_user_id));

                                    $row = Session::find($session_id);
                                    if (!empty($row)) {
                                        $session_id = $row->id;
                                        $time_start = $row->time_start;
                                        $time_start_full = date("D M j Y H:i A T", $time_start);
                                        $time_end = $row->time_end;
                                        $checks++;
                                        if ($logged_in_user_id == $invited_user_id) {
                                            ?>	
                                                <div class="row times_row mt-20">
                                                    <div class="col-sm-12">
                                                        <p>
                                                            You have been booked by <?php echo $requested_user_name; ?> for the following date and time.
                                                        </p>	
                                                    </div>	
                                                </div>	
                                            <?php
                                        }
                                        else {
                                            ?>
                                                <div class="row times_row mt-20">
                                                    <div class="col-sm-12">
                                                        <p>
                                                            You have booked <?php echo $invited_user_name; ?> for the following date and time.
                                                        </p>	
                                                    </div>	
                                                </div>
                                            <?php
                                        }
                                        ?>
                                            <div class="row times_row mt-20">
                                                <div class="col-sm-12">
                                                    <strong><?php echo $invited_user_name; ?> and <?php echo $requested_user_name; ?></strong>                       	
                                                </div>	
                                            </div>
                                            <div class="row times_row mt-10">
                                                <div class="col-sm-12">
                                                    <p>
                                                        When
                                                    </p>
                                                </div>		
                                            </div>
                                            <div class="row times_row mt-5">
                                                <div class="col-sm-12">
                                                    <p>
                                                        <strong><?php echo $time_start_full; ?></strong>
                                                    </p>
                                                </div>	
                                            </div>
                                            <div class="row times_row mt-20">
                                                <div class="col-sm-12">
                                                    <p>
                                                        Who
                                                    </p>
                                                </div>		
                                            </div>
                                            <div class="row times_row mt-5">
                                                <div class="col-sm-12">
                                                    <p>
                                                        <strong>1. <?php echo $invited_user_name; ?></strong> - organizer
                                                    </p>
                                                </div>	
                                            </div>
                                            <div class="row times_row mt-5">
                                                <div class="col-sm-12">
                                                    <p>
                                                        <strong>2. <?php echo $requested_user_name; ?></strong>
                                                    </p>
                                                </div>	
                                            </div> 	
                                        <?php
                                    }
                                }
                                ?> 
                                </div> 

                                <div class="row mt-10">
                                    <div class="col-sm-12">
                                        &nbsp;
                                    </div>
                                </div>

                                <?php
                                if ($checks > 0) {
                                    ?>	
                                    <?php
                                    $style = ' style="display:none"';
                                    if ($option == 1) {
                                        $style = '';
                                    }
                                    ?>                            				
                                    <div id="cancel_slot_div" <?php echo $style; ?>>
                                        <div class="row mt-10">
                                            <div class="col-sm-12">
                                                <label>Cancel Reason</label>
                                                <br />
                                                <textarea id="cancel_reason" name="cancel_reason" cols="30" rows="4"></textarea>
                                            </div>
                                        </div>				
                                        <div class="row mt-10">
                                            <div class="col-sm-12 text-center">
                                                <hr />
                                            </div>
                                        </div>				
                                        <div class="row mt-10">
                                            <div class="col-sm-12 text-center">
                                                <button type="button" class="btn btn-primary btn-block">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $style = ' style="display:none"';
                                    if ($option == 2) {
                                        $style = '';
                                    }
                                    ?>					
                                    <div id="reschedule_slot_div" <?php echo $style; ?>>
                                        <div class="row mt-10">
                                            <div class="col-sm-12">
                                                <label>Reschedule Reason</label>
                                                <br />
                                                <textarea id="reschedule_reason" name="reschedule_reason" cols="30" rows="4"></textarea>
                                            </div>
                                        </div>				
                                        <div class="row mt-10">
                                            <div class="col-sm-12 text-center">
                                                <hr />
                                            </div>
                                        </div>				
                                        <div class="row mt-10">
                                            <div class="col-sm-12 text-center">
                                                <button type="button" class="btn btn-primary btn-block">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>				
                                    <div class="row mt-10">
                                        <div class="col-sm-12">
                                            <div id="book_message"></div>
                                        </div>
                                    </div>				
                                    <div class="row mt-10">
                                        <div class="col-sm-6 text-center">                            	
                                    <?php
                                    $class = '';
                                    if ($option == 1) {
                                        $class = 'btn-outline';
                                    }
                                    ?>	
                                            <button type="button" class="btn btn-danger btn_cancel_slot <?php echo $class; ?>">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                        </div>
                                        <div class="col-sm-6 text-center">                            	
                                            <?php
                                            $class = '';
                                            if ($option == 2) {
                                                $class = 'btn-outline';
                                            }
                                            ?>	
                                            <button type="button" class="btn btn-primary colrblue btn_reschedule_slot <?php echo $class; ?>">
                                                <i class="fa fa-list"></i> Reschedule
                                            </button>
                                        </div>
                                    </div>				
                                    <div class="row mt-10">
                                        <div class="col-sm-12 text-center">
                                            <hr />
                                        </div>
                                    </div>				
                                    <div class="row mt-10">
                                        <div class="col-sm-12 text-center">
                                            <button type="button" class="btn btn-primary btn-block btn_reload">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            exit;
                        }
                        break;

                    default:
                        break;
                }
            } {
                switch ($type) {

                    case 'check_slot': {
                            $slot_id = 0;

                            $class_name = '';
                            if ($request->class_name) {
                                $class_name = $request->class_name;
                            }
                            //fc-event fc-event-hori fc-event-draggable fc-event-start fc-event-end cust_cli 20 cust_cl_1
                            $str = explode('cust_cli', $class_name);
                            $str = ltrim(rtrim($str[1]));
                            $str = explode('cust_cl_', $str);
                            $slot_id = ltrim(rtrim($str[0]));

                            echo $slot_id;
                        }
                        break;

                    case 'json_month': {

                            $Result = array();

                            $Result['response_Code'] = "0";

                            $Result['response_Text'] = "User is Not Available";
                            $slots = array();
                            $slots_count = 0;

                            if (isset($request->user_id) && $request->user_id != '' && $request->user_id != 0) {
                                $cal_user_id = $request->user_id;
                                $available_from = 9;
                                $available_to = 10;

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

                                $UserCalendar = UserCalendar::where('user_id', $cal_user_id)->first();
                                if (!empty($UserCalendar)) {
                                    $row = $UserCalendar;
                                    $available_from = $row->available_from;
                                    $available_to = $row->available_to;

                                    $sunday_sts = $row->sunday_sts;
                                    $monday_sts = $row->monday_sts;
                                    $tuesday_sts = $row->tuesday_sts;
                                    $wednesday_sts = $row->wednesday_sts;
                                    $thursday_sts = $row->thursday_sts;
                                    $friday_sts = $row->friday_sts;
                                    $saturday_sts = $row->saturday_sts;

                                    $date_format = $row->date_format;
                                    $time_format = $row->time_format;
                                    $time_zone = $row->time_zone;
                                    $time_zone = get_timezone_data('name', $row->time_zone);
                                }



                                $data = array();
                                $data['sun'] = $sunday_sts;
                                $data['mon'] = $monday_sts;
                                $data['tue'] = $tuesday_sts;
                                $data['wed'] = $wednesday_sts;
                                $data['thu'] = $thursday_sts;
                                $data['fri'] = $friday_sts;
                                $data['sat'] = $saturday_sts;
                                $Result['availableDays'] = $data;

                                if ($time_zone == '') {
                                    $time_zone = 'UTC';
                                }

                                if ($date_format == 'MM/DD/YYYY') {
                                    
                                }
                                else {
                                    
                                }

                                $data = array();
                                $data['date_format'] = $date_format;
                                $data['time_format'] = $time_format;
                                $data['time_zone'] = $time_zone;
                                $Result['date_time'] = $data;

                                $current_time = time();
                                $bool = 1;
                                $slots_count = 0;
                                $rows = Session::where('user_id', $cal_user_id)->where('time_start', '>', $current_time)->where('status', 1)->orderby('time_start', 'asc')->get();
                                if (!$rows->isEmpty()) {
                                    foreach ($rows as $row) {
                                        $bool = 1;
                                        $slot_id = $row->id;
                                        $row->time_start = strtotime(date('Y-m-d H:i:s', $row->time_start));
                                        $row->time_end = strtotime(date('Y-m-d H:i:s', $row->time_end));
                                        $time_start = $row->time_start;
                                        $time_end = $row->time_end;

                                        $session_type = get_session_type($row->type);
                                        $session_status = $row->status;
                                        $session_color = $row->color;

                                        $session_id = $row->id;

                                        $user_req_status = 0;
                                        if ($logged_in_user_id > 0) {
                                            $user_req_status = get_availability_req_status($time_start, $logged_in_user_id);
                                        }

                                        $ampm = '';
                                        $available_from_str = '';
                                        $available_to_str = '';
                                        $available_str = '';
                                        {
                                            $available_from = date('H', $time_start);
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

                                            $available_mins = date('i', $time_start);
                                            $available_mins = sprintf('%02d', $available_mins);

                                            $available_from_str = $available_from . ':' . $available_mins . ' ' . $ampm;

                                            $available_to = date('H', $time_end);
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

                                            $available_mins = date('i', $time_end);
                                            $available_mins = sprintf('%02d', $available_mins);

                                            $available_to_str = $available_to . ':' . $available_mins . ' ' . $ampm;
                                        }

                                        $available_str .= $session_type . "\n" . $available_from_str; //.' - '.$available_to_str;
                                        $available_str = ltrim(rtrim($available_str));

                                        $className = '';
                                        $className .= ' cust_cli';
                                        $className .= ' ' . $slot_id;
                                        $className .= ' ' . $session_color;

                                        $slot_title = '';

                                        $slot_status = get_slot_status($session_id);
                                        if ($slot_status == 1) {// if invite received by logged in user and it is accepted for this time slot
                                            $slot_title = 'Booked';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 2) {
                                            $slot_title = 'Confirmed';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 3) {
                                            $slot_title = 'Delivered';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 4) {
                                            $slot_title = 'Expired';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 5) {
                                            $slot_title = 'Cancelled';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        else {
                                            $slot_title = 'Available';
                                        }
                                        $description = $slot_title . ' ' . $available_str;

                                        $time_start = (strtotime(date('Y-m-d H:i:s', $row->time_start)) * 1000);
                                        $time_end = (strtotime(date('Y-m-d H:i:s', $row->time_end)) * 1000);

                                        $slots_count++;
                                        $data = array();
                                        $data['slot_status'] = $slot_status;
                                        $data['available_str'] = $available_str;
                                        $data['className'] = $className;
                                        $data['start'] = $time_start;
                                        $data['end'] = $time_end;
                                        $data['title'] = $slot_title;
                                        $data['description'] = $description;

                                        $slots[$time_start] = $data;
                                    }
                                }
                                if ($bool == 1) {
                                    $Result['response_Code'] = "1";
                                    $Result['response_Text'] = "User is Available";
                                }
                            }
                            ksort($slots);
                            $slots = array_values($slots);
                            $Result['time_slots'] = $slots;
                            $Result['total_slots'] = $slots_count;

                            echo json_encode($Result);

                            exit;
                        }
                        break;

                    case 'json_month_front': {

                            $Result = array();

                            $Result['response_Code'] = "0";

                            $Result['response_Text'] = "User is Not Available";
                            $slots = array();
                            $slots_count = 0;

                            if (isset($request->user_id) && $request->user_id != '' && $request->user_id != 0) {
                                $cal_user_id = $request->user_id;
                                $available_from = 9;
                                $available_to = 10;

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

                                $UserCalendar = UserCalendar::where('user_id', $cal_user_id)->first();
                                if (!empty($UserCalendar)) {
                                    $row = $UserCalendar;
                                    $available_from = $row->available_from;
                                    $available_to = $row->available_to;

                                    $sunday_sts = $row->sunday_sts;
                                    $monday_sts = $row->monday_sts;
                                    $tuesday_sts = $row->tuesday_sts;
                                    $wednesday_sts = $row->wednesday_sts;
                                    $thursday_sts = $row->thursday_sts;
                                    $friday_sts = $row->friday_sts;
                                    $saturday_sts = $row->saturday_sts;

                                    $date_format = $row->date_format;
                                    $time_format = $row->time_format;
                                    $time_zone = $row->time_zone;
                                }



                                $data = array();
                                $data['sun'] = $sunday_sts;
                                $data['mon'] = $monday_sts;
                                $data['tue'] = $tuesday_sts;
                                $data['wed'] = $wednesday_sts;
                                $data['thu'] = $thursday_sts;
                                $data['fri'] = $friday_sts;
                                $data['sat'] = $saturday_sts;
                                $Result['availableDays'] = $data;

                                if ($time_zone == '') {
                                    $time_zone = 'UTC';
                                }

                                if ($date_format == 'MM/DD/YYYY') {
                                    
                                }
                                else {
                                    
                                }

                                $data = array();
                                $data['date_format'] = $date_format;
                                $data['time_format'] = $time_format;
                                $data['time_zone'] = $time_zone;
                                $Result['date_time'] = $data;

                                $current_time = time(); //(time()+(24*60*60));
                                $bool = 1;
                                $slots_count = 0;
                                $rows = Session::where('user_id', $cal_user_id)->where('time_start', '>', $current_time)->where('status', 1)->orderby('time_start', 'asc')->get();
                                if (!$rows->isEmpty()) {
                                    foreach ($rows as $row) {
                                        $bool = 1;
                                        $slot_id = $row->id;
                                        $row->time_start = strtotime(date('Y-m-d H:i:s', $row->time_start));
                                        $row->time_end = strtotime(date('Y-m-d H:i:s', $row->time_end));

                                        $time_start = $row->time_start;
                                        $time_end = $row->time_end;

                                        $session_type = get_session_type($row->type);
                                        $session_status = $row->status;
                                        $session_color = $row->color;

                                        $slot_user_id = $row->user_id;
                                        $slot_user_name = get_user_name($slot_user_id);
                                        $slot_user_name = get_user_data('public_url', $slot_user_id);

                                        $session_id = $row->id;

                                        $user_req_status = 0;
                                        if ($logged_in_user_id > 0) {
                                            $user_req_status = get_availability_req_status($time_start, $logged_in_user_id);
                                        }

                                        $ampm = '';
                                        $available_from_str = '';
                                        $available_to_str = '';
                                        $available_str = '';
                                        {
                                            $available_from = date('H', $time_start);
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

                                            $available_mins = date('i', $time_start);
                                            $available_mins = sprintf('%02d', $available_mins);

                                            $available_from_str = $available_from . ':' . $available_mins . ' ' . $ampm;

                                            $available_to = date('H', $time_end);
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

                                            $available_mins = date('i', $time_end);
                                            $available_mins = sprintf('%02d', $available_mins);

                                            $available_to_str = $available_to . ':' . $available_mins . ' ' . $ampm;
                                        }
                                        $available_str = $slot_user_name . "\n";

                                        $available_str .= $session_type . "\n" . $available_from_str; //.' - '.$available_to_str;
                                        $available_str = ltrim(rtrim($available_str));

                                        /* $available_str = $session_type.' - '.$available_from_str."\n";

                                          $available_str.= 'by '.$slot_user_name;//.' - '.$available_to_str;
                                          $available_str = ltrim(rtrim($available_str)); */

                                        $className = '';
                                        if ($logged_in_user_id > 0) {
                                            $className .= 'cust_cli';
                                            $className .= ' ' . $slot_id;
                                            $className .= ' ' . $session_color;
                                        }
                                        else {
                                            $className .= 'cust_cli';
                                        }

                                        $slot_title = '';

                                        $slot_status = get_slot_status($session_id);
                                        if ($slot_status == 1) {
                                            $slot_title = 'Booked';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 2) {
                                            $slot_title = 'Confirmed';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 3) {
                                            $slot_title = 'Delivered';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 4) {
                                            $slot_title = 'Expired';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        elseif ($slot_status == 5) {
                                            $slot_title = 'Cancelled';
                                            $className = 'cust_cl_booked';
                                            $available_str = $slot_title . "\n" . $available_str;
                                        }
                                        else {
                                            $slot_title = 'Available';
                                        }
                                        $description = $slot_title . ' ' . $available_str;

                                        $time_start = (strtotime(date('Y-m-d H:i:s', $row->time_start)) * 1000);
                                        $time_end = (strtotime(date('Y-m-d H:i:s', $row->time_end)) * 1000);

                                        $slots_count++;
                                        $data = array();
                                        $data['slot_status'] = $slot_status;
                                        $data['available_str'] = $available_str;
                                        $data['className'] = $className;
                                        $data['start'] = $time_start;
                                        $data['end'] = $time_end;
                                        $data['title'] = $slot_title;
                                        $data['description'] = $description;

                                        $slots[$time_start] = $data;
                                    }
                                }
                                if ($bool == 1) {
                                    $Result['response_Code'] = "1";
                                    $Result['response_Text'] = "User is Available";
                                }
                            }
                            ksort($slots);
                            $slots = array_values($slots);
                            $Result['time_slots'] = $slots;
                            $Result['total_slots'] = $slots_count;

                            echo json_encode($Result);

                            exit;
                        }
                        break;

                    default:
                        break;
                }
            }
        }
    }

}
