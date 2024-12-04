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

class PaymentController extends MainController {

    protected $uploads_root = "uploads";
    private $uploads_path = "uploads/payments/";
    private $views_path = "backend.payments";
    private $home_route = "payments.index";
    private $create_route = "payments.create";
    private $edit_route = "payments.edit";
    private $view_route = "payments.show";
    private $delete_route = "payments.destroy";
    private $active_route = "payments.activate";
    private $inactive_route = "payments.deactivate";
    private $msg_created = "Payment added successfully.";
    private $msg_updated = "Payment updated successfully.";
    private $msg_deleted = "Payment deleted successfully.";
    private $msg_not_found = "Payment not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Payment name";
    private $list_permission = "payments-listing";
    private $add_permission = "payments-add";
    private $edit_permission = "payments-edit";
    private $view_permission = "payments-view";
    private $status_permission = "payments-status";
    private $delete_permission = "payments-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of payment. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add payment. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update payment. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View product details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of payment. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete payment. Please Contact Administrator.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
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

    public function historydatatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $status_array = [2, 7, 8, 9];
            $Records = Payment::Join('payment_details', 'payment_details.payment_id', '=', 'payments.id')
                    ->join('bookings', 'bookings.id', '=', 'payment_details.booking_id')
                    ->join('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->join('session_types', 'sessions.type', '=', 'session_types.id')
                    ->select(['payments.*', 'session_types.name as type', 'bookings.req_user_id as player_name', 'payment_details.ref_date', 'payment_details.ref_req_date', 'bookings.status as booking_status'])
                    ->whereIN('bookings.status', $status_array);

            $response = Datatables::of($Records)

                    // ->filter(function ($query) use ($request) {
                    // 	if ($request->has('name') && !empty($request->name)) {
                    // 		$query->where('cities.name', 'like', "%{$request->get('name')}%");
                    // 	}
                    // 	if ($request->has('is_featured') && $request->get('is_featured') != -1) {
                    // 		$query->where('cities.is_featured', '=', "{$request->get('is_featured')}");
                    // 	}
                    // 	if ($request->has('status') && $request->get('status') != -1) {
                    // 		$query->where('cities.status', '=', "{$request->get('status')}");
                    // 	}
                    // 	if ($request->has('pr_name') && !empty($request->pr_name)) {
                    // 		$query->where('states.name', 'like', "%{$request->get('pr_name')}%");
                    // 	}
                    // })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('datetime', function ($Records) {
                        $timestamp = $Records->pay_date;
                        // Create a Carbon instance from the timestamp
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        // Define the target timezone for New York (America/New_York)
                        $newTimezone = new DateTimeZone('America/New_York');
                        // Set the target timezone for the Carbon instance
                        $dateTime->setTimezone($newTimezone);
                        // Format the converted date and time as a string with AM/PM indicator
                        $convertedDateTime = $dateTime->format('Y-m-d');
                        return $convertedDateTime;
                    })
                    ->addColumn('payment_status', function ($Records) {
                        $status = $Records->booking_status;

                        return get_booking_status_details($status);
                    })
                    ->addColumn('player', function ($Records) {
                        $player_name = $Records->player_name;
                        return get_user_name($player_name);
                    })
                    ->addColumn('type', function ($Records) {
                        $str = $Records->type;

                        return $str;
                    })
                    ->addColumn('payment_date', function ($Records) {
                        $timestamp = $Records->pay_date;
                        // Create a Carbon instance from the timestamp
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        // Define the target timezone for New York (America/New_York)
                        $newTimezone = new DateTimeZone('America/New_York');
                        // Set the target timezone for the Carbon instance
                        $dateTime->setTimezone($newTimezone);
                        // Format the converted date and time as a string with AM/PM indicator
                        $convertedDateTime = $dateTime->format('Y-m-d');
                        return $convertedDateTime;
                    })
                    ->addColumn('price', function ($Records) {
                        $str = $Records->paid_amount;

                        return $str;
                    })
                    ->addColumn('transaction_id', function ($Records) {
                        $str = $Records->transaction_id;

                        return $str;
                    })
                    ->addColumn('action', function ($Records) {
                        $record_id = $Records->id;
                        $Auth_User = Auth::user();
                        $status = $Records->status;

                        $str = '<button data-bs-toggle="dropdown" class="btn btn-info btn-sm">
							<i class="fa fa-ellipsis-v"></i>
						</button>
						<div class="dropdown-menu">';

                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $record_id);
                        }

                        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
                            $str .= edit_link_in_table($this->edit_route, $record_id);
                        }

                        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {


                            if ($status == 1) {
                                $str .= inactive_link_in_table($this->inactive_route, $record_id);
                            }
                            else {
                                $str .= active_link_in_table($this->active_route, $record_id);
                            }
                        }

                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_link_in_table($record_id);
                          } */


                        $str .= '</div>';
                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->name);
                          } */
                        return $str;
                    })
                    ->rawColumns(['sr_no', 'is_featured', 'type', 'status', 'action', 'payment_status', 'transaction_id', 'payment_date', 'player', 'datetime'])
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

    public function pendingindex() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 1;

            return view($this->views_path . '.pending_listing', compact("records_exists"));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function pendingdatatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $status_array = [1];
            $Records = Payment::Join('payment_details', 'payment_details.payment_id', '=', 'payments.id')
                    ->join('bookings', 'bookings.id', '=', 'payment_details.booking_id')
                    ->join('sessions', 'sessions.id', '=', 'bookings.session_id')
                    ->join('session_types', 'sessions.type', '=', 'session_types.id')
                    ->select(['payments.*', 'session_types.name as type', 'bookings.req_user_id as player_name', 'payment_details.ref_date', 'payment_details.ref_req_date', 'bookings.status as booking_status'])
                    ->where('sessions.time_start', '>', strtotime('now'))
                    ->whereIN('bookings.status', $status_array);
            $response = Datatables::of($Records)

                    // ->filter(function ($query) use ($request) {
                    // 	if ($request->has('name') && !empty($request->name)) {
                    // 		$query->where('cities.name', 'like', "%{$request->get('name')}%");
                    // 	}
                    // 	if ($request->has('is_featured') && $request->get('is_featured') != -1) {
                    // 		$query->where('cities.is_featured', '=', "{$request->get('is_featured')}");
                    // 	}
                    // 	if ($request->has('status') && $request->get('status') != -1) {
                    // 		$query->where('cities.status', '=', "{$request->get('status')}");
                    // 	}
                    // 	if ($request->has('pr_name') && !empty($request->pr_name)) {
                    // 		$query->where('states.name', 'like', "%{$request->get('pr_name')}%");
                    // 	}
                    // })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('datetime', function ($Records) {
                        $timestamp = $Records->pay_date;
                        // Create a Carbon instance from the timestamp
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        // Define the target timezone for New York (America/New_York)
                        $newTimezone = new DateTimeZone('America/New_York');
                        // Set the target timezone for the Carbon instance
                        $dateTime->setTimezone($newTimezone);
                        // Format the converted date and time as a string with AM/PM indicator
                        $convertedDateTime = $dateTime->format('Y-m-d');
                        return $convertedDateTime;
                    })
                    ->addColumn('payment_status', function ($Records) {
                        $status = $Records->booking_status;

                        return get_booking_status_details($status);
                    })
                    ->addColumn('player', function ($Records) {
                        $player_name = $Records->player_name;
                        return get_user_name($player_name);
                    })
                    ->addColumn('type', function ($Records) {
                        $str = $Records->type;

                        return $str;
                    })
                    ->addColumn('payment_date', function ($Records) {
                        $timestamp = $Records->pay_date;
                        // Create a Carbon instance from the timestamp
                        $dateTime = Carbon::createFromTimestamp($timestamp);
                        // Define the target timezone for New York (America/New_York)
                        $newTimezone = new DateTimeZone('America/New_York');
                        // Set the target timezone for the Carbon instance
                        $dateTime->setTimezone($newTimezone);
                        // Format the converted date and time as a string with AM/PM indicator
                        $convertedDateTime = $dateTime->format('Y-m-d');
                        return $convertedDateTime;
                    })
                    ->addColumn('price', function ($Records) {
                        $str = $Records->paid_amount;

                        return $str;
                    })
                    ->addColumn('transaction_id', function ($Records) {
                        $str = $Records->transaction_id;

                        return $str;
                    })
                    ->addColumn('action', function ($Records) {
                        $record_id = $Records->id;
                        $Auth_User = Auth::user();
                        $status = $Records->status;

                        $str = '<button data-bs-toggle="dropdown" class="btn btn-info btn-sm">
							<i class="fa fa-ellipsis-v"></i>
						</button>
						<div class="dropdown-menu">';

                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $record_id);
                        }

                        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
                            $str .= edit_link_in_table($this->edit_route, $record_id);
                        }

                        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {


                            if ($status == 1) {
                                $str .= inactive_link_in_table($this->inactive_route, $record_id);
                            }
                            else {
                                $str .= active_link_in_table($this->active_route, $record_id);
                            }
                        }

                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_link_in_table($record_id);
                          } */


                        $str .= '</div>';
                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->name);
                          } */
                        return $str;
                    })
                    ->rawColumns(['sr_no', 'is_featured', 'type', 'status', 'action', 'payment_status', 'transaction_id', 'payment_date', 'player', 'datetime'])
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {

            return view($this->views_path . '.create');
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            $request->validate([
                'state_id' => 'required',
                'name'     => 'required'
            ]);

            $Model_Data = new Payment();

            $Model_Data->state_id = $request->state_id;

            $Model_Data->name = $request->name;

            $Model_Data->created_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_created);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = Payment::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }


            return view($this->views_path . '.show', compact("Model_Data"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Payment::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            return view($this->views_path . '.edit', compact("Model_Data"));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Payment::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $request->validate([
                'state_id' => 'required',
                'name'     => 'required'
            ]);

            $Model_Data->state_id = $request->state_id;

            $Model_Data->name = $request->name;

            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * update status of the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function makeActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = Payment::find($id);

            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 1;
            $Model_Data->save();

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * update status of the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function makeInActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = Payment::find($id);

            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 0;
            $Model_Data->save();

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
