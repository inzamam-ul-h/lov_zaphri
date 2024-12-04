<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPersonal;
use App\Models\Booking;
use App\Models\Session;

class CoachApiController extends BaseController
{

    public function index(Request $request, $action = 'listing')
    {
        $result = $this->base_authentication($request, $action);
        if ($result['status']) {
            $user_id = $this->_User_Id;
            $User = User::find($user_id);

            $current_time = time();
            update_all_bookings($current_time);

            switch ($action) {
                case 'coach_dashboard': {
                        return $this->coach_dashboard($request, $User);
                    }
                    break;

                case 'coach_my_sessions': {
                        return $this->coach_my_sessions($request, $User);
                    }
                    break;

                default: {
                        return $this->sendError('Invalid Request');
                    }
                    break;
            }
        } else {
            return $this->sendError($result['message']);
        }
    }

    private function get_upcoming_session_array($session, $user_type = 'player')
    {

        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);
        $session_limit = (60 * 10);
        $current_time = time();

        $status = $session->status; //$session->session_status;

        $session_type = $aval_type = get_session_type($session->type);

        $time_start = stripslashes($session->time_start);
        $time_end = stripslashes($session->time_end);
        $time_to_go = ($time_start - $current_time);

        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $session_row = array();
        $session_row["session_id"] = $session->id;
        if ($session->booked == 0) {
            $session_row["booking_id"] = null;
        } else {
            $session_row["booking_id"] = $session->booking_id;
        }
        $session_row["date"] = date('M d, Y', $time_start);
        $session_row["start_time"] = date('h:i A', $time_start);
        $session_row["session_type"] = $session_type;
        if ($session_type != $aval_type) {
            $session_row["session_type"] = $session_row["session_type"] . " - " . $aval_type;
        }
        if ($user_type == 'player') {
            $session_row["coach"] = get_user_name(stripslashes($session->user_id));
        } else {
            $session_row["booked_by"] = get_user_name(stripslashes($session->req_user_id));
        }
        $session_row["status"] = get_booking_status_details($status);
        $session_row["status_id"] = ($status);
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
        //$session_row["color"] = stripslashes($session->color);
        //$session_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session_user_id);


        $session_user_id = stripslashes($session->user_id);
        $public_url = get_user_profile_data('meetinglink', $session_user_id);

        $have_link = 0;

        if ($status != 1 and $status != 2) {

            $upcoming_session_rows["action"] = "Delete";
            $upcoming_session_rows["delete"] = 1;
        } else {
            if ($time_start > $current_time) {

                $upcoming_session_rows["action"] = "Cancel";
                $upcoming_session_rows["cancel"] = 1;
            } else {

                $upcoming_session_rows["action"] = "none";
                $upcoming_session_rows["time_to_go"] = get_expiry($time_start);
            }
        }



