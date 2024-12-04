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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Module;

class RoleController extends MainController {

    private $views_path = "backend.roles";
    private $home_route = "roles.index";
    private $create_route = "roles.create";
    private $edit_route = "roles.edit";
    private $view_route = "roles.show";
    private $delete_route = "roles.destroy";
    private $msg_created = "Role added successfully.";
    private $msg_updated = "Role updated successfully.";
    private $msg_deleted = "Role deleted successfully.";
    private $msg_not_found = "Role not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Role name";
    private $permissions_updated = "Role Permissions updated successfully.";
    private $list_permission = "roles-listing";
    private $add_permission = "roles-add";
    private $edit_permission = "roles-edit";
    private $view_permission = "roles-view";
    private $status_permission = "roles-status";
    private $delete_permission = "roles-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Roles. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Role. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Role. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Role details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Role. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Role. Please Contact Administrator.";

    /**
     * Display a listing of the Model.
     *
     * 
     * @return Response
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $Auth_User = Auth::user();

            $records_exists = 0;
            $records = Role::select(['id'])->where('id', '>=', 1)->get();
            foreach ($records as $record) {
                $records_exists = 1;
            }

            return view($this->views_path . '.listing', compact("records_exists"));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function datatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $Records = Role::select(['roles.id', 'roles.name as title', 'roles.display_to', 'roles.created_at', 'roles.updated_at']);

            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('title') && !empty($request->title)) {
                            $query->where('roles.name', 'like', "%{$request->get('title')}%");
                        }

                        if ($request->has('display_to') && $request->get('display_to') != -1 && $request->get('display_to') != '') {
                            $query->where('roles.display_to', '=', "{$request->get('display_to')}");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('display_to', function ($Records) {
                        $record_id = $Records->id;
                        $display_to = $Records->display_to;

                        if ($display_to == 0) {
                            $display_to = "Admin Users Only";
                        }
                        elseif ($display_to == 1) {
                            $display_to = "Others";
                        }

                        return $display_to;
                    })
                    ->addColumn('created_at', function ($Records) {
                        $str = dispaly_date_in_table($Records->created_at);
                        return $str;
                    })
                    ->addColumn('updated_at', function ($Records) {
                        $str = dispaly_date_in_table($Records->updated_at);
                        return $str;
                    })
                    ->addColumn('action', function ($Records) {
                        $record_id = $Records->id;
                        $Auth_User = Auth::user();

                        $str = '<div>';

                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $record_id);
                        }

                        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
                            $str .= edit_link_in_table($this->edit_route, $record_id);
                        }

                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_link_in_table($record_id);
                          } */

