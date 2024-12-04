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
use App\Models\Booking;
use App\Models\Session;

class PlayerApiController extends BaseController {

    public function index(Request $request, $action = 'listing') {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);
            
            $current_time = time();
            update_all_bookings($current_time);

            switch ($action) {
                case 'player_dashboard': {
                        $user_type = $User->user_type;
                        if ($user_type == $this->_PARENT_USER && isset($request->user_id) && $request->user_id != '') {
                            $user_id = $request->user_id;
                            $parent_user_ids = $this->get_parent_user_ids($User);
                            if (in_array($user_id, $parent_user_ids)) {
                                $User = User::find($user_id);
                                return $this->player_dashboard($request, $User);
                            }
                            else {
                                return $this->sendError('User not found.');
                            }
                        }
                        else {
                            return $this->player_dashboard($request, $User);
                        }
                    }
                    break;

                case 'player_my_trainings': {
                        return $this->player_my_trainings($request, $User);
                    }
                    break;

                case 'parent_dashboard': {
                        return $this->parent_dashboard($request, $User);
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

    private function get_all_booking_array($booking, $user_type = 'player') {

        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);
        $session_limit = (60 * 10);
        $current_time = time();

        $status = $booking->status;

        $session_type = $aval_type = get_session_type($booking->type);

        $time_start = stripslashes($booking->time_start);
        $time_end = stripslashes($booking->time_end);
        $time_to_go = ($time_start - $current_time);

        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $booking_row = array();
        $booking_row["booking_id"] = $booking->id;
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
        elseif ($user_type == 'parent') {
            $booking_row["booked_by"] = get_user_name(stripslashes($booking->req_user_id));
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
        $booking_row["session_price"] = stripslashes($booking->price);
        //$booking_row["color"] = stripslashes($booking->color);
        //$booking_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session_user_id);


        $session_user_id = stripslashes($booking->user_id);
        $public_url = get_user_profile_data('meetinglink', $session_user_id);

        $have_link = 0;

        if ($time_start > $current_time) {

            $booking_row["action"] = "Cancel";
            $booking_row["cancel"] = 1;
        }
        else {

            $booking_row["action"] = "none";
            $booking_row["time_to_go"] = get_expiry($time_start);
        }


        return $booking_row;
    }

    private function get_past_session_array($session, $user_type = 'player') {
        $status = $session->status;

        $session_type = $aval_type = get_session_type($session->type);

        $time_start = stripslashes($session->time_start);
        $time_end = stripslashes($session->time_end);
        $coach_feedback = 0;
        $player_feedback = 0;
        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $session_row = array();
        $session_row["session_id"] = $session->id;
        $session_row["date"] = date('M d, Y', $time_start);
        $session_row["start_time"] = date('h:i A', $time_start);
        $session_row["date_time"] = date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end);
        $session_row["session_type"] = $session_type;
        if ($session_type != $aval_type) {
            $session_row["session_type"] = $session_row["session_type"] . " - " . $aval_type;
        }
        if ($user_type == 'player') {
            $session_row["coach"] = get_user_name(stripslashes($session->user_id));
        }
        elseif ($user_type == 'parent') {
            $session_row["coach"] = get_user_name(stripslashes($session->user_id));
            $session_row["booked_by"] = get_user_name(stripslashes($session->req_user_id));
        }
        else {
            $session_row["booked_by"] = get_user_name(stripslashes($session->req_user_id));
        }
        $session_row["status"] = get_booking_status_details($status);
        $session_row["session_status_id"] = $session->session_status;
        $session_row["booking_status_id"] = $session->booking_status;
        $is_deletable = 0;
        $is_cancelable = 0;
        if (empty($session->booking_status)) {
            $is_deletable = 1;
        }
        if ($session->booking_status == 1 || $session->booking_status == 2) {
            $is_cancelable = 1;
        }
        $session_row["is_deletable"] = $is_deletable;
        $session_row["is_cancelable"] = $is_cancelable;
        $session_row["price"] = stripslashes($session->price);
        $session_row["color"] = stripslashes($session->color);
        $session_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session->user_id);
        if ($session->player_feedback != 0) {

            $coach_feedback = $session->coach_feedback;
        }
        if ($session->player_feedback != 0) {

            $player_feedback = $session->player_feedback;
        }
        $session_row['coach_feedback'] = $coach_feedback;
        $session_row['player_feedback'] = $player_feedback;

        $payment_id = $session->payment_id;
        $coach_delivery = $session->coach_delivery;

        if (($status == 2 && $status == 0)) {
            $session_row['action'] = "Feedback";
        }
        else {
            if ($coach_delivery > 0) {
                $session_row['action'] = "View Feedback";
            }
            else {
                $session_row['action'] = "Not Delivered";
            }
        }

        return $session_row;
    }

