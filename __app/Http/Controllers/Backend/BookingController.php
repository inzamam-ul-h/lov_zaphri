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

class BookingController extends MainController {

    private $views_path = "backend.bookings";
    private $home_route = "bookings.index";
    private $create_route = "bookings.create";
    private $edit_route = "bookings.edit";
    private $view_route = "bookings.show";
    private $delete_route = "bookings.destroy";
    private $active_route = "bookings.activate";
    private $inactive_route = "bookings.deactivate";
    private $addFeature_route = "bookings.addFeatured";
    private $removeFeature_route = "bookings.removeFeatured";
    private $make_default_route = "bookings.make_default";
    private $msg_created = "Booking created successfully.";
    private $msg_updated = "Booking updated successfully.";
    private $msg_deleted = "Booking deleted successfully.";
    private $msg_not_found = "Booking not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same name";
    private $msg_active = "Booking made Active successfully.";
    private $msg_inactive = "Booking made InActive successfully.";
    private $msg_addFeature = "Booking added to Featured Listing successfully.";
    private $msg_removeFeature = "Booking removed from Featured Listing successfully.";
    private $msg_default = "Booking made Default successfully.";
    private $msg_cant_inactive = " Default Booking can not be inactive.";
    private $msg_cant_default = " This Booking can not be set as default.";
    private $list_permission = "bookings-listing";
    private $add_permission = "categories-add";
    private $edit_permission = "categories-edit";
    private $view_permission = "categories-view";
    private $status_permission = "categories-status";
    private $delete_permission = "categories-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Bookings. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Booking. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Booking. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Booking details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Booking. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Booking. Please Contact Administrator.";

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

    public function datatable() {
        $Auth_User = Auth::user();

        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $user_id = $Auth_User->id;
            $user_type = $Auth_User->user_type;
            $status_array = get_sessesion_status_array($user_type, 'dashboard_upcoming');
            $current_time = time();

            $Records = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->join('session_types', 'sessions.type', '=', 'session_types.id')
                    ->join('users', 'sessions.user_id', '=', 'users.id')
                    ->where('sessions.time_start', '>=', $current_time)
                    ->select(['bookings.*', 'sessions.*', 'users.name as user_name', 'session_types.name as session_name', 'bookings.status as booking_status']);

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
                    ->addColumn('booked', function ($Records) {
                        $user_id = $Records->user_id;

                        return get_user_name($user_id);
                    })
                    ->addColumn('player', function ($Records) {
                        $user_id = $Records->req_user_id;

                        return get_user_name($user_id);
                    })
                    ->addColumn('payment_status', function ($Records) {
                        $status = $Records->booking_status;

                        return get_booking_status_details($status);
                    })
                    ->addColumn('price', function ($Records) {
                        $str = $Records->price;

                        return $str;
                    })
                    ->rawColumns(['sr_no', 'start_date', 'player', 'end_date', 'type', 'coach', 'booked', 'payment_status', 'price'])
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

    public function history() {
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

    public function historyDatatable() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $user_id = $Auth_User->id;
            $user_type = $Auth_User->user_type;
            $status_array = get_sessesion_status_array($user_type, 'dashboard_all');

            $Records = Booking::leftjoin('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->join('session_types', 'sessions.type', '=', 'session_types.id')
                    ->join('users', 'sessions.user_id', '=', 'users.id')
                    ->select(['bookings.*', 'sessions.*', 'users.name as user_name', 'session_types.name as session_name', 'bookings.status as booking_status']);

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
                    ->addColumn('booked', function ($Records) {
                        $user_id = $Records->user_id;

                        return get_user_name($user_id);
                    })
                    ->addColumn('player', function ($Records) {
                        $user_id = $Records->req_user_id;

                        return get_user_name($user_id);
                    })
                    ->addColumn('payment_status', function ($Records) {
                        $status = $Records->booking_status;

                        return get_booking_status_details($status);
                    })
                    ->addColumn('price', function ($Records) {
                        $str = $Records->price;

                        return $str;
                    })
                    ->rawColumns(['sr_no', 'start_date', 'player', 'end_date', 'type', 'coach', 'booked', 'payment_status', 'price'])
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