                        $str .= '</div>';
                        /* if($Auth_User->can($this->delete_permission) || $Auth_User->can('all'))
                          {
                          $str.= delete_modal_in_table($this->delete_route, $record_id, $Records->title);
                          } */
                        return $str;
                    })
                    ->rawColumns(['sr_no', 'title', 'display_to', 'created_at', 'updated_at', 'action'])
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
            $Auth_User = Auth::user();
            $user_id = $Auth_User->id;
            return view($this->views_path . '.create', compact("user_id"));
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
                'name' => 'required'
            ]);

            $name = ltrim(rtrim($request->name));

            $Auth_User = Auth::user();
            $user_id = $Auth_User->id;

            if ($name != '') {
                $bool = 0;
                $Model_Results = Role::where('name', '=', $name)->get();
                foreach ($Model_Results as $Model_Result) {
                    $bool = 1;
                }
                if ($bool == 0) {
                    $Model_Data = new Role();
                    $Model_Data->name = $name;
                    $Model_Data->guard_name = 'web';
                    $Model_Data->display_to = $request->display_to;
                    $Model_Data->created_by = $Auth_User->id;
                    $Model_Data->save();

                    Flash::success($this->msg_created);
                    return redirect()->route($this->view_route, $Model_Data->id);
                }
                else {
                    Flash::error($this->msg_exists);
                    return redirect()->route($this->create_route);
                }
            }
            else {
                Flash::error($this->msg_required);
                return redirect()->route($this->create_route);
            }
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = Role::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }


            $Modules_1 = $Modules = Module::orderby('id', 'asc')->get();

            $list_array = array();
            $add_array = array();
            $edit_array = array();
            $view_array = array();
            $status_array = array();
            $delete_array = array();

            $role = $Model_Data;

            foreach ($Modules as $Module) {
                $module_id = $Module->id;
                $module_name = $Module->module_name;

                $action = "listing";
                if ($Module->mod_list == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $list_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $list_array[$module_id] = 1;
                    }
                }

                $action = "add";
                if ($Module->mod_add == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $add_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $add_array[$module_id] = 1;
                    }
                }

                $action = "edit";
                if ($Module->mod_edit == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $edit_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $edit_array[$module_id] = 1;
                    }
                }

                $action = "view";
                if ($Module->mod_view == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $view_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $view_array[$module_id] = 1;
                    }
                }

                $action = "status";
                if ($Module->mod_status == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $status_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $status_array[$module_id] = 1;
                    }
                }

                $action = "delete";
                if ($Module->mod_delete == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $delete_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $delete_array[$module_id] = 1;
                    }
                }
            }

            return view($this->views_path . '.show', compact("Model_Data", "Modules", "Modules_1", "list_array", "add_array", "edit_array", "view_array", "status_array", "delete_array"));
        }
        else {
            Flash::error($this->view_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Role::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Modules_1 = $Modules = Module::orderby('id', 'asc')->get();

            $list_array = array();
            $add_array = array();
            $edit_array = array();
            $view_array = array();
            $status_array = array();
            $delete_array = array();

            $role = $Model_Data;

            foreach ($Modules as $Module) {
                $module_id = $Module->id;
                $module_name = $Module->module_name;

                $action = "listing";
                if ($Module->mod_list == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $list_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $list_array[$module_id] = 1;
                    }
                }

                $action = "add";
                if ($Module->mod_add == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $add_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $add_array[$module_id] = 1;
                    }
                }

                $action = "edit";
                if ($Module->mod_edit == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $edit_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $edit_array[$module_id] = 1;
                    }
                }

                $action = "view";
                if ($Module->mod_view == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $view_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $view_array[$module_id] = 1;
                    }
                }

                $action = "status";
                if ($Module->mod_status == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $status_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $status_array[$module_id] = 1;
                    }
                }

                $action = "delete";
                if ($Module->mod_delete == 1) {
                    $permission = $module_name . '-' . $action;
                    $permission = createSlug($permission);

                    $delete_array[$module_id] = 0;
                    if ($role->hasPermissionTo($permission)) {
                        $delete_array[$module_id] = 1;
                    }
                }
            }

            return view($this->views_path . '.edit', compact("Model_Data", "Modules", "Modules_1", "list_array", "add_array", "edit_array", "view_array", "status_array", "delete_array"));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Model  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Role::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $request->validate([
                'name' => 'required'
            ]);

            $name = ltrim(rtrim($request->name));
            if ($name != '') {
                $bool = 0;
                $Model_Results = Role::where('name', '=', $name)->where('id', '!=', $id)->get();
                foreach ($Model_Results as $Model_Result) {
                    $bool = 1;
                }
                if ($bool == 0) {
                    $Model_Data = Role::find($id);
                    $Model_Data->name = $name;
                    $Model_Data->guard_name = 'web';
                    $Model_Data->display_to = $request->display_to;
                    $Model_Data->updated_by = $Auth_User->id;
                    $Model_Data->save();

                    Flash::success($this->msg_updated);
                    return redirect()->route($this->view_route, $Model_Data->id);
                }
                else {
                    Flash::error($this->msg_exists);
                    return redirect()->route($this->edit_route, $id);
                }
            }
            else {
                Flash::error($this->msg_required);
                return redirect()->route($this->edit_route, $id);
            }
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function permission_update($id, Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Role::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $role = $Model_Data;

            $counter = 0;

            $Modules = Module::orderby('id', 'asc')->get();
            foreach ($Modules as $Module) {
                $module_id = $Module->id;
                $module_name = $Module->module_name;

                $action = "listing";
                $permission = $module_name . '-' . $action;
                $permission = createSlug($permission);
                if ($Module->mod_list == 1) {
                    $insert = 0;
                    $exits = 0;

                    if ($role->hasPermissionTo($permission)) {
                        $exits = 1;
                    }


                    if (isset($request->list_module[$counter]) && $request->list_module[$counter] == 1) {
                        $insert = 1;
                    }

                    if ($exits == 0 && $insert == 1) {
                        $role->givePermissionTo($permission);
                    }
                    elseif ($exits == 1 && $insert == 0) {
                        $role->revokePermissionTo($permission);
                    }
                }
                elseif ($role->hasPermissionTo($permission)) {
                    $role->revokePermissionTo($permission);
                }

                $action = "add";
                $permission = $module_name . '-' . $action;
                $permission = createSlug($permission);
                if ($Module->mod_add == 1) {
                    $insert = 0;
                    $exits = 0;

                    if ($role->hasPermissionTo($permission)) {
                        $exits = 1;
                    }


                    if (isset($request->add_module[$counter]) && $request->add_module[$counter] == 1) {
                        $insert = 1;
                    }

                    if ($exits == 0 && $insert == 1) {
                        $role->givePermissionTo($permission);
                    }
                    elseif ($exits == 1 && $insert == 0) {
                        $role->revokePermissionTo($permission);
                    }
                }
                elseif ($role->hasPermissionTo($permission)) {
                    $role->revokePermissionTo($permission);
                }

                $action = "edit";
                $permission = $module_name . '-' . $action;
                $permission = createSlug($permission);
                if ($Module->mod_edit == 1) {
                    $insert = 0;
                    $exits = 0;

                    if ($role->hasPermissionTo($permission)) {
                        $exits = 1;
                    }


                    if (isset($request->edit_module[$counter]) && $request->edit_module[$counter] == 1) {
                        $insert = 1;
                    }

                    if ($exits == 0 && $insert == 1) {
                        $role->givePermissionTo($permission);
                    }
                    elseif ($exits == 1 && $insert == 0) {
                        $role->revokePermissionTo($permission);
                    }
                }
                elseif ($role->hasPermissionTo($permission)) {
                    $role->revokePermissionTo($permission);
                }

                $action = "view";
                $permission = $module_name . '-' . $action;
                $permission = createSlug($permission);
                if ($Module->mod_view == 1) {
                    $insert = 0;
                    $exits = 0;

                    if ($role->hasPermissionTo($permission)) {
                        $exits = 1;
                    }


                    if (isset($request->view_module[$counter]) && $request->view_module[$counter] == 1) {
                        $insert = 1;
                    }

                    if ($exits == 0 && $insert == 1) {
                        $role->givePermissionTo($permission);
                    }
                    elseif ($exits == 1 && $insert == 0) {
                        $role->revokePermissionTo($permission);
                    }
                }
                elseif ($role->hasPermissionTo($permission)) {
                    $role->revokePermissionTo($permission);
                }

                $action = "status";
                $permission = $module_name . '-' . $action;
                $permission = createSlug($permission);
                if ($Module->mod_status == 1) {
                    $insert = 0;
                    $exits = 0;

                    if ($role->hasPermissionTo($permission)) {
                        $exits = 1;
                    }


                    if (isset($request->status_module[$counter]) && $request->status_module[$counter] == 1) {
                        $insert = 1;
                    }

                    if ($exits == 0 && $insert == 1) {
                        $role->givePermissionTo($permission);
                    }
                    elseif ($exits == 1 && $insert == 0) {
                        $role->revokePermissionTo($permission);
                    }
                }
                elseif ($role->hasPermissionTo($permission)) {
                    $role->revokePermissionTo($permission);
                }

                $action = "delete";
                $permission = $module_name . '-' . $action;
                $permission = createSlug($permission);
                if ($Module->mod_delete == 1) {
                    $insert = 0;
                    $exits = 0;

                    if ($role->hasPermissionTo($permission)) {
                        $exits = 1;
                    }


                    if (isset($request->delete_module[$counter]) && $request->delete_module[$counter] == 1) {
                        $insert = 1;
                    }

                    if ($exits == 0 && $insert == 1) {
                        $role->givePermissionTo($permission);
                    }
                    elseif ($exits == 1 && $insert == 0) {
                        $role->revokePermissionTo($permission);
                    }
                }
                elseif ($role->hasPermissionTo($permission)) {
                    $role->revokePermissionTo($permission);
                }
                $counter++;
            }


            Flash::success($this->permissions_updated);
            return redirect(route($this->edit_route, $id));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
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
          $Model_Data = Role::find($id);

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
