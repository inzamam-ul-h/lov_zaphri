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
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Video;
use App\Models\Event;
use App\Models\TrainingPlan;
use App\Models\TrainingPlanDetail;
use App\Models\TrainingProgram;
use App\Models\TrainingProgramDetail;
use App\Models\ContactDetail;

class HomeController extends MainController {

    private $views_path = "backend.dashboard";
    private $list_permission = "sessions-listing";
    private $add_permission = "sessions-add";
    private $edit_permission = "sessions-edit";
    private $view_permission = "sessions-view";
    private $status_permission = "sessions-status";
    private $delete_permission = "sessions-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Users. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add User. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update User. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View User details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of User. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete User. Please Contact Administrator.";

    public function index(Request $request) {
        $this->middleware('auth');
        if (isset($request->verified)) {
            Flash::success(translate_it('Email address has been successfully verified.'));
        }

        $Settings = ContactDetail::find(1);

        switch (Auth::User()->user_type) {
            case 1:return $this->coach_dashboard($request, $Settings);
            case 2:return $this->player_dashboard($request, $Settings);
            case 3:return $this->club_dashboard($request, $Settings);
            case 4:return $this->parent_dashboard($request, $Settings);
            default:return $this->admin_dashboard($request, $Settings);
        }
    }

    public function admin_dashboard(Request $request, $Settings) {
        $sessions_total = 0;
        $sessions_expired = 0;
        $sessions_canceled = 0;
        $sessions_delivered = 0;
        $sessions_upcoming = 0;
        $sessions_today = 0;
        // Get the current time and calculate session boundaries
        $current_time = time();
        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);

        $sessions = Session::select('id', 'time_start', 'time_end', 'booked')->get();
        foreach ($sessions as $session) {
            $sessions_total++;

            $session_id = $session->id;
            $time_start = $session->time_start;
            $time_end = $session->time_end;
            $booked = $session->booked;
            $expired = 0;

            if ($time_end <= $current_time) {
                if ($booked == 0) {
                    $sessions_expired++;
                }
                else {
                    $expired = 1;
                    $sessions_delivered++;
                }
            }
            elseif ($time_start >= $current_time) {
                $sessions_upcoming++;
            }

            // Check if the session is today
            if ($time_start >= $session_start && $time_start <= $session_end) {
                $sessions_today++;
            }

            if ($booked == 1) {
                $bookings = Booking::select('id', 'status')->where('session_id', $session_id)->get();
                foreach ($bookings as $booking) {
                    $booking_id = $booking->id;
                    $status = $booking->status;

                    if ($expired == 1) {
                        if ($status == 1) {
                            update_booking(10, $booking_id);
                        }
                        elseif ($status == 2) {
                            update_booking(7, $booking_id);
                        }
                    }

                    if ($time_start >= $session_start && $time_end <= $session_end) {
                        if ($status == 3 || $status == 4 || $status == 5 || $status == 6 || $status == 7) {
                            $sessions_upcoming--;
                        }
                        if ($status == 2) {
                            $sessions_today++;
                        }
                    }
                    elseif ($status == 3 || $status == 4 || $status == 5 || $status == 6) {
                        $sessions_canceled++;
                    }
                }
            }
        }

