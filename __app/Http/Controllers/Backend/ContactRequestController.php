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
use App\Models\ContactRequest;

class ContactRequestController extends MainController {

    private $views_path = "backend.contact_requests";
    private $list_permission = "contact_request-listing"; //"subscribers-listing";
    private $list_permission_error_message = "Error: You are not authorized to View Contact Request Listings . Please Contact Administrator.";

    public function index() {
        $Auth_User = Auth::user();
        $user_type = $Auth_User->user_type;
        if (($Auth_User->can($this->list_permission) || $Auth_User->can('all')) && $user_type == 0) {
            $records_exists = 0;
            $records = ContactRequest::select(['id'])->where('id', '>=', 1)->get();

            foreach ($records as $record) {
                $records_exists = 1;
            }

            return view($this->views_path . '/listing', compact("records_exists"));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function datatable(Request $request) {
        $Auth_User = Auth::user();
        $user_type = $Auth_User->user_type;
        if (($Auth_User->can($this->list_permission) || $Auth_User->can('all')) && $user_type == 0) {
            return $this->admin_datatable($request);
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function admin_datatable(Request $request) {
        $Records = ContactRequest::select(['id', 'name', 'email', 'contact', 'created_at']);
        $response = Datatables::of($Records)
                ->filter(function ($query) use ($request) {
                    if ($request->has('name') && !empty($request->name)) {
                        $query->where('contact_requests.name', 'like', "%{$request->get('name')}%");
                    }

                    if ($request->has('email') && !empty($request->email)) {
                        $query->where('contact_requests.email', 'like', "%{$request->get('email')}%");
                    }

                    if ($request->has('phone') && !empty($request->phone)) {
                        $query->where('contact_requests.contact', 'like', "%{$request->get('phone')}%");
                    }

                    if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                        $query->whereDate('contact_requests.created_at', '=', "{$request->get('created_at')}");
                    }

                    if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                        $query->whereDate('contact_requests.updated_at', '=', "{$request->get('updated_at')}");
                    }
                })
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('created_at', function ($Records) {
                    $str = dispaly_date_in_table($Records->created_at);
                    return $str;
                })
                ->rawColumns(['sr_no', 'created_at'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

}