    private function get_upcoming_booking_array($booking, $user_type = 'player') {

        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);
        $session_limit = (60 * 10);
        $current_time = time();

        $status = $booking->status;

        $session_type = $aval_type = get_session_type($booking->type);

        $time_start = stripslashes($booking->time_start);
        $time_end = stripslashes($booking->time_end);
        $time_to_go = ($time_start - $current_time);

        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $booking_row = array();
        $booking_row["booking_id"] = $booking->id;
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
        elseif ($user_type == 'parent') {
            $booking_row["booked_by"] = get_user_name(stripslashes($booking->req_user_id));
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
        $booking_row["session_price"] = stripslashes($booking->price);
        //$booking_row["color"] = stripslashes($booking->color);
        //$booking_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session_user_id);


        $session_user_id = stripslashes($booking->user_id);
        $public_url = get_user_profile_data('meetinglink', $session_user_id);

        $have_link = 0;

        if ($status == 2) {
            $show_link = 0;

            if ($time_to_go <= $session_limit || ($time_start >= $current_time && $time_end <= $current_time)) {
                $show_link = 1;
            }
            if ($time_start > $current_time) {
                $booking_row["action"] = get_expiry($time_start);
            }
            elseif ($show_link == 1) {
                // echo " Session Started ";
            }
            elseif ($show_link == 0) {
                $booking_row["action"] = " Session Expired ";
            }
            if ($show_link == 1) {
                $have_link = 1;
            }
        }
        elseif ($time_start > $current_time) {
            $booking_row["action"] = get_expiry($time_start);
        }
        else {
            $booking_row["action"] = "Session Expired";
        }

        $booking_row["have_link"] = $have_link;
        if ($have_link == 1) {

            $booking_row["action"] = "Session Started";
            $booking_row["link"] = $public_url;
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
                if ($user_type == 'player')
                    $array["meetinglink"] = $User->meetinglink;
                $array["zip_code"] = $User->zip_code;
                $array["image"] = env('APP_URL') . "/uploads/images/" . $User->coachpic;

                $users_array[] = $array;
            }
        }
        return $users_array;
    }

    private function player_dashboard(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PLAYER_USER) {
            $listing = (isset($request->listing) && $request->listing != "") ? $request->listing : "both";
            $page_no = (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) ? $request->page_no : 1;
            $limit = (isset($request->limit) && $request->limit != "" && $request->limit != 0)  ? $request->limit : 5;
            $offset = ($page_no - 1) * $limit;
            $order = (isset($request->order) && $request->order == "asc") ? "asc" : "desc";

            //$session_start = strtotime(date('d-m-Y'));
            //$_time_24 = (24 * 60 * 60);
            //$session_end = ($session_start + $_time_24);
            //$session_limit = (60 * 10);
            $current_time = time();

            $upcoming_total_no_of_pages = $upcoming_total_records = $upcoming_count = $upcoming_listing = 0;
            $upcoming_sessions = array();
            $upcoming_users = array();
            
            $all_total_no_of_pages = $all_total_records = $all_current_count = $all_listing = 0;
            $all_sessions = array();
            $all_users = array();

            if ($listing == "upcoming" || $listing == "both") {
                $upcoming_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'dashboard_upcoming');

                $query = Session::join('bookings', 'sessions.id', '=', 'bookings.session_id')                        
                        ->where([['bookings.req_user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['sessions.booked', 1]])
                        ->orWhere([['bookings.req_user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['bookings.status', 'IN', $status_array]]);

                $upcoming_total_records = $query->select(['sessions.id'])->count();
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $bookings = $query->select(
                                'sessions.user_id',
                                'sessions.type',
                                'sessions.price',
                                'sessions.color',
                                'sessions.time_start',
                                'sessions.time_end',
                                'bookings.id',
                                'bookings.session_id',
                                'bookings.req_user_id',
                                'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'
                        )
                        ->orderBy('sessions.time_start', 'asc')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();

                $upcoming_count = $bookings->count();
                foreach ($bookings as $booking) {
                    $session_user_id = stripslashes($booking->user_id);

                    if (!in_array($session_user_id, $upcoming_users) && $session_user_id != 0) {
                        $upcoming_users[] = $session_user_id;
                    }

                    $upcoming_sessions[] = $this->get_upcoming_booking_array($booking, $this->login_user_type == 4 ? 'parent' : 'player');
                }
                $upcoming_users = $this->get_booking_user_array($upcoming_users, $this->login_user_type == 4 ? 'parent' : 'player');
            }



            if ($listing == "all" || $listing == "both") {

                $all_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'dashboard_all');

                $query = Booking::join('sessions', 'bookings.session_id', '=', 'sessions.id')                          
                        ->where([['bookings.req_user_id', $user_id], ['sessions.time_start', '<=', $current_time], ['sessions.booked', 1]])
                        ->orWhere([['bookings.req_user_id', $user_id], ['bookings.status', 'NOT IN', $status_array]]);

                $all_total_records = $query->select(['sessions.id'])->count();
                $all_total_no_of_pages = ceil($all_total_records / $limit);

                $bookings = $query->select('bookings.id', 'bookings.session_id', 'bookings.status', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'sessions.status as session_status', 'bookings.status as booking_status')
                        ->orderby('sessions.time_start', $order)
                        ->skip($offset)
                        ->take($limit)
                        ->get();

                $all_current_count = $bookings->count();

                foreach ($bookings as $booking) {

                    $session_user_id = stripslashes($booking->user_id);

                    if (!in_array($session_user_id, $all_users) && $session_user_id != 0) {
                        $all_users[] = $session_user_id;
                    }

                    $all_sessions[] = $this->get_all_booking_array($booking, $this->login_user_type == 4 ? 'parent' : 'player');
                }
                $all_users = $this->get_booking_user_array($all_users, $this->login_user_type == 4 ? 'parent' : 'player');
            }

            $data = array();

            if ($upcoming_listing == 1 || $all_listing == 1) {
                $data['page_no'] = $page_no;
                $data['limit'] = $limit;
                $data['listing'] = $listing;
            }
            else {
                return $this->sendError("Incorrect Listing value");
            }

            if ($upcoming_listing == 1) {
                $data['upcoming_total_records'] = $upcoming_total_records;
                $data['upcoming_current_count'] = $upcoming_count;
                $data['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $data['upcoming_session_details'] = $upcoming_sessions;
                $data['upcoming_Coaches_details'] = $upcoming_users;
            }

            if ($all_listing == 1) {
                $data['all_total_records'] = $all_total_records;
                $data['all_current_count'] = $all_current_count;
                $data['all_total_no_of_pages'] = $all_total_no_of_pages;

                $data['all_session_details'] = $all_sessions;
                $data['all_Coaches_details'] = $all_users;
            }
            return $this->sendResponse($data, 'Successfully returned Sessions data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function player_my_trainings(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PLAYER_USER) {
            $listing = (isset($request->listing) && $request->listing != "") ? $request->listing : "both";
            $page_no = (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) ? $request->page_no : 1;
            $limit = (isset($request->limit) && $request->limit != "" && $request->limit != 0)  ? $request->limit : 5;
            $offset = ($page_no - 1) * $limit;

            //$_24_time = (time() - (24 * 60 * 60));
            $current_time = time();

            $upcoming_total_no_of_pages = $upcoming_total_records = $upcoming_current_count = $upcoming_listing = 0;
            $upcoming_sessions = array();
            $upcoming_users = array();
            
            $past_total_no_of_pages = $past_total_records = $past_current_count = $past_listing = 0;
            $past_sessions = array();
            $past_users = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'session_upcoming');

                $query = Session::join('bookings', 'sessions.id', '=', 'bookings.session_id')                        
                        ->where([['bookings.req_user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['sessions.booked', 1]])
                        ->orWhere([['bookings.req_user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['bookings.status', 'IN', $status_array]]);

                $upcoming_total_records = $query->select(['sessions.id'])->count();
                $upcoming_total_no_of_pages = ceil($upcoming_total_records / $limit);

                $bookings = $query->select('bookings.id', 'bookings.session_id', 'bookings.status', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'sessions.status as session_status', 'bookings.status as booking_status')
                        ->orderBy('sessions.time_start', 'asc')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();
                $upcoming_current_count = $bookings->count();
                foreach ($bookings as $booking) {

                    $session_user_id = (int)$booking->user_id;

                    if (!in_array($session_user_id, $upcoming_users) && $session_user_id != 0) {
                        $upcoming_users[] = $session_user_id;
                    }

                    $upcoming_sessions[] = $this->get_all_booking_array($booking, $this->login_user_type == 4 ? 'parent' : 'player');
                }
                $upcoming_users = $this->get_booking_user_array($upcoming_users, $this->login_user_type == 4 ? 'parent' : 'player');
            }

            if ($listing == "past" || $listing == "both") {

                $past_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'session_all');

                $query = Session::join('bookings', 'sessions.id', '=', 'bookings.session_id')                        
                        ->where([['bookings.req_user_id', $user_id], ['sessions.time_start', '<=', $current_time], ['sessions.booked', 1]])
                        ->orWhere([['bookings.req_user_id', $user_id], ['bookings.status', 'NOT IN', $status_array]]);

                $past_total_records = $query->select(['sessions.id'])->count();
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $bookings = $query->select(
                                'sessions.user_id',
                                'sessions.type',
                                'sessions.price',
                                'sessions.color',
                                'sessions.time_start',
                                'sessions.time_end',
                                'bookings.id',
                                'bookings.session_id',
                                'bookings.req_user_id',
                                'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'
                        )
                        ->orderBy('sessions.time_start', 'desc')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();
                $past_current_count = $bookings->count();

                foreach ($bookings as $booking) {
                    $session_user_id = (int)$booking->user_id;

                    if (!in_array($session_user_id, $past_users) && $session_user_id != 0) {
                        $past_users[] = $session_user_id;
                    }

                    $past_sessions[] = $this->get_past_session_array($booking, $this->login_user_type == 4 ? 'parent' : 'player');
                }
                $past_users = $this->get_booking_user_array($past_users, $this->login_user_type == 4 ? 'parent' : 'player');
            }

            $data = array();
            if ($upcoming_listing == 1 || $past_listing == 1) {
                $data['page_no'] = $page_no;
                $data['limit'] = $limit;
                $data['listing'] = $listing;
            }
            else {
                return $this->sendError("Incorrect Listing value");
            }

            if ($upcoming_listing == 1) {
                $data['upcoming_total_records'] = $upcoming_total_records;
                $data['upcoming_current_count'] = $upcoming_current_count;
                $data['upcoming_total_no_of_pages'] = $upcoming_total_no_of_pages;

                $data['upcoming_session_details'] = $upcoming_sessions;
                $data['upcoming_coach_details'] = $upcoming_users;
            }

            if ($past_listing == 1) {
                $data['past_total_records'] = $past_total_records;
                $data['past_current_count'] = $past_current_count;
                $data['past_total_no_of_pages'] = $past_total_no_of_pages;

                $data['past_session_details'] = $past_sessions;
                $data['past_coach_details'] = $past_users;
            }
            return $this->sendResponse($data, 'Successfully returned Sessions data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function parent_dashboard(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_PARENT_USER) {
            $listing = (isset($request->listing) && $request->listing != "") ? $request->listing : "both";
            $page_no = (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) ? $request->page_no : 1;
            $limit = (isset($request->limit) && $request->limit != "" && $request->limit != 0)  ? $request->limit : 5;
            $offset = ($page_no - 1) * $limit;
            $order = (isset($request->order) && $request->order == "asc") ? "asc" : "desc";

            $unpaid_total_no_of_pages = $unpaid_total_records = $unpaid_current_count = $unpaid_listing = 0;
            $unpaid_sessions = array();
            $unpaid_users = array();

            $paid_total_no_of_pages = $paid_total_records = $paid_current_count = $paid_listing = 0;
            $paid_sessions = array();
            $paid_users = array();

            $parent_user_ids = $this->get_parent_user_ids($User);
            if (count($parent_user_ids) > 0) {

                //$session_start = time();
                //$_time_24 = (24 * 60 * 60);
                //$session_end = ($session_start + $_time_24);
                //$session_limit = (60 * 10);
                $current_time = time();

                if ($listing == "unpaid" || $listing == "both") {
                    $unpaid_listing = 1;
                    $status_array = get_sessesion_status_array($user_type, 'dashboard_unpaid');
                    
                    $query = Session::join('bookings', 'sessions.id', '=', 'bookings.session_id')
                            ->whereIN('bookings.req_user_id', $parent_user_ids)
                            ->where('sessions.time_start', '>=', $current_time)
                            ->whereIN('bookings.status', $status_array);

                    $unpaid_total_records = $query->select(['sessions.id'])->count();
                    $unpaid_total_no_of_pages = ceil($unpaid_total_records / $limit);

                    $bookings = $query->select(
                                    'sessions.user_id',
                                    'sessions.type',
                                    'sessions.price',
                                    'sessions.color',
                                    'sessions.time_start',
                                    'sessions.time_end',
                                    'bookings.id',
                                    'bookings.session_id',
                                    'bookings.req_user_id',
                                    'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'
                            )
                            ->orderBy('sessions.time_start', 'asc')
                            ->limit($limit)
                            ->offset($offset)
                            ->get();

                    $unpaid_current_count = $bookings->count();
                    foreach ($bookings as $booking) {
                        $session_user_id = (int)$booking->user_id;

                        if (!in_array($session_user_id, $unpaid_users) && $session_user_id != 0) {
                            $unpaid_users[] = $session_user_id;
                        }

                        $unpaid_sessions[] = $this->get_upcoming_booking_array($booking, $this->login_user_type == 4 ? 'parent' : 'player');
                    }
                    $unpaid_users = $this->get_booking_user_array($unpaid_users, $this->login_user_type == 4 ? 'parent' : 'player');
                }

                if ($listing == "paid" || $listing == "both") {

                    $paid_listing = 1;
                    $status_array = get_sessesion_status_array($user_type, 'dashboard_paid');
                    
                    $query = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                            ->whereIN('bookings.req_user_id', $parent_user_ids)
                            ->whereIN('bookings.status', $status_array);

                    $paid_total_records = $query->select(['sessions.id'])->count();
                    $paid_total_no_of_pages = ceil($paid_total_records / $limit);

                    $bookings = $query->select(['sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'bookings.payment_id', 'sessions.status as session_status', 'bookings.status as booking_status'])
                        ->orderby('sessions.time_start', $order)
                        ->skip($offset)
                        ->take($limit)
                        ->get();

                    $paid_current_count = $bookings->count();

                    foreach ($bookings as $booking) {
                        $session_user_id = (int)$booking->user_id;

                        if (!in_array($session_user_id, $paid_users) && $session_user_id != 0) {
                            $paid_users[] = $session_user_id;
                        }

                        $paid_sessions[] = $this->get_all_booking_array($booking, $this->login_user_type == 4 ? 'parent' : 'player');
                    }
                    $paid_users = $this->get_booking_user_array($paid_users, $this->login_user_type == 4 ? 'parent' : 'player');
                }
            }

            $data = array();

            if ($unpaid_listing == 1 || $paid_listing == 1) {
                $data['page_no'] = $page_no;
                $data['limit'] = $limit;
                $data['listing'] = $listing;
            }
            else {
                return $this->sendError("Incorrect Listing value");
            }

            if ($unpaid_listing == 1) {
                $data['unpaid_total_records'] = $unpaid_total_records;
                $data['unpaid_current_count'] = $unpaid_current_count;
                $data['unpaid_total_no_of_pages'] = $unpaid_total_no_of_pages;

                $data['unpaid_session_details'] = $unpaid_sessions;
                $data['unpaid_Coaches_details'] = $unpaid_users;
            }

            if ($paid_listing == 1) {
                $data['paid_total_records'] = $paid_total_records;
                $data['paid_current_count'] = $paid_current_count;
                $data['paid_total_no_of_pages'] = $paid_total_no_of_pages;

                $data['paid_session_details'] = $paid_sessions;
                $data['paid_Coaches_details'] = $paid_users;
            }

            return $this->sendResponse($data, 'Successfully returned Sessions data');
        }
        else {
            return $this->sendError("Incorrect User Type");
        }
    }

}
