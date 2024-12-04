<?php

namespace App\Http\Controllers\Backend;

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
use App\Models\UserProfessional;
use App\Models\SessionType;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ContactDetail;

class SessionController extends MainController {

    private $views_path = "backend.sessions";
    private $home_route = "sessions.index";
    private $create_route = "sessions.create";
    private $edit_route = "sessions.edit";
    private $view_route = "sessions.show";
    private $delete_route = "sessions.destroy";
    private $active_route = "sessions.activate";
    private $inactive_route = "sessions.deactivate";
    private $msg_created = "Session added successfully.";
    private $msg_updated = "Session updated successfully.";
    private $msg_deleted = "Session deleted successfully.";
    private $msg_not_found = "Session not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Session name";
    private $list_permission = "sessions-listing";
    private $add_permission = "sessions-add";
    private $edit_permission = "sessions-edit";
    private $view_permission = "sessions-view";
    private $status_permission = "sessions-status";
    private $delete_permission = "sessions-delete";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming() {

        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {

            $records_exists = 1;

            return view($this->views_path . '.upcomming_listing', compact("records_exists"));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function upcomingDatatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $current_time = time();
            $user_id = $Auth_User->id;
            $user_type = $Auth_User->user_type;
            $status_array = get_sessesion_status_array($user_type, 'session_upcoming');

            $Records = Session::leftjoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                    ->join('users', 'sessions.user_id', '=', 'users.id')
                    ->join('session_types', 'sessions.type', '=', 'session_types.id')
                    ->select(['sessions.*', 'users.name as user_name', 'session_types.name as session_name'])
                    ->where('sessions.time_start', '>=', $current_time);
            switch ($user_type) {
                case $this->_ADMIN_USER: {
                        //$Records = $Records->whereIN('bookings.status', $status_array);
                    }
                    break;
                case $this->_COACH_USER: {
                        $Records = $Records->where('sessions.user_id', $user_id)
                                ->whereIN('bookings.status', $status_array);
                    }
                    break;
                case $this->_PLAYER_USER: {
                        $Records = $Records->where('bookings.req_user_id', $user_id)
                                ->whereIN('bookings.status', $status_array);
                    }

                    break;
                default : {
                        //
                    }
                    break;
            }

            $response = Datatables::of($Records)
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('start_date', function ($Records) {

                        $timestamp = $Records->time_start;
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        $newTimezone = new DateTimeZone('America/New_York');
                        $dateTime->setTimezone($newTimezone);
                        $convertedDateTime = $dateTime->format('Y-m-d');
                        return $convertedDateTime;
                    })
                    ->addColumn('end_date', function ($Records) {
                        $timestamp = $Records->time_end;
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        $newTimezone = new DateTimeZone('America/New_York');
                        $dateTime->setTimezone($newTimezone);
                        $convertedDateTime = $dateTime->format('h:i:s a');
                        return $convertedDateTime;
                    })
                    ->addColumn('type', function ($Records) {
                        $str = $Records->session_name;

                        return $str;
                    })
                    ->addColumn('coach', function ($Records) {
                        $str = $Records->user_name;

                        return $str;
                    })
                    ->addColumn('player', function ($Records) {
                        $user_id = $Records->req_user_id;

                        return get_user_name($user_id);
                    })
                    ->addColumn('payment_status', function ($Records) {
                        $status = $Records->status;

                        return get_booking_status_details($status);
                    })
                    ->addColumn('price', function ($Records) {
                        $str = $Records->price;

                        return $str;
                    })
                    ->rawColumns(['sr_no', 'start_date', 'end_date', 'player', 'type', 'coach', 'payment_status', 'price'])
                    ->setRowId(function ($Records) {
                        return 'myDtRow' . $Records->id;
                    })
                    ->make(true);

            return $response;
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function history(Request $request) {

        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 1;

            return view($this->views_path . '.history_listing', compact("records_exists"));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function historyDatatable(Request $request) {
        $Auth_User = Auth::user();

        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $user_id = $Auth_User->id;
            $user_type = $Auth_User->user_type;
            $status_array = get_sessesion_status_array($user_type, 'session_all');

            $Records = Session::leftjoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                    ->join('users', 'sessions.user_id', '=', 'users.id')
                    ->join('session_types', 'sessions.type', '=', 'session_types.id')
                    ->select(['sessions.*', 'users.name as user_name', 'session_types.name as session_name']);
            switch ($user_type) {
                case $this->_ADMIN_USER: {
                        //$Records = $Records->whereNotIn('bookings.status', $status_array);
                    }
                    break;
                case $this->_COACH_USER: {
                        $Records = $Records->where('sessions.user_id', $user_id)
                                ->whereNotIn('bookings.status', $status_array);
                    }
                    break;
                case $this->_PLAYER_USER: {
                        $Records = $Records->where('bookings.req_user_id', $user_id)
                                ->whereNotIn('bookings.status', $status_array);
                    }
                    break;
                default : {
                        //
                    }
                    break;
            }
            $response = Datatables::of($Records)
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('start_date', function ($Records) {

                        $timestamp = $Records->time_start;
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        $newTimezone = new DateTimeZone('America/New_York');
                        $dateTime->setTimezone($newTimezone);
                        $convertedDateTime = $dateTime->format('Y-m-d');
                        return $convertedDateTime;
                    })
                    ->addColumn('end_date', function ($Records) {
                        $timestamp = $Records->time_end;
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        $newTimezone = new DateTimeZone('America/New_York');
                        $dateTime->setTimezone($newTimezone);
                        $convertedDateTime = $dateTime->format('h:i:s a');
                        return $convertedDateTime;
                    })
                    ->addColumn('type', function ($Records) {
                        $str = $Records->session_name;

                        return $str;
                    })
                    ->addColumn('coach', function ($Records) {
                        $str = $Records->user_name;

                        return $str;
                    })
                    ->addColumn('player', function ($Records) {
                        $user_id = $Records->req_user_id;

                        return get_user_name($user_id);
                    })
                    ->addColumn('payment_status', function ($Records) {
                        $status = $Records->status;

                        return get_booking_status_details($status);
                    })
                    ->addColumn('price', function ($Records) {
                        $str = $Records->price;

                        return $str;
                    })
                    ->rawColumns(['sr_no', 'start_date', 'end_date', 'type', 'coach', 'player', 'payment_status', 'price'])
                    ->setRowId(function ($Records) {
                        return 'myDtRow' . $Records->id;
                    })
                    ->make(true);

            return $response;
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

}
