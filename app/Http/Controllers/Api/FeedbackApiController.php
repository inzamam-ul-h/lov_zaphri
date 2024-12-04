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
use App\Models\Session;
use App\Models\Booking;

class FeedbackApiController extends BaseController {

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
                case 'create_feedback': {
                        $user_type = $User->user_type;
                        if ($user_type == $this->_COACH_USER) {
                            return $this->coach_session_feedback($request, $User);
                        }
                        elseif ($user_type == $this->_PLAYER_USER) {
                            return $this->player_session_feedback($request, $User);
                        }
                        else {
                            return $this->sendError("Incorrect User Type");
                        }
                    }
                    break;

                case 'view_feedback': {
                        return $this->view_feedback($request, $User);
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

    private function create_feedback(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
            if (isset($request->session_id) && ltrim(rtrim($request->session_id)) != '' && isset($request->delivery) && ltrim(rtrim($request->delivery)) != '' && isset($request->rating) && ltrim(rtrim($request->rating)) != '' && isset($request->remarks)) {
                $session_id = $request->session_id;

                $booking = array();
                if ($user_type == $this->_COACH_USER) {
                    $booking = Booking::where('session_id', $session_id)->where('user_id', $user_id)->where('status', 7)->first();
                }
                elseif ($user_type == $this->_PLAYER_USER) {
                    $booking = Booking::where('session_id', $session_id)->where('req_user_id', $user_id)->where('status', 7)->first();
                }

                if (!empty($booking)) {
                    if ($user_type == $this->_COACH_USER) {
                        return $this->coach_session_feedback($request, $User, $booking);
                    }
                    elseif ($user_type == $this->_PLAYER_USER) {
                        return $this->player_session_feedback($request, $User, $booking);
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

    private function coach_session_feedback(Request $request, $User, $booking) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $booking_id = $booking->id;

        $coach_feedback = 1;

        $coach_delivery = ltrim(rtrim($request->delivery));

        $coach_rating = ltrim(rtrim($request->rating));
        if ($coach_delivery == 0)
            $coach_rating = 0;

        if (ltrim(rtrim($request->remarks)) != '') {
            $coach_remarks = ltrim(rtrim($request->remarks));
        }
        else {
            $coach_remarks = "No Remarks Provided";
        }

        $Booking = Booking::find($booking_id);
        $Booking->coach_feedback = $coach_feedback;
        $Booking->coach_delivery = $coach_delivery;
        $Booking->coach_rating = $coach_rating;
        $Booking->coach_remarks = $coach_remarks;
        $Booking->save();

        $player_id = $booking->user_id;

        $rating = 0;
        $count = 0;
        $bookings = Booking::where('req_user_id', $player_id)->where('coach_rating', '<>', 0)->where('coach_rating', ' IS NOT ', 'NULL')->get();
        foreach ($bookings as $booking) {
            $count++;
            $rating += $booking->coach_rating;
        }

        $rating = ($rating / $count);

        $UserRating = User::find($player_id);
        $UserRating->rating = $rating;
        $UserRating->save();

        return $this->sendSuccess("Feedback Successfully Submitted");
    }

    private function player_session_feedback(Request $request, $User, $booking) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $booking_id = $booking->id;

        $player_feedback = 1;

        $player_delivery = ltrim(rtrim($request->delivery));

        $player_rating = ltrim(rtrim($request->rating));
        if ($player_delivery == 0)
            $player_rating = 0;

        if (ltrim(rtrim($request->remarks)) != '') {
            $player_remarks = ltrim(rtrim($request->remarks));
        }
        else {
            $player_remarks = "No Remarks Provided";
        }

        $Booking = Booking::find($booking_id);
        $Booking->player_feedback = $player_feedback;
        $Booking->player_delivery = $player_delivery;
        $Booking->player_rating = $player_rating;
        $Booking->player_remarks = $player_remarks;
        $Booking->save();

        $coach_id = $booking->user_id;

        $rating = 0;
        $count = 0;
        $bookings = Booking::where('user_id', $coach_id)->where('player_rating', '<>', 0)->where('player_rating', ' IS NOT ', 'NULL')->get();
        foreach ($bookings as $booking) {
            $count++;
            $rating += $booking->player_rating;
        }

        $rating = ($rating / $count);

        $UserRating = User::find($coach_id);
        $UserRating->rating = $rating;
        $UserRating->save();

        return $this->sendSuccess("Feedback Successfully Submitted");
    }

    private function view_feedback(Request $request, $User) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
            if (isset($request->session_id) && ltrim(rtrim($request->session_id)) != '') {
                $session_id = $request->session_id;

                $booking = array();
                if ($user_type == $this->_COACH_USER) {
                    $booking = Booking::where('session_id', $session_id)->where('user_id', $user_id)->where('coach_feedback', 1)->first();
                }
                elseif ($user_type == $this->_PLAYER_USER) {
                    $booking = Booking::where('session_id', $session_id)->where('req_user_id', $user_id)->where('player_feedback', 1)->first();
                }

                if (!empty($booking)) {
                    if ($user_type == $this->_COACH_USER) {
                        return $this->coach_view_feedback($request, $User, $booking);
                    }
                    elseif ($user_type == $this->_PLAYER_USER) {
                        return $this->player_view_feedback($request, $User, $booking);
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

    private function coach_view_feedback(Request $request, $User, $booking) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $array = array();

        if ($booking->coach_feedback != 0) {
            $array['coach_feedback'] = $booking->coach_feedback;
            $array['coach_delivery'] = $booking->coach_delivery;
            $array['coach_rating'] = $booking->coach_rating;
            $array['coach_remarks'] = $booking->coach_remarks;
        }
        else {
            $array['coach_feedback'] = $booking->coach_feedback;
        }

        if ($booking->player_feedback != 0) {
            $array['player_feedback'] = $booking->player_feedback;
            $array['player_delivery'] = $booking->player_delivery;
            $array['player_rating'] = $booking->player_rating;
            $array['player_remarks'] = $booking->player_remarks;
        }
        else {
            $array['player_feedback'] = $booking->player_feedback;
        }

        $response = [
            'session_feedback' => $array
        ];
        return $this->sendResponse($response, 'Successfully Returned the feedback');
    }

    private function player_view_feedback(Request $request, $User, $booking) {
        $user_id = $User->id;
        $user_type = $User->user_type;
        $array = array();

        if ($booking->player_feedback != 0) {
            $array['player_feedback'] = $booking->player_feedback;
            $array['player_delivery'] = $booking->player_delivery;
            $array['player_rating'] = $booking->player_rating;
            $array['player_remarks'] = $booking->player_remarks;
        }
        else {
            $array['player_feedback'] = $booking->player_feedback;
        }

        if ($booking->coach_feedback != 0) {
            $array['coach_feedback'] = $booking->coach_feedback;
            $array['coach_delivery'] = $booking->coach_delivery;
            $array['coach_rating'] = $booking->coach_rating;
            $array['coach_remarks'] = $booking->coach_remarks;
        }
        else {
            $array['coach_feedback'] = $booking->coach_feedback;
        }

        $response = [
            'session_feedback' => $array
        ];
        return $this->sendResponse($response, 'Successfully Returned the feedback');
    }

}