        return $session_row;
    }

    private function get_past_session_array($session, $user_type = 'player')
    {
        $status = $session->status; //$session->session_status;

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
        if ($session->booked == 0) {
            $session_row["booking_id"] = null;
        } else {
            $session_row["booking_id"] = $session->booking_id;
        }
        $session_row["date"] = date('M d, Y', $time_start);
        $session_row["start_time"] = date('h:i A', $time_start);
        $session_row["date_time"] = date('M d, Y H:i A', $time_start) . " - " . date('H:i A', $time_end);
        $session_row["session_type"] = $session_type;
        if ($session_type != $aval_type) {
            $session_row["session_type"] = $session_row["session_type"] . " - " . $aval_type;
        }
        if ($user_type == 'player') {
            $session_row["coach"] = get_user_name(stripslashes($session->user_id));
        } else {
            $session_row["booked_by"] = get_user_name(stripslashes($session->req_user_id));
        }
        $session_row["status"] = get_booking_status_details($status);
        $session_row["status_id"] = ($status);
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
        //$session_row["color"] = stripslashes($session->color);
        //$session_row["public_url"] = env('APP_URL') . '/' . get_user_data('public_url', $session_user_id);
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
        } else {
            if ($coach_delivery > 0) {
                $session_row['action'] = "View Feedback";
            } else {
                $session_row['action'] = "Not Delivered";
            }
        }

        return $session_row;
    }

    private function get_upcoming_booking_array($booking, $user_type = 'player')
    {

        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);
        $session_limit = (60 * 10);
        $current_time = time();

        $status = $booking->status; //$booking->session_status;

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
        } else {
            $booking_row["booked_by"] = get_user_name(stripslashes($booking->req_user_id));
        }
        $booking_row["status"] = get_booking_status_details($status);
        $booking_row["status_id"] = ($status);
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
            } elseif ($show_link == 1) {
                // echo " Session Started ";
            } elseif ($show_link == 0) {
                $booking_row["action"] = " Session Expired ";
            }
            if ($show_link == 1) {
                $have_link = 1;
            }
        } elseif ($time_start > $current_time) {
            $booking_row["action"] = get_expiry($time_start);
        } else {
            $booking_row["action"] = "Session Expired";
        }

        $booking_row["have_link"] = $have_link;
        if ($have_link == 1) {

            $booking_row["action"] = "Session Started";
            $booking_row["link"] = $public_url;
        }

        return $booking_row;
    }

    private function get_all_booking_array($booking, $user_type = 'player')
    {
        $status = $booking->status; //$booking->session_status;

        $session_type = $aval_type = get_session_type($booking->type);

        $time_start = stripslashes($booking->time_start);
        $time_end = stripslashes($booking->time_end);

        if ($time_end <= time() && $status == 0) {
            $status = 9;
        }


        $booking_row = array();
        $booking_row["booking_id"] = $booking->id;
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
        } else {
            $booking_row["booked_by"] = get_user_name(stripslashes($booking->req_user_id));
        }
        $booking_row["status"] = get_booking_status_details($status);
        $booking_row["status_id"] = ($status);
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

    private function get_booking_user_array($User_ids, $user_type = 'player')
    {
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

    private function coach_dashboard(Request $request, $User)
    {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER) {

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

            $upcoming_listing = 0;
            $upcoming_bookings = array();
            $upcoming_users = array();

            $all_listing = 0;
            $all_bookings = array();
            $all_users = array();

            if ($listing == "upcoming" || $listing == "both") {
                $upcoming_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'dashboard_upcoming');

                $query = Session::leftjoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                    ->where([['sessions.user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['sessions.booked', 0]])
                    ->orWhere([['sessions.user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['bookings.status', 'IN', $status_array]]);

                $upcoming_total_records = $query->select(['sessions.id'])->count();
                $upcoming_total_pages = ceil($upcoming_total_records / $limit);

                $bookings = $query->select(['bookings.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('sessions.time_start', 'asc')
                    ->skip($offset)
                    ->take($limit)
                    ->get();

                $upcoming_count = $bookings->count();

                foreach ($bookings as $booking) {
                    $upcoming_bookings[] = $this->get_upcoming_booking_array($booking, 'coach');
                    $player_id = (int)$booking->req_user_id;
                    if (!in_array($player_id, $upcoming_users) && $player_id != 0) {
                        $upcoming_users[] = $player_id;
                    }
                }

                $upcoming_users = $this->get_booking_user_array($upcoming_users, 'coach');
            }



            if ($listing == "all" || $listing == "both") {
                $all_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'dashboard_all');

                $query = Session::leftjoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                    ->where([['sessions.user_id', $user_id], ['sessions.time_start', '<=', $current_time], ['sessions.booked', 1]])
                    ->orWhere([['sessions.user_id', $user_id], ['bookings.status', 'NOT IN', $status_array]]);

                $past_total_records = $query->select(['sessions.id'])->count();
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $bookings = $query->select(['bookings.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.time_start', 'sessions.time_end', 'sessions.booked', 'bookings.session_id', 'bookings.req_user_id', 'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'])
                    ->orderby('sessions.time_start', $order)
                    ->skip($offset)
                    ->take($limit)
                    ->get();

                $past_count = $bookings->count();
                foreach ($bookings as $booking) {
                    $booking_id = (int)$booking['id'];

                    $booking_user_id = 0;

                    $booked = (int)$booking['booked'];
                    if ($booked == 1) {
                        $bookings = Booking::where('id', $booking_id)->first();
                        //$status = $booking->status;
                        $booking_id = (int)$booking->id;
                        $booking_user_id = (int)$booking->req_user_id;
                    }


                    if (!in_array($booking_user_id, $all_users) && $booking_user_id != 0) {
                        $all_users[] = $booking_user_id;
                    }


                    $all_bookings[] = $this->get_all_booking_array($booking, $user_type = 'player');
                }
                $all_users = $this->get_booking_user_array($all_users, 'coach');
            }

            $data = array();
            if ($upcoming_listing == 1 || $all_listing == 1) {
                $data['page_no'] = $page_no;
                $data['limit'] = $limit;
                $data['listing'] = $listing;
            } else {
                return $this->sendError("Incorrect Listing value");
            }

            if ($upcoming_listing == 1) {

                $data['upcoming_total_records'] = $upcoming_total_records;
                $data['upcoming_current_count'] = $upcoming_count;
                $data['upcoming_total_pages'] = $upcoming_total_pages;

                $data['upcoming_session_details'] = $upcoming_bookings;
                $data['upcoming_player_details'] = $upcoming_users;
            }

            if ($all_listing == 1) {

                $data['all_total_records'] = $past_total_records;
                $data['all_current_count'] = $past_count;
                $data['all_total_no_of_pages'] = $past_total_no_of_pages;

                $data['all_session_details'] = $all_bookings;
                $data['all_player_details'] = $all_users;
            }

            return $this->sendResponse($data, 'Successfully returned Sessions data');
        } else {
            return $this->sendError("Incorrect User Type");
        }
    }

    private function coach_my_sessions(Request $request, $User)
    {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER) {

            $listing = (isset($request->listing) && $request->listing != "") ? $request->listing : "both";
            $page_no = (isset($request->page_no) && $request->page_no != "" && $request->page_no != 0) ? $request->page_no : 1;
            $limit = (isset($request->limit) && $request->limit != "" && $request->limit != 0)  ? $request->limit : 5;
            $offset = ($page_no - 1) * $limit;

            $current_time = time();

            $upcoming_listing = 0;
            $upcoming_bookings = array();
            $upcoming_users = array();

            $past_listing = 0;
            $past_session_array = array();
            $past_users = array();

            if ($listing == "upcoming" || $listing == "both") {

                $upcoming_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'session_upcoming');

                $query = Session::leftjoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                     ->where([['sessions.user_id', $user_id], ['sessions.time_start', '>=', $current_time]])
                   // ->where([['sessions.user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['sessions.booked', 'IN', [0, 1]]])
                    ->orWhere([['sessions.user_id', $user_id], ['sessions.time_start', '>=', $current_time], ['bookings.status', 'IN', $status_array]]);

                $upcoming_total_records = $query->select(['sessions.id'])->count();
              
                $upcoming_total_pages = ceil($upcoming_total_records / $limit);

                $sessions = $query->select('sessions.id', 'sessions.user_id', 'sessions.type', 'sessions.price', 'sessions.color', 'sessions.booked', 
                'sessions.time_start', 'sessions.time_end', 'bookings.session_id', 'bookings.id as booking_id', 
                'bookings.req_user_id', 'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status')
                    ->orderBy('sessions.time_start', 'asc')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
                $upcoming_count = $sessions->count();
                foreach ($sessions as $session) {
                    $booking_user_id = (int)$session->req_user_id;

                    if (!in_array($booking_user_id, $upcoming_users) && $booking_user_id != 0) {
                        $upcoming_users[] = $booking_user_id;
                    }

                    $upcoming_bookings[] = $this->get_upcoming_session_array($session, 'coach');
                }
                $upcoming_users = $this->get_booking_user_array($upcoming_users, 'coach');
            }

            if ($listing == "past" || $listing == "both") {

                $past_listing = 1;
                $status_array = get_sessesion_status_array($user_type, 'session_all');

                $query = Session::leftjoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                    ->where([['sessions.user_id', $user_id], ['sessions.time_start', '<=', $current_time], ['sessions.booked', 'IN', [0, 1]]])
                    ->orWhere([['sessions.user_id', $user_id], ['bookings.status', 'NOT IN', $status_array]]);

                $past_total_records = $query->select(['sessions.id'])->count();
                $past_total_no_of_pages = ceil($past_total_records / $limit);

                $sessions = $query->select('sessions.id', 'sessions.user_id', 'sessions.booked', 'bookings.req_user_id', 'sessions.price', 'bookings.coach_feedback', 'bookings.id as booking_id', 'bookings.player_feedback', 'bookings.payment_id', 'bookings.coach_delivery', 'sessions.time_start', 'sessions.time_end', 'sessions.id as session_id', 'sessions.status as session_status', 'bookings.status as booking_status')
                    ->orderBy('sessions.time_start', 'desc')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
                $past_count = $sessions->count();

                foreach ($sessions as $session) {
                    $booking_user_id = (int)$session->req_user_id;

                    if (!in_array($booking_user_id, $past_users) && $booking_user_id != 0) {
                        $past_users[] = $booking_user_id;
                    }

                    $past_session_array[] = $this->get_past_session_array($session, 'coach');
                }
                $past_users = $this->get_booking_user_array($past_users, 'coach');
            }
            $data = array();

            if ($upcoming_listing == 1 || $past_listing == 1) {


                $data['page_no'] = $page_no;
                $data['limit'] = $limit;
                $data['listing'] = $listing;
            } else {
                return $this->sendError("Incorrect Listing value");
            }

            if ($upcoming_listing == 1) {
                $data['upcoming_total_records'] = $upcoming_total_records;
                $data['upcoming_current_count'] = $upcoming_count;
                $data['upcoming_total_pages'] = $upcoming_total_pages;

                $data['upcoming_session_details'] = $upcoming_bookings;
                $data['upcoming_player_details'] = $upcoming_users;
            }

            if ($past_listing == 1) {
                $data['past_total_records'] = $past_total_records;
                $data['past_current_count'] = $past_count;
                $data['past_total_no_of_pages'] = $past_total_no_of_pages;

                $data['past_session_details'] = $past_session_array;
                $data['past_player_details'] = $past_users;
            }
            return $this->sendResponse($data, 'Successfully returned Sessions data');
        } else {
            return $this->sendError("Incorrect User Type");
        }
    }
}
