<?php

namespace App\Http\Controllers\Backend\Setups;

use App\Http\Controllers\MainController as MainController;
use Auth;
use File;
use Flash;
use Response;
use Attribute;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Sample;

class SetupController extends MainController {

    private $views_path = "backend.setups";
    private $home_route = "setups.index";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Setups. Please Contact Administrator.";

    /**
     * Display a listing of the Model.
     *
     * 
     * @return Response
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can('countries-listing') || $Auth_User->can('positions-listing') || $Auth_User->can('timezones-listing') || $Auth_User->can('all')) {
            return view($this->views_path . '.listing');
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function datatable(Request $request) {
        return redirect(route($this->home_route));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return redirect(route($this->home_route));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return redirect(route($this->home_route));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return redirect(route($this->home_route));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        return redirect(route($this->home_route));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        return redirect(route($this->home_route));
    }

    /**
     * update status of the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function makeActive($id) {
        return redirect(route($this->home_route));
    }

    /**
     * update status of the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function makeInActive($id) {
        return redirect(route($this->home_route));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        return redirect(route($this->home_route));

        /* $Auth_User = Auth::user();
          if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
          {
          $Model_Data = Sample::find($id);

          if (empty($Model_Data))
          {
          Flash::error($this->msg_not_found);
          return redirect(route($this->home_route));
          }

          $Model_Data->delete();

          Flash::success($this->msg_deleted);
          return redirect(route($this->home_route));
          }
          else
          {
          Flash::error($this->delete_permission_error_message);
          return redirect()->route($this->home_route);
          } */
    }

}
