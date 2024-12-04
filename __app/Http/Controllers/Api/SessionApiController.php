<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPersonal;
use App\Models\Session;
use App\Models\Booking;

class SessionApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            $completedProfileActions = [
                'create_session',
                'check_session',
                'book_session',
                'cancel_booking',
                'delete_session',
            ];
            if (in_array($action, $completedProfileActions) && $profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }

            $current_time = time();
            update_all_bookings($current_time);

            switch ($action) {
                case 'create_session': {
                        return $this->create_session($request, $User);
                    }
                    break;

                case 'check_session': {
                        return $this->check_session($request, $User);
                    }
                    break;

                case 'search_session': {
                        return $this->search_session($request, $User);
                    }
                    break;

                case 'book_session': {
                        return $this->book_session($request, $User);
                    }
                    break;

                case 'cancel_booking': {
                        return $this->cancel_booking($request, $User);
                    }
                    break;

                case 'delete_session': {
                        return $this->delete_session($request, $User);
                    }
                    break;

                default: {
                        return $this->sendError('Invalid Request');
                    }
                    break;
            }
        }
        else {
            return $this->sendError($result['message']);
        }
    }

    private function get_new_sessions($session_ids) {
        $session_array = array();
        $CreatedSessions = Session::whereIN('id', $session_ids)->get();
        foreach ($CreatedSessions as $availability) {
            $time_start = $availability->time_start;
            //$time_end = $availability->time_end;
            $session_id = $availability->id;
            $coach_id = $availability->user_id;
            //$session_color = $availability->color;
            $session_price = $availability->price;
            $session_type = $aval_type = get_session_type($availability->type);
            //$session_user_id = $availability->user_id;
            //$status = $availability->booked;

            $session_rows = [
                "session_id"   => $session_id,
                "date"         => date('M d, Y', $time_start),
                "start_time"   => date('h:i A', $time_start),
                "session_type" => $session_type,
                "coach_id"     => $coach_id,
                "coach"        => get_user_name($coach_id),
                "rating"       => get_user_data('rating', $coach_id),
                "price"        => $session_price,
            ];

            if ($session_type != $aval_type) {
                $session_rows["session_type"] .= " - " . $aval_type;
            }

            $session_array[] = $session_rows;
        }
        return $session_array;
    }

    private function create_session(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER) {
            if (isset($request->date_time) && ltrim(rtrim($request->date_time)) != '' && isset($request->session_type) && ltrim(rtrim($request->session_type)) != '' && isset($request->price) && ltrim(rtrim($request->price)) != '' && isset($request->description) && ltrim(rtrim($request->description)) != '' && isset($request->recursion_type) && ltrim(rtrim($request->recursion_type)) != '') {
                $session_type = $request->session_type;
                $price = $request->price;
                $description = $request->description;
                $recursion_type = $request->recursion_type;
                $color = 'cust_cl_0';

                $current_time = time();

                $timestamp = strtotime($request->date_time) + (60 * 60);
                $time_start = strtotime($request->date_time);
                $time_end = strtotime(date("Y-m-d H:i:s", $timestamp));

                if ($time_start >= $current_time) {
                    if ($recursion_type == 1) {
                        $slot_id = 0;
                        $Session = Session::where('user_id', $user_id)->where('time_start', $time_start)->where('time_end', $time_end)->first();
                        if (!empty($Session)) {
                            $slot_id = $Session->id;
                        }

                        if ($slot_id == 0) {
                            $Session = new Session();
                            $Session->user_id = $user_id;
                            $Session->type = $session_type;
                            $Session->price = $price;
                            $Session->description = $description;
                            $Session->color = $color;
                            $Session->time_start = $time_start;
                            $Session->time_end = $time_end;
                            $Session->status = 1;
                            $Session->save();
                            $session_ids = [$Session->id];

                            $session_array = $this->get_new_sessions($session_ids);
                            $data = [
                                'sessions' => $session_array
                            ];

                            return $this->sendResponse($data, 'Session Created Successfully');
                        }
                        else {
                            return $this->sendError('Session Already exists');
                        }
                    }
                    elseif ($recursion_type == 2) {
                        if (isset($request->duration) && ltrim(rtrim($request->duration)) != '' && isset($request->sunday) && ltrim(rtrim($request->sunday)) != '' && isset($request->monday) && ltrim(rtrim($request->monday)) != '' && isset($request->tuesday) && ltrim(rtrim($request->tuesday)) != '' && isset($request->wednesday) && ltrim(rtrim($request->wednesday)) != '' && isset($request->thursday) && ltrim(rtrim($request->thursday)) != '' && isset($request->friday) && ltrim(rtrim($request->friday)) != '' && isset($request->saturday) && ltrim(rtrim($request->saturday)) != '') {
                            $duration = $request->duration;

                            $sunday = $request->sunday;
                            $monday = $request->monday;
                            $tuesday = $request->tuesday;
                            $wednesday = $request->wednesday;
                            $thursday = $request->thursday;
                            $friday = $request->friday;
                            $saturday = $request->saturday;

                            $now = strtotime("now");

                            if ($duration < 3) {
                                $end_date = strtotime("+$duration weeks");
                            }
                            elseif ($duration == 3) {
                                $end_date = strtotime("+1 month");
                            }
                            elseif ($duration == 4) {
                                $end_date = strtotime("+2 month");
                            }

                            $session_ids = array();
                            while (date("Y-m-d", $now) != date("Y-m-d", $end_date)) {

                                $proceed = 0;

                                $day_index = date("w", $now);

                                if ($day_index == 0 && $sunday == 1) {
                                    $proceed = 1;
                                }
                                elseif ($day_index == 1 && $monday == 1) {
                                    $proceed = 1;
                                }
                                elseif ($day_index == 2 && $tuesday == 1) {
                                    $proceed = 1;
                                }
                                elseif ($day_index == 3 && $wednesday == 1) {
                                    $proceed = 1;
                                }
                                elseif ($day_index == 4 && $thursday == 1) {
                                    $proceed = 1;
                                }
                                elseif ($day_index == 5 && $friday == 1) {
                                    $proceed = 1;
                                }
                                elseif ($day_index == 6 && $saturday == 1) {
                                    $proceed = 1;
                                }
                                $now = strtotime(date("Y-m-d", $now) . "+1 day");

                                if ($proceed) {
                                    $slot_id = 0;

                                    $Session = Session::where('user_id', $user_id)->where('time_start', $time_start)->where('time_end', $time_end)->first();
                                    if (!empty($Session)) {
                                        $slot_id = $Session->id;
                                    }

                                    if ($slot_id == 0) {
                                        $Session = new Session();
                                        $Session->user_id = $user_id;
                                        $Session->type = $session_type;
                                        $Session->price = $price;
                                        $Session->description = $description;
                                        $Session->color = $color;
                                        $Session->time_start = $time_start;
                                        $Session->time_end = $time_end;
                                        $Session->status = 1;
                                        $Session->save();
                                        $session_ids[] = $Session->id;
                                    }
                                }

                                $time_start = ($time_start + 86400);
                                $time_end = ($time_end + 86400);
                            }

                            $session_array = $this->get_new_sessions($session_ids);
                            $data = [
                                'sessions' => $session_array
                            ];

                            return $this->sendResponse($data, 'Sessions Created Successfully');
                        }
                        else {
                            return $this->sendError('missing Parameters');
                        }
                    }
                    else {
                        return $this->sendError('Incorrect Recursion Type');
                    }
                }
                else {
                    return $this->sendError('Cannot create session for previous dates');
                }
            }
            else {
                return $this->sendError('missing Parameters');
            }
        }
        else {
            return $this->sendError('Incorrect User Type');
        }
    }

    private function check_session(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        $session_data = array();

        $exists = 0;
        $current_time = time();
        $session_start = time();
        $session_end = (time() + (10 * 60));

        $booking = Session::leftJoin('bookings', 'sessions.id', '=', 'bookings.session_id');
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

            $booking_user_id = $booking['req_user_id'];
            $session_type = $aval_type = get_session_type($booking['type']);
            $session_user_id = stripslashes($booking['user_id']);

            $time_start = $booking['time_start'];
            $time_end = $booking['time_end'];

            $time_to_go = ($time_start - $current_time);
            $time_mins = ($time_to_go / 60);
            $time_secs = ($time_to_go % 60);
            $time_secs = sprintf('%02d', $time_secs);
            $remaining = "$time_mins:$time_secs";

            $public_url = get_user_profile_data('meetinglink', $session_user_id);

            if ($user_type == $this->_COACH_USER) {
                $session_data["user"] = ucwords(get_user_name($booking_user_id));
            }
            else {
                $session_data["user"] = ucwords(get_user_name($session_user_id));
            }
            $session_data["session_type"] = $session_type;
            $session_data["start_date"] = date('M d, Y', $time_start);
            $session_data["start_time"] = date('h:i A', $time_start);
            $session_data["url"] = $public_url;
            $session_data["remaining"] = $remaining;
        }

        $response = [
            'session' => $session_data
        ];

        if ($exists > 0) {
            return $this->sendResponse($response, 'Successfully returned Sessions data');
        }
        else {
            return $this->sendResponse($response, 'No Sessions data found');
        }
    }

    private function search_session(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $SITE_URL = env('APP_URL');

        if (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) {
            $page_no = $request->page_no;
        }
        else {
            $page_no = 1;
        }

        if (isset($request->limit) && $request->limit != "" && $request->limit != 0) {
            $limit = $request->limit;
        }
        else {
            $limit = 5;
        }

        $offset = ($page_no - 1) * $limit;

        if (isset($request->start) && ltrim(rtrim($request->start)) != '' && isset($request->end) && ltrim(rtrim($request->end)) != '') {
            $start = $request->start;
            $start = strtotime($start);

            $end = $request->end;
            $end = strtotime($end);
        }
        else {
            $start = date("Y-m-d");
            $start = strtotime($start);

            $end = date("Y-m-d", strtotime("+1 week"));
            $end = strtotime($end);
        }

        $session_array = array();
        $session_rows = array();

        $users = array();
        $user_details = array();
        $user_array = array();

        $total_records = Session::where('time_start', '>=', $start)
                ->where('time_end', '<=', $end)
                ->where('booked', '<', '1')
                ->count();
        $total_no_of_pages = ceil($total_records / $limit);

        $availabilities = Session::where('time_start', '>=', $start)
                ->where('time_end', '<=', $end)
                ->where('booked', '<', '1')
                ->orderBy('time_start', 'asc')
                ->limit($limit)
                ->offset($offset)
                ->get();

        $current_count = $availabilities->count();
        foreach ($availabilities as $availability) {
            $time_start = $availability->time_start;
            $time_end = $availability->time_end;
            $session_id = $availability->id;
            $coach_id = $availability->user_id;
            $session_color = $availability->color;
            $session_price = $availability->price;
            $session_type = $aval_type = get_session_type($availability->type);
            $session_user_id = $availability->user_id;
            $status = $availability->booked;

            $session_rows = [
                "session_id"   => $session_id,
                "date"         => date('M d, Y', $time_start),
                "start_time"   => date('h:i A', $time_start),
                "session_type" => $session_type,
                "coach_id"     => $coach_id,
                "coach"        => get_user_name($coach_id),
                "rating"       => get_user_data('rating', $coach_id),
                "price"        => $session_price,
            ];

            if ($session_type != $aval_type) {
                $session_rows["session_type"] .= " - " . $aval_type;
            }

            $session_array[] = $session_rows;

            if (!in_array($coach_id, $users) && $coach_id != 0) {
                $users[] = $coach_id;
            }
        }

        foreach ($users as $coach_id) {
            $personal_profile = UserPersonal::where('user_id', $coach_id)->first();

            if ($personal_profile) {
                $profile_data = [
                    "caoch_id"      => $personal_profile->user_id,
                    "f_name"        => $personal_profile->first_name,
                    "l_name"        => $personal_profile->last_name,
                    "conatc_number" => $personal_profile->conatc_number,
                    "about_me"      => $personal_profile->about_me,
                    "zip_code"      => $personal_profile->zip_code,
                    "image"         => $SITE_URL . "/uploads/images/" . $personal_profile->coachpic,
                ];

                $user_array[] = $profile_data;
            }
        }

        $data = array();

        $data['page_no'] = $page_no;
        $data['limit'] = $limit;

        $data['total_records'] = $total_records;
        $data['current_count'] = $current_count;
        $data['total_no_of_pages'] = $total_no_of_pages;

        $data['session_details'] = $session_array;
        $data['coach_details'] = $user_array;

        return $this->sendResponse($data, 'Successfully returned Sessions data');
    }

    private function book_session(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PLAYER_USER) {

            if (isset($request->session_id) && ltrim(rtrim($request->session_id)) != '' && isset($request->session_type) && ltrim(rtrim($request->session_type)) != '') {
                $current = strtotime(date('Y-m-d H:i'));
                $next_day = ($current + (24 * 60 * 60));
                $next_month = ($next_day + (30 * 24 * 60 * 60));

                $session_id = ltrim(rtrim($request->session_id));
                $session_type = ltrim(rtrim($request->session_type));

                $slot_exists = 0;
                $ModelData = Session::where('id', $session_id)->where('status', 1)->where('booked', 0)->first();
                if (!empty($ModelData)) {
                    $slot_exists = 1;
                    $cal_user_id = $ModelData->user_id;
                    $time_start = $ModelData->time_start;
                    $time_end = $ModelData->time_end;

                    if ($time_start < $current) {
                        return $this->sendError('You can not make booking in old dates.');
                    }
                    else {
                        $Session = Session::find($session_id);
                        $Session->type = $session_type;
                        $Session->booked = 1;
                        $Session->save();

                        $bool = get_slot_user_availability($session_id, $user_id);
                        switch ($bool) {
                            case 1: {
                                    $Booking = new Booking();
                                    $Booking->user_id = $cal_user_id;
                                    $Booking->req_user_id = $user_id;
                                    $Booking->session_id = $session_id;
                                    $Booking->status = 1;
                                    $Booking->save();

                                    return $this->sendSuccess("Session Booked Succesfully");
                                }
                                break;

                            case 2: {
                                    return $this->sendError("Waiting for approval [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]");
                                }
                                break;

                            case 3: {
                                    return $this->sendError("Booked Already [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]");
                                }
                                break;

                            case 4: {
                                    return $this->sendError("Booking Request is declined [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]");
                                }
                                break;

                            default: {
                                    return $this->sendError("Not Available [" . date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end) . "]");
                                }
                                break;
                        }
                    }
                }
                else {
                    return $this->sendError('Incorrect Session Provided');
                }

                if ($slot_exists == 0) {
                    return $this->sendError('Time slot is no more available');
                }
            }
            else {
                return $this->sendError('missing Parameters');
            }
        }
        else {
            return $this->sendError('Incorrect User Type');
        }
    }

    private function delete_session(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER) {
            if (isset($request->session_id) && ltrim(rtrim($request->session_id)) != '') {
                $session_id = $request->session_id;

                $ModelData = Session::where('id', $session_id)->where('user_id', $user_id)->first();

                if (!empty($ModelData)) {
                    $booking = Booking::where('session_id', $session_id)->first();
                    if ($booking != null) {
                        return $this->sendError('Session already booked');
                    }

                    // $Model_Data = Session::find($session_id);

                    $ModelData->delete();

                    return $this->sendSuccess("Successfully deleted the session");
                }
                else {
                    return $this->sendError('Incorrect Session Provided');
                }
            }
            else {
                return $this->sendError('Missing parameters');
            }
        }
        else {
            return $this->sendError('Incorrect User Type');
        }
    }

    private function cancel_booking(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
            if (isset($request->booking_id) && ltrim(rtrim($request->booking_id)) != '' && isset($request->reason) && ltrim(rtrim($request->reason)) != '') {
                $booking_id = $request->booking_id;

                $booking = array();
                if ($user_type == $this->_COACH_USER) {
                    $booking = Booking::where('id', $booking_id)->where('user_id', $user_id)->first();
                }
                elseif ($user_type == $this->_PLAYER_USER) {
                    $booking = Booking::where('id', $booking_id)->where('req_user_id', $user_id)->first();
                }

                if (!empty($booking)) {
                    if ($user_type == $this->_COACH_USER) {
                        return $this->coach_cancel_booking($request, $User, $booking);
                    }
                    elseif ($user_type == $this->_PLAYER_USER) {
                        return $this->player_cancel_booking($request, $User, $booking);
                    }
                }
                else {
                    return $this->sendError("Incorrect Session Provided");
                }
            }
            else {
                return $this->sendError("Missing parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function coach_cancel_booking(Request $request, $User, $booking) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $booking_id = $booking->id;
        $session_id = $booking->session_id;
        // dd($booking_id);
        $reason = $request->reason;

        $booking = Booking::where('id', $booking_id)->where('user_id', $user_id)->whereIn('status', [0, 1, 2])->first();
        if ($booking == null) {
            return $this->sendError("booking not found");
        }
        if ($booking->status == 1)
            $booking->status = 3;
        elseif ($booking->status == 2)
            $booking->status = 5;
        $booking->coach_cancellation_reason = $reason;
        $booking->save();

        $userSession = Session::find($session_id);
        $userSession->booked = 0;
        $userSession->save();

        return $this->sendSuccess("Successfully Cancelled the Session");
    }

    private function player_cancel_booking(Request $request, $User, $session) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $session_id = $session->session_id;
        $reason = $request->reason;

        $booking = Booking::where('session_id', $session_id)->where('req_user_id', $user_id)->whereIn('status', [0, 1, 2])->first();
        if ($booking == null) {
            return $this->sendError("Session not found");
        }
        if ($booking->status == 1)
            $booking->status = 4;
        elseif ($booking->status == 2)
            $booking->status = 6;
        $booking->player_cancellation_reason = $reason;
        $booking->save();

        $Session = Session::find($session_id);
        $Session->booked = 0;
        $Session->save();

        return $this->sendSuccess("Successfully Cancelled the Session");
    }

}