        return view($this->views_path . '.admin', compact(
                        'sessions_total',
                        'sessions_expired',
                        'sessions_canceled',
                        'sessions_delivered',
                        'sessions_upcoming',
                        'sessions_today',
                        'Settings'
        ));
    }

    public function admin_all_sessions_datatable(Request $request) {


        $Records = Session::join('session_types', 'sessions.type', '=', 'session_types.id')
                ->leftJoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                ->select(['sessions.*', 'bookings.req_user_id', 'bookings.status as booking_status', 'session_types.name as session_name']);

        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('session_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'date');
                    return $date;
                })
                ->addColumn('start_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start);
                    return $date;
                })
                ->addColumn('end_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_end);
                    return $date;
                })
                ->addColumn('type', function ($Records) {
                    $str = $Records->session_name;
                    return $str;
                })
                ->addColumn('coach_name', function ($Records) {
                    $str = get_user_name($Records->user_id);
                    return $str;
                })
                ->addColumn('player_name', function ($Records) {
                    $str = get_user_name($Records->req_user_id);
                    return $str;
                })
                ->addColumn('payment_status', function ($Records) {
                    $status = $Records->booking_status; //booking_status;
                    return get_booking_status_details($status);
                })
                ->addColumn('price', function ($Records) {
                    $str = $Records->price;
                    return $str;
                })
                /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                  {
                  $str.= delete_link_in_table($record_id);
                  } */
                ->rawColumns(['sr_no', 'start_date', 'session_date', 'type', 'coach_name', 'player_name', 'payment_status', 'price'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function coach_dashboard(Request $request, $Settings) {
        $sessions_total = 0;
        $sessions_expired = 0;
        $sessions_canceled = 0;
        $sessions_delivered = 0;
        $sessions_upcoming = 0;
        $sessions_today = 0;
        // Get the current time and calculate session boundaries
        $current_time = time();
        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);

        $sessions = Session::select('id', 'time_start', 'time_end', 'booked')->where('user_id', '=', Auth::User()->id)->get();
        foreach ($sessions as $session) {
            $sessions_total++;
            $session_id = $session->id;
            $time_start = $session->time_start;
            $time_end = $session->time_end;
            $booked = $session->booked;
            $expired = 0;

            if ($time_end <= $current_time) {

                if ($booked == 0) {

                    $sessions_expired++;
                }
                else {

                    $expired = 1;
                    $sessions_delivered++;
                }
            }
            elseif ($time_start >= $current_time) {

                $sessions_upcoming++;
            }

            // Check if the session is today
            if ($time_start >= $session_start && $time_start <= $session_end) {

                $sessions_today++;
            }

            if ($booked == 1) {
                $bookings = Booking::select('id', 'status')->where('session_id', $session_id)->get();
                foreach ($bookings as $booking) {
                    $booking_id = $booking->id;
                    $status = $booking->status;
                    if ($expired == 1) {
                        if ($status == 1) {
                            update_booking(10, $booking_id);
                        }
                        elseif ($status == 2) {
                            update_booking(7, $booking_id);
                        }
                    }
                    if ($time_end < $current_time && ($status == 2 || $status == 8)) {
                        $sessions_delivered++;
                        $expired = 1;
                    }

                    if ($time_start >= $session_start && $time_end <= $session_end) {
                        if ($status == 3 || $status == 4 || $status == 5 || $status == 6 || $status == 7) {
                            //
                        }
                        if ($status == 2) {
                            $sessions_today++;
                        }
                    }
                    if ($status == 3 || $status == 4 || $status == 5 || $status == 6) {
                        $sessions_canceled++;
                    }
                }
            }
        }

        return view($this->views_path . '.coach', compact(
                        'sessions_total',
                        'sessions_expired',
                        'sessions_canceled',
                        'sessions_delivered',
                        'sessions_upcoming',
                        'sessions_today',
                        'Settings'
        ));
    }

    public function coach_upc_sessions_datatable(Request $request) {
        $Auth_User = Auth::user();
        $user_id = $Auth_User->id;
        $user_type = $Auth_User->user_type;
        $status_array = get_sessesion_status_array($user_type, 'session_upcoming');
        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);

        $Records = Session::join('session_types', 'sessions.type', '=', 'session_types.id')
                ->leftJoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                ->where('sessions.user_id', $user_id)
                ->whereIN('bookings.status', $status_array)
                ->where('sessions.time_start', '>=', $session_start)
                ->where('sessions.time_end', '<=', $session_end)
                ->select(['sessions.*', 'bookings.req_user_id', 'bookings.status as booking_status', 'session_types.name as session_name'])
                ->orderBy('time_start', 'asc');

        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('session_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'date');
                    return $date;
                })
                ->addColumn('start_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'time');
                    return $date;
                })
                ->addColumn('end_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_end, 'time');
                    return $date;
                })
                ->addColumn('type', function ($Records) {
                    $str = $Records->session_name;
                    return $str;
                })
                // ->addColumn('coach_name', function ($Records) {
                // 	$str = get_user_name($Records->user_id);
                // 	return $str;
                // })
                ->addColumn('player_name', function ($Records) {
                    $str = get_user_name($Records->req_user_id);
                    return $str;
                })
                ->addColumn('payment_status', function ($Records) {
                    $status = $Records->booking_status; //booking_status;
                    return get_booking_status_details($status);
                })
                ->addColumn('price', function ($Records) {
                    $str = $Records->price;
                    return $str;
                })

                /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                  {
                  $str.= delete_link_in_table($record_id);
                  } */
                ->rawColumns(['sr_no', 'start_date', 'session_date', 'type', 'player_name', 'payment_status', 'price'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function coach_all_sessions_datatable(Request $request) {
        $Auth_User = Auth::user();
        $user_id = $Auth_User->id;
        $user_type = $Auth_User->user_type;
        $status_array = get_sessesion_status_array($user_type, 'session_all');
        $Records = Session::join('session_types', 'sessions.type', '=', 'session_types.id')
                ->leftJoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                ->where('sessions.user_id', $user_id)
                ->whereNotIn('bookings.status', $status_array)
                ->select(['sessions.*', 'bookings.req_user_id', 'bookings.status as booking_status', 'session_types.name as session_name']);

        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('session_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'date');
                    return $date;
                })
                ->addColumn('start_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'time');
                    return $date;
                })
                ->addColumn('end_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_end);
                    return $date;
                })
                ->addColumn('type', function ($Records) {
                    $str = $Records->session_name;
                    return $str;
                })
                ->addColumn('coach_name', function ($Records) {
                    $str = get_user_name($Records->user_id);
                    return $str;
                })
                ->addColumn('player_name', function ($Records) {
                    $str = get_user_name($Records->req_user_id);
                    return $str;
                })
                ->addColumn('payment_status', function ($Records) {
                    $status = $Records->booking_status; //booking_status;
                    return get_booking_status_details($status);
                })
                ->addColumn('price', function ($Records) {
                    $str = $Records->price;
                    return $str;
                })
                /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                  {
                  $str.= delete_link_in_table($record_id);
                  } */
                ->rawColumns(['sr_no', 'start_date', 'session_date', 'type', 'coach_name', 'player_name', 'payment_status', 'price'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function club_dashboard(Request $request, $Settings) {
        $club = Auth::user();
        $userId = $club->id;
        $total_videos = User::join('videos', 'videos.user_id', '=', 'users.id')
                ->where('users.id', $userId)
                ->select('videos.id')
                ->count();
        $total_coaches = count($this->get_club_coach_ids($club));

        $total_players = count($this->get_club_player_ids($club));
        $total_associations = ($total_players + $total_coaches);

        $coachVideosCount = User::join('videos', 'videos.user_id', '=', 'users.id')
                ->where('users.id', $userId)
                ->where('videos.recipients', 1)
                ->select('videos.id') // Count videos for coaches
                ->count();

        $playerVideosCount = User::join('videos', 'videos.user_id', '=', 'users.id')
                ->where('users.id', $userId)
                ->where('videos.recipients', 2)
                ->select('videos.id') // Count videos for players
                ->count();

        return view($this->views_path . '.club', compact(
                        'total_associations',
                        'total_players',
                        'total_coaches',
                        'total_videos',
                        'playerVideosCount',
                        'coachVideosCount',
                        'Settings'
        ));
    }

    public function club_all_requests_databale(Request $request) {
        $Auth_User = Auth::user();
        $user_id = $Auth_User->id;

        $Records = User::join('user_professionals', 'user_professionals.user_id', '=', 'users.id')
                ->Where('user_professionals.club', $user_id)
                ->Where('user_professionals.club_authentication', 0)
                ->select('users.*', 'user_professionals.id as request_id', 'user_professionals.club_authentication');

        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('user_type', function ($Records) {
                    $user_type = $Records->user_type;

                    $str = "";
                    if ($user_type == 0) {
                        $str = 'Admin';
                    }
                    elseif ($user_type == 1) {
                        $str = 'Coach';
                    }
                    elseif ($user_type == 2) {
                        $str = 'Player';
                    }
                    elseif ($user_type == 3) {
                        $str = 'Club';
                    }
                    elseif ($user_type == 4) {
                        $str = 'Parent';
                    }
                    return $str;
                })
                ->addColumn('action', function ($Records) {
                    $record_id = $Records->request_id;
                    $Auth_User = Auth::user();
                    $club_authentication = $Records->club_authentication;

                    $str = '<div>';
                    if ($club_authentication == 0) {
                        $str .= approve_link_in_table('clubs.request_approve', $record_id) . '  ';
                        $str .= reject_link_in_table('clubs.request_reject', $record_id) . '  ';
                    }
                    $str .= '</div>';
                    return $str;
                })
                ->rawColumns(['sr_no', 'user_type', 'action'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function player_dashboard(Request $request, $Settings) {
        $sessions_total = 0;
        $sessions_expired = 0;
        $sessions_canceled = 0;
        $sessions_delivered = 0;
        $sessions_upcoming = 0;
        $sessions_today = 0;
        // Get the current time and calculate session boundaries
        $current_time = time();
        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);

        $bookings = Booking::select('id', 'session_id', 'status')->where('req_user_id', Auth::User()->id)->get();
        foreach ($bookings as $booking) {
            $sessions_total++;
            $booking_id = $booking->id;
            $session_id = $booking->session_id;
            $status = $booking->status;
            $sessions = Session::select('time_start', 'time_end')->where('id', $session_id)->get();

            foreach ($sessions as $session) {
                $time_start = $session->time_start;
                $time_end = $session->time_end;
                $expired = 0;

                if ($time_start >= $session_start && $time_end <= $session_end) {
                    if ($status == 2 || $status == 7) {
                        $sessions_today++;
                    }
                }
                if ($status == 3 || $status == 4 || $status == 5 || $status == 6) {
                    $sessions_canceled++;
                }

                if ($time_end < $current_time && ($status == 2 || $status == 8)) {
                    $sessions_delivered++;
                    $expired = 1;
                }
                elseif ($time_end < $current_time && ($status == 1 || $status == 10 )) {
                    $sessions_expired++;
                }

                if ($time_start > $current_time && ($status == 1 || $status == 2)) {
                    $sessions_upcoming++;
                }

                if ($expired == 1) {
                    if ($status == 1) {
                        update_booking(10, $booking_id);
                    }
                    elseif ($status == 2) {
                        update_booking(7, $booking_id);
                    }
                }
            }
        }

        return view($this->views_path . '.player', compact(
                        'sessions_total',
                        'sessions_expired',
                        'sessions_canceled',
                        'sessions_delivered',
                        'sessions_upcoming',
                        'sessions_today',
                        'Settings'
        ));
    }

    public function player_upc_sessions_datatable(Request $request) {
        $Auth_User = Auth::user();
        $user_id = $Auth_User->id;
        $user_type = $Auth_User->user_type;
        $status_array = get_sessesion_status_array($user_type, 'session_upcoming');

        $session_start = strtotime(date('d-m-Y'));
        $_time_24 = (24 * 60 * 60);
        $session_end = ($session_start + $_time_24);
        $Records = Session::join('session_types', 'sessions.type', '=', 'session_types.id')
                ->leftJoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                ->where('bookings.req_user_id', $user_id)
                ->whereIN('bookings.status', $status_array)
                ->where('sessions.time_start', '>=', $session_start)
                ->where('sessions.time_end', '<=', $session_end)
                ->select(['sessions.*', 'bookings.req_user_id', 'bookings.id as booking_id', 'bookings.status as booking_status', 'session_types.name as session_name'])
                ->orderBy('time_start', 'asc');

        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('session_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'date');
                    return $date;
                })
                ->addColumn('start_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'time');
                    return $date;
                })
                ->addColumn('end_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_end, 'time');
                    return $date;
                })
                ->addColumn('type', function ($Records) {
                    $str = $Records->session_name;
                    return $str;
                })
                ->addColumn('coach_name', function ($Records) {
                    $str = get_user_name($Records->user_id);
                    return $str;
                })
                // ->addColumn('player_name', function ($Records) {
                // 	$str = get_user_name($Records->req_user_id);
                // 	return $str;
                // })
                ->addColumn('payment_status', function ($Records) {
                    $status = $Records->booking_status; //booking_status;
                    return get_booking_status_details($status);
                })
                ->addColumn('price', function ($Records) {
                    $str = $Records->price;
                    return $str;
                })

                /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                  {
                  $str.= delete_link_in_table($record_id);
                  } */
                ->rawColumns(['sr_no', 'start_date', 'session_date', 'type', 'coach_name', 'payment_status', 'price'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function player_all_sessions_datatable(Request $request) {
        $Auth_User = Auth::user();
        $user_id = $Auth_User->id;
        $user_type = $Auth_User->user_type;
        $status_array = get_sessesion_status_array($user_type, 'session_all');
        $Records = Session::join('session_types', 'sessions.type', '=', 'session_types.id')
                ->leftJoin('bookings', 'sessions.id', '=', 'bookings.session_id')
                ->where('bookings.req_user_id', $user_id)
                ->whereNotIn('bookings.status', $status_array)
                ->select(['sessions.*', 'bookings.req_user_id', 'bookings.status as booking_status', 'session_types.name as session_name'])
                ->orderBy('time_start', 'asc');
        $response = Datatables::of($Records)
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('start_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start);
                    return $date;
                })
                ->addColumn('session_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_start, 'date');
                    return $date;
                })
                ->addColumn('end_date', function ($Records) {
                    $date = dispaly_carbon_date_in_table($Records->time_end);
                    return $date;
                })
                ->addColumn('type', function ($Records) {
                    $str = $Records->session_name;
                    return $str;
                })
                ->addColumn('coach_name', function ($Records) {
                    $str = get_user_name($Records->user_id);
                    return $str;
                })
                // ->addColumn('player_name', function ($Records) {
                // 	$str = get_user_name($Records->req_user_id);
                // 	return $str;
                // })
                ->addColumn('payment_status', function ($Records) {
                    $status = $Records->booking_status; //booking_status;
                    return get_booking_status_details($status);
                })
                ->addColumn('price', function ($Records) {
                    $str = $Records->price;
                    return $str;
                })
                /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                  {
                  $str.= delete_link_in_table($record_id);
                  } */
                ->rawColumns(['sr_no', 'start_date', 'session_date', 'type', 'coach_name', 'payment_status', 'price'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function parent_dashboard(Request $request, $Settings) {
        $Auth_User = Auth::user();
        $sessions_total = 0;
        $sessions_expired = 0;
        $sessions_canceled = 0;
        $sessions_delivered = 0;
        $sessions_upcoming = 0;
        $sessions_today = 0;
        $parent_user_ids = $this->get_parent_user_ids($Auth_User);
        if (count($parent_user_ids) > 0) {
            // Get the current time and calculate session boundaries
            $current_time = time();
            $session_start = strtotime(date('d-m-Y'));
            $_time_24 = (24 * 60 * 60);
            $session_end = ($session_start + $_time_24);

            $sessions = Session::join('bookings', 'sessions.id', '=', 'bookings.session_id')
                            ->whereIN('bookings.req_user_id', $parent_user_ids)
                            ->select(
                                    'sessions.id',
                                    'sessions.type',
                                    'sessions.price',
                                    'sessions.color',
                                    'sessions.time_start',
                                    'sessions.time_end',
                                    'sessions.booked',
                                    'bookings.id as booking_id',
                                    'bookings.session_id',
                                    'bookings.req_user_id',
                                    'bookings.status', 'sessions.status as session_status', 'bookings.status as booking_status'
                            )->get();

            foreach ($sessions as $session) {
                $sessions_total++;

                $session_id = $session->id;
                $time_start = strtotime($session->time_start);
                $time_end = strtotime($session->time_end);
                $booked = $session->booked;
                $expired = 0;

                if ($time_end <= $current_time) {
                    if ($booked == 0) {
                        $sessions_expired++;
                    }
                    else {
                        $expired = 1;
                        $sessions_delivered++;
                    }
                }
                elseif ($time_start >= $current_time) {
                    $sessions_upcoming++;
                }

                // Check if the session is today
                if ($time_start >= $session_start && $time_start <= $session_end) {
                    $sessions_today++;
                }
            }
        }

        return view($this->views_path . '.parent', compact(
                        'sessions_total',
                        'sessions_expired',
                        'sessions_canceled',
                        'sessions_delivered',
                        'sessions_upcoming',
                        'sessions_today',
                        'Settings'
        ));
    }

}
