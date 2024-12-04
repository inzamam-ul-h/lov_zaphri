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
use App\Models\UserPersonal;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentDetail;

class PaymentApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            $profileStatus = $this->get_user_profile_status($User);
            if ($profileStatus == 0) {
                return $this->sendError('Please complete your profile first');
            }
            $current_time = time();
            update_all_bookings($current_time);

            switch ($action) {
                case 'pay_now': {
                        return $this->pay_now($request, $User);
                    }
                    break;

                case 'my_payments': {
                        return $this->my_payments($request, $User);
                    }
                    break;

                default: {
                        return $this->sendError('Invalid Request');
                    }
                    break;
            }
        }
        else{
            return $this->sendError($result['message']);
        }
    }

    private function delete_unpaid_payments() {
        $payments = Payment::where('status', 0)->get();
        foreach ($payments as $payment) {
            $payment_id = $payment->id;
            $payment_details = PaymentDetail::where('payment_id', $payment_id)->get();
            foreach ($payment_details as $payment_detail) {
                $payment_detail_id = $payment_detail->id;
                PaymentDetail::find($payment_detail_id)->delete();
            }
            Payment::find($payment_id)->delete();
        }
    }

    private function get_upcoming_booking_array($booking, $user_type = 'player') {
        $status = $booking->status;

        $session_type = $aval_type = get_session_type($booking->type);

        $time_start = stripslashes($booking->time_start);
        $time_end = stripslashes($booking->time_end);

        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $booking_row = array();
        //$booking_row["booking_id"] = $booking->id;
        $booking_row["session_id"] = $booking->session_id;
        $booking_row["date"] = date('M d, Y', $time_start);
        $booking_row["start_time"] = date('h:i A', $time_start);
        $booking_row["session_type"] = $session_type;
        if ($session_type != $aval_type) {
            $booking_row["session_type"] = $booking_row["session_type"] . " - " . $aval_type;
        }
        if ($user_type == 'player') {
            $booking_row["coach"] = get_user_name(stripslashes($booking->user_id));
        }
        else {
            $booking_row["booked_by"] = get_user_name(stripslashes($booking->req_user_id));
        }
        $booking_row["status"] = get_booking_status_details($status);
        $booking_row["session_status_id"] = $booking->session_status;
        $booking_row["booking_status_id"] = $booking->booking_status;
        $is_deletable = 0;
        $is_cancelable = 0;
        if (empty($booking->booking_status)) {
            $is_deletable = 1;
        }
        if ($booking->booking_status == 1 || $booking->booking_status == 2) {
            $is_cancelable = 1;
        }
        $booking_row["is_deletable"] = $is_deletable;
        $booking_row["is_cancelable"] = $is_cancelable;
        $booking_row["price"] = stripslashes($booking->price);
        //$booking_row["color"] = stripslashes($booking->color);
        //$booking_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session_user_id);

        return $booking_row;
    }

    private function get_past_booking_array($booking, $user_type = 'player') {
        $status = $booking->status;

        $session_type = $aval_type = get_session_type($booking->type);

        $time_start = stripslashes($booking->time_start);
        $time_end = stripslashes($booking->time_end);

        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $booking_row = array();
        //$booking_row["booking_id"] = $booking->id;
        $booking_row["session_id"] = $booking->session_id;
        $booking_row["date"] = date('M d, Y', $time_start);
        $booking_row["start_time"] = date('h:i A', $time_start);
        $booking_row["date_time"] = date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end);
        $booking_row["session_type"] = $session_type;
        if ($session_type != $aval_type) {
            //$booking_row["session_type"] = " - " . $aval_type;
            $booking_row["session_type"] = $booking_row["session_type"] . " - " . $aval_type;
        }
        if ($user_type == 'player') {
            $booking_row["coach"] = get_user_name(stripslashes($booking->user_id));
        }
        else {
            $booking_row["booked_by"] = get_user_name(stripslashes($booking->req_user_id));
        }
        $booking_row["status"] = get_booking_status_details($status);
        $booking_row["session_status_id"] = $booking->session_status;
        $booking_row["booking_status_id"] = $booking->booking_status;
        $is_deletable = 0;
        $is_cancelable = 0;
        if (empty($booking->booking_status)) {
            $is_deletable = 1;
        }
        if ($booking->booking_status == 1 || $booking->booking_status == 2) {
            $is_cancelable = 1;
        }
        $booking_row["is_deletable"] = $is_deletable;
        $booking_row["is_cancelable"] = $is_cancelable;
        $booking_row["price"] = stripslashes($booking->price);
        //$booking_row["color"] = stripslashes($booking->color);
        //$booking_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session_user_id);

        $payment_id = $booking->payment_id;

        if (($status == 2 || $status == 7 || $status == 8 || $status == 9) && $payment_id != 0) {
            $transaction_id = get_payment_data('transaction_id', $payment_id);

            if ($transaction_id != '') {
                $booking_row["payment_date"] = date('M d, Y', get_payment_data('pay_date', $payment_id));

                $booking_row["transaction_id"] = $transaction_id;
            }
        }

        return $booking_row;
    }

    private function get_booking_user_array($User_ids, $user_type = 'player') {
        $users_array = array();
        foreach ($User_ids as $user_id) {
            $User = UserPersonal::where('user_id', $user_id)->orderby('id', 'desc')->first();
            if (!empty($User)) {
                $array = array();
                $array["f_name"] = $User->first_name;
                $array["l_name"] = $User->last_name;
                $array["contact_number"] = $User->contact_number;
                $array["about_me"] = $User->about_me;
                $array["meetinglink"] = $User->meetinglink;
                $array["zip_code"] = $User->zip_code;
                $array["image"] = env('APP_URL') . "/uploads/images/" . $User->coachpic;

                $users_array[] = $array;
            }
        }
        return $users_array;
    }

    private function pay_now(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $auth_key = $this->_Token;
        if ($user_type == $this->_PLAYER_USER || $user_type == $this->_PARENT_USER) {
            if (isset($request->slots) && ltrim(rtrim($request->slots)) != '') {
                $this->delete_unpaid_payments();

                $slots = ltrim(rtrim($request->slots));
                $slots_array = explode(',', $slots);
                if (count($slots_array) > 0) {
                    $slot_exists = 0;
                    $availabilities = Session::whereIn('id', $slots_array)->get();
                    foreach ($availabilities as $availability) {
                        $slot_exists = 1;
                    }

                    if ($slot_exists == 1) {
                        if ($user_type == $this->_PLAYER_USER) {
                            return $this->pay_by_player($request, $User, $slots_array, $availabilities);
                        }
                        elseif ($user_type == $this->_PARENT_USER) {
                            return $this->pay_by_parent($request, $User, $slots_array, $availabilities);
                        }
                    }
                    else {
                        return $this->sendError("Sesions Not Found");
                    }
                }
                else {
                    return $this->sendError("Missing Parameters");
                }
            }
            else {
                return $this->sendError("Missing Parameters");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function pay_by_player(Request $request, $User, $slots, $availabilities) {
        return $this->record_payment($User, $slots);
    }

    private function pay_by_parent(Request $request, $User, $slots, $availabilities) {
        $session_ids = $slots;
        $parent_user_ids = $this->get_parent_user_ids($User);

        $slots = array();
        $bookings = Booking::select(['session_id'])->whereIn('session_id', $session_ids)->whereIn('req_user_id', $parent_user_ids)->get();
        foreach ($bookings as $booking) {
            $slots[] = $booking->session_id;
        }

        return $this->record_payment($User, $slots);
    }

    private function record_payment($User, $slots) {
        $auth_key = $this->_Token;

        $slot_exists = 0;
        $message = "No sessions found";
        if (count($slots) > 0) {
            $response = $this->save_booking_payments($slots, $User);
            $message = $response->message;
            if ($response->status) {
                $slot_exists = 1;
                $payment_id = $response->payment_id;
                $payment_link = route('payments.pay_now', $payment_id) . "&auth_key=" . $auth_key;

                $response = [
                    'payment_link' => $payment_link
                ];

                return $this->sendResponse($response, $message);
            }
        }
        if ($slot_exists == 0) {
            return $this->sendError($message);
        }
    }

    private function my_payments(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER || $user_type == $this->_PARENT_USER) {
            if (isset($request->listing) && $request->listing != "") {
                $listing = $request->listing;
            }
            else {
                $listing = "both";
            }

            if ($listing == "upcoming" || $listing == "past" || $listing == "both") {
                if ($user_type == $this->_COACH_USER) {
                    return $this->payments_by_coach($request, $User, $listing);
                }
                elseif ($user_type == $this->_PLAYER_USER) {
                    return $this->payments_by_player($request, $User, $listing);
                }
                elseif ($user_type == $this->_PARENT_USER) {
                    return $this->payments_by_parent($request, $User, $listing);
                }
            }
            else {
                return $this->sendError("Incorrect Listing value");
            }
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function payments_by_player(Request $request, $User, $listing) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        $upcoming_listing = 0;
        $past_listing = 0;

        $current_time = time();

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

        $offset = (($page_no - 1) * $limit);

        $upcoming_bookings = array();

        $upcoming_users = array();

        $past_bookings = array();

        $past_users = array();

        if ($listing == "upcoming" || $listing == "both") {
            $upcoming_listing = 1;

            $countAll = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.req_user_id', $user_id)
                    ->where('sessions.time_start', '>=', $current_time)
                    ->where('bookings.status', 1)
                    ->select(['bookings.id'])
                    ->count();

            $upcoming_total_records = $countAll;
            $upcoming_total_pages = ceil($upcoming_total_records / $limit);

            $bookings = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.req_user_id', $user_id)
                    ->where('sessions.time_start', '>=', $current_time)
                    ->where('bookings.status', 1)
                    ->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('time_start', 'asc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $upcoming_count = 0;
            $user_ids_array = array();

            foreach ($bookings as $booking) {
                $upcoming_count++;

                $upcoming_bookings[] = $this->get_upcoming_booking_array($booking, 'player');

                $session_user_id = stripslashes($booking->user_id);
                if (!in_array($session_user_id, $user_ids_array) && $session_user_id != 0) {
                    $user_ids_array[] = $session_user_id;
                }
            }

            $upcoming_users = $this->get_booking_user_array($user_ids_array);
        }

        if ($listing == "past" || $listing == "both") {
            $past_listing = 1;
            $status_array = [2, 7, 8, 9];

            $countAll = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.req_user_id', $user_id)
                    ->whereIN('bookings.status', $status_array)
                    ->where('sessions.time_start', '<', $current_time)
                    ->select(['bookings.id'])
                    ->count();

            $past_total_records = $countAll;
            $past_total_pages = ceil($past_total_records / $limit);

            $bookings = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.req_user_id', $user_id)
                    ->whereIN('bookings.status', $status_array)
                    ->where('sessions.time_start', '<', $current_time)
                    ->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'bookings.payment_id', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('time_start', 'desc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $past_count = 0;
            $user_ids_array = array();

            foreach ($bookings as $booking) {
                $past_count++;

                $past_bookings[] = $this->get_past_booking_array($booking, 'player');

                $session_user_id = stripslashes($booking->user_id);
                if (!in_array($session_user_id, $user_ids_array) && $session_user_id != 0) {
                    $user_ids_array[] = $session_user_id;
                }
            }

            $past_users = $this->get_booking_user_array($user_ids_array);
        }

        $response = array();
        $response['page_no'] = $page_no;
        $response['limit'] = $limit;
        $response['listing'] = $listing;

        if ($upcoming_listing == 1) {
            $response['upcoming_total_records'] = $upcoming_total_records;
            $response['upcoming_current_count'] = $upcoming_count;
            $response['upcoming_total_no_of_pages'] = $upcoming_total_pages;

            $response['upcoming_payment_details'] = $upcoming_bookings;
            $response['upcoming_coach_details'] = $upcoming_users;
        }

        if ($past_listing == 1) {
            $response['past_total_records'] = $past_total_records;
            $response['past_current_count'] = $past_count;
            $response['past_total_no_of_pages'] = $past_total_pages;

            $response['past_payment_details'] = $past_bookings;
            $response['past_coach_details'] = $past_users;
        }

        return $this->sendResponse($response, 'Successfully returned Sessions data');
    }

    private function payments_by_coach(Request $request, $User, $listing) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        $upcoming_listing = 0;
        $past_listing = 0;

        $current_time = time();

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

        $offset = (($page_no - 1) * $limit);

        $upcoming_bookings = array();

        $upcoming_users = array();

        $past_bookings = array();

        $past_users = array();

        if ($listing == "upcoming" || $listing == "both") {
            $upcoming_listing = 1;

            $countAll = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.user_id', $user_id)
                    ->where('sessions.time_start', '>=', $current_time)
                    ->where('bookings.status', 1)
                    ->select(['bookings.id'])
                    ->count();

            $upcoming_total_records = $countAll;
            $upcoming_total_pages = ceil($upcoming_total_records / $limit);

            $bookings = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.user_id', $user_id)
                    ->where('sessions.time_start', '>=', $current_time)
                    ->where('bookings.status', 1)
                    ->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('time_start', 'asc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $upcoming_count = 0;
            $user_ids_array = array();

            foreach ($bookings as $booking) {
                $upcoming_count++;

                $upcoming_bookings[] = $this->get_upcoming_booking_array($booking, 'coach');

                $player_id = $booking->req_user_id;
                if (!in_array($player_id, $user_ids_array) && $player_id != 0) {
                    $user_ids_array[] = $player_id;
                }
            }

            $upcoming_users = $this->get_booking_user_array($user_ids_array, 'coach');
        }


        if ($listing == "past" || $listing == "both") {
            $past_listing = 1;
            $status_array = [2, 7, 8, 9];

            $countAll = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.user_id', $user_id)
                    ->where('sessions.time_start', '<', $current_time)
                    ->whereIN('bookings.status', $status_array)
                    ->select(['bookings.id'])
                    ->count();

            $past_total_records = $countAll;
            $past_total_pages = ceil($past_total_records / $limit);

            $bookings = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->where('bookings.user_id', $user_id)
                    ->where('sessions.time_start', '<', $current_time)
                    ->whereIN('bookings.status', $status_array)
                    ->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'bookings.payment_id', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('time_start', 'desc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $past_count = 0;
            $user_ids_array = array();

            foreach ($bookings as $booking) {
                $past_count++;
                $past_bookings[] = $this->get_past_booking_array($booking, 'coach');

                $player_id = $booking->req_user_id;
                if (!in_array($player_id, $user_ids_array) && $player_id != 0) {
                    $user_ids_array[] = $player_id;
                }
            }

            $past_users = $this->get_booking_user_array($user_ids_array, 'coach');
        }


        $response = array();
        $response['page_no'] = $page_no;
        $response['limit'] = $limit;
        $response['listing'] = $listing;

        if ($upcoming_listing == 1) {
            $response['upcoming_total_records'] = $upcoming_total_records;
            $response['upcoming_current_count'] = $upcoming_count;
            $response['upcoming_total_no_of_pages'] = $upcoming_total_pages;

            $response['upcoming_payment_details'] = $upcoming_bookings;
            $response['upcoming_Player_details'] = $upcoming_users;
        }

        if ($past_listing == 1) {
            $response['past_total_records'] = $past_total_records;
            $response['past_current_count'] = $past_count;
            $response['past_total_no_of_pages'] = $past_total_pages;

            $response['past_payment_details'] = $past_bookings;
            $response['past_Player_details'] = $past_users;
        }

        return $this->sendResponse($response, 'Successfully returned Sessions data');
    }

    private function payments_by_parent(Request $request, $User, $listing) {
        $user_id = $User->id;
        $user_type = $User->user_type;

        $parent_user_ids = $this->get_parent_user_ids($User);

        $upcoming_listing = 0;
        $past_listing = 0;

        $current_time = time();

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

        $offset = (($page_no - 1) * $limit);

        $upcoming_bookings = array();

        $upcoming_users = array();

        $past_bookings = array();

        $past_users = array();

        if ($listing == "upcoming" || $listing == "both") {
            $upcoming_listing = 1;

            $countAll = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->whereIn('bookings.req_user_id', $parent_user_ids)
                    ->where('sessions.time_start', '>=', $current_time)
                    ->where('bookings.status', 1)
                    ->select(['bookings.id'])
                    ->count();

            $upcoming_total_records = $countAll;
            $upcoming_total_pages = ceil($upcoming_total_records / $limit);

            $bookings = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->whereIn('bookings.req_user_id', $parent_user_ids)
                    ->where('sessions.time_start', '>=', $current_time)
                    ->where('bookings.status', 1)
                    ->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('time_start', 'asc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $upcoming_count = 0;
            $user_ids_array = array();

            foreach ($bookings as $booking) {
                $upcoming_count++;

                $upcoming_bookings[] = $this->get_upcoming_booking_array($booking, 'player');

                $session_user_id = stripslashes($booking->user_id);
                if (!in_array($session_user_id, $user_ids_array) && $session_user_id != 0) {
                    $user_ids_array[] = $session_user_id;
                }
            }

            $upcoming_users = $this->get_booking_user_array($user_ids_array);
        }

        if ($listing == "past" || $listing == "both") {
            $past_listing = 1;
            $status_array = [2, 7, 8, 9];

            $countAll = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->whereIN('bookings.req_user_id', $parent_user_ids)
                    ->where('sessions.time_start', '<', $current_time)
                    ->whereIN('bookings.status', $status_array)
                    ->select(['bookings.id'])
                    ->count();

            $past_total_records = $countAll;
            $past_total_pages = ceil($past_total_records / $limit);

            $bookings = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->whereIN('bookings.req_user_id', $parent_user_ids)
                    ->whereIN('bookings.status', $status_array)
                    ->where('sessions.time_start', '<', $current_time)
                    ->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'bookings.payment_id', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('time_start', 'desc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

            $past_count = 0;
            $user_ids_array = array();

            foreach ($bookings as $booking) {
                $past_count++;

                $past_bookings[] = $this->get_past_booking_array($booking, 'player');

                $session_user_id = stripslashes($booking->user_id);
                if (!in_array($session_user_id, $user_ids_array) && $session_user_id != 0) {
                    $user_ids_array[] = $session_user_id;
                }
            }

            $past_users = $this->get_booking_user_array($user_ids_array);
        }

        $response = array();
        $response['page_no'] = $page_no;
        $response['limit'] = $limit;
        $response['listing'] = $listing;

        if ($upcoming_listing == 1) {
            $response['upcoming_total_records'] = $upcoming_total_records;
            $response['upcoming_current_count'] = $upcoming_count;
            $response['upcoming_total_no_of_pages'] = $upcoming_total_pages;

            $response['upcoming_payment_details'] = $upcoming_bookings;
            $response['upcoming_coach_details'] = $upcoming_users;
        }

        if ($past_listing == 1) {
            $response['past_total_records'] = $past_total_records;
            $response['past_current_count'] = $past_count;
            $response['past_total_no_of_pages'] = $past_total_pages;

            $response['past_payment_details'] = $past_bookings;
            $response['past_coach_details'] = $past_users;
        }

        return $this->sendResponse($response, 'Successfully returned Sessions data');
    }

}
