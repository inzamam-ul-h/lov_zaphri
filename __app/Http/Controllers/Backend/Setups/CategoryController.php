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
use App\Models\ContactDetail;
use App\Models\User;
use App\Models\Category;

class CategoryController extends MainController {

    private $repository;
    protected $uploads_root = "uploads";
    private $uploads_categories_path = "uploads/categories";
    private $views_path = "backend.setups.categories";
    private $home_route = "categories.index";
    private $create_route = "categories.create";
    private $edit_route = "categories.edit";
    private $view_route = "categories.show";
    private $delete_route = "categories.destroy";
    private $active_route = "categories.activate";
    private $inactive_route = "categories.deactivate";
    private $addFeature_route = "categories.addFeatured";
    private $removeFeature_route = "categories.removeFeatured";
    private $make_default_route = "categories.make_default";
    private $msg_created = "Category created successfully.";
    private $msg_updated = "Category updated successfully.";
    private $msg_deleted = "Category deleted successfully.";
    private $msg_not_found = "Category not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same name";
    private $msg_active = "Category made Active successfully.";
    private $msg_inactive = "Category made InActive successfully.";
    private $msg_addFeature = "Category added to Featured Listing successfully.";
    private $msg_removeFeature = "Category removed from Featured Listing successfully.";
    private $msg_default = "Category made Default successfully.";
    private $msg_cant_inactive = " Default Category can not be inactive.";
    private $msg_cant_default = " This Category can not be set as default.";
    private $list_permission = "categories-listing";
    private $add_permission = "categories-add";
    private $edit_permission = "categories-edit";
    private $view_permission = "categories-view";
    private $status_permission = "categories-status";
    private $delete_permission = "categories-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of Categorys. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add Category. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update Category. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View Category details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of Category. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete Category. Please Contact Administrator.";

    /**
     * Display a listing of the Model.
     *
     *
     * @return Response
     */
    public function index() {
        // dd("index called");
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 0;
            $records = Category::select(['id'])->where('id', '>=', 1)->limit(1)->get();
            $categories = Category::where('id', '>=', 1)->paginate(10);
            foreach ($records as $record) {
                $records_exists = 1;
            }

            // dd($categories);

            return view($this->views_path . '.listing', compact("records_exists", 'categories'));
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function datatable(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $user_type = $Auth_User->user_type;

            if ($user_type == 0) {
                return $this->admin_datatable($request);
            }
        }
        else {
            Flash::error($this->list_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    public function admin_datatable(Request $request) {
        // dd("check categoreis");
        $Auth_User = Auth::user();

        $Records = Category::select(['categories.id', 'categories.name', 'categories.status']);

        $response = Datatables::of($Records)
                ->filter(function ($query) use ($request) {
                    if ($request->has('name') && !empty($request->name)) {
                        $query->where('categories.name', 'like', "%{$request->get('name')}%");
                    }



                    if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                        $query->where('categories.status', '=', "{$request->get('status')}");
                    }



                    if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                        $query->whereDate('categories.created_at', '=', "{$request->get('created_at')}");
                    }

                    if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                        $query->whereDate('categories.updated_at', '=', "{$request->get('updated_at')}");
                    }
                })
                ->addColumn('sr_no', function ($Records) {
                    $str = '';
                    return $str;
                })
                ->addColumn('name', function ($Records) {
                    $record_id = $Records->id;

                    $str = '<a class="text-warning" href="' . route($this->view_route, $record_id) . '">' . $Records->name . '</a>';

                    return $str;
                })
                ->addColumn('status', function ($Records) {
                    $str = dispaly_status_in_table($Records->status);

                    return $str;
                })
                ->addColumn('action', function ($Records) {
                    $record_id = $Records->id;
                    $Auth_User = Auth::user();
                    $is_featured = $Records->is_featured;
                    $status = $Records->status;
                    $is_default = $Records->is_default;

                    $str = '
				';

                    // if($Auth_User->can($this->view_permission) || $Auth_User->can('all'))
                    // {
                    // 	$str.= view_link_in_table($this->view_route, $record_id);
                    // }

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

                    if ($Auth_User->can($this->delete_permission) || $Auth_User->can('all')) {
                        $str .= delete_modal_in_table($this->delete_route, $record_id, $Records->name);
                    }
                    return $str;
                })
                ->rawColumns(['sr_no', 'name', 'status', 'action', 'is_default'])
                ->setRowId(function ($Records) {
                    return 'myDtRow' . $Records->id;
                })
                ->make(true);

        return $response;
    }

    public function is_not_authorized($id, $Auth_User) {
        $user_type = $Auth_User->user_type;

        $bool = 1;
        if ($user_type == 0) {
            $bool = 0;
        }
        elseif ($user_type == 'employer') {
            $employer_id = $Auth_User->refer_id;
            $records = Category::select(['id'])->where('id', '=', $id)->where('employer_id', '=', $employer_id)->limit(1)->get();
            foreach ($records as $record) {
                $bool = 0;
            }
        }

        return $bool;
    }

    // private function create_uploads_directory($uploads_path)
    // {
    //     if(!is_dir($uploads_path))
    //     {
    //         $uploads_root = $this->uploads_root;
    //         $src_file = $uploads_root."/index.html";
    //         mkdir($uploads_path);
    //         $dest_file = $uploads_path."/index.html";
    //         copy($src_file,$dest_file);
    //     }
    //     return $uploads_path;
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // dd("create route");
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
        $employer_id = $Auth_User->refer_id;
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) { {
                $request->validate([
                    'name' => 'required|string|min:2|max:255|unique:categories,name',
                ]);
            }

            $status = $request->status;

            // if($Auth_User->user_type == 0)
            // {
            //     $Records = Category::where('status',1)->where('name',$request->name)->first();
            //     if(!empty($Records) && $Records != null)
            //     {
            //         Flash::error($this->msg_exists);
            //         return redirect()->route($this->home_route);
            //     }
            // }



            $Model_Data = new Category();

            if ($status == 0 || $status == 1) {
                $Model_Data->status = $status;
            }
            else {
                Flash::error('Invalid status type');
                return redirect()->route($this->home_route);
            }

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
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = Category::find($id);

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
     * @param  \App\Models\Model  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // dd("edit route hit ");
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Category::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($id, $Auth_User)) {
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
     * @param  \App\Models\Model  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        // dd("update rounte hit ");
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Category::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }


            $request->validate([
                'name' => 'required|string|min:2|max:255',
            ]);

            $Model_Data->name = $request->name;
            $Model_Data->status = $request->status;

            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            if (Auth::user() && Auth::user()->user_type == 'employer') {
                $log_array = array();
                $log_array['title'] = 'Categories Updated';
                $log_array['description'] = 'Updated Categories Details';
                employers_logs($Auth_User, $log_array);
            }

            Flash::success($this->msg_updated);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function addFeatured($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == 0 && ($Auth_User->can($this->status_permission) || $Auth_User->can('all'))) {
            $Model_Data = Category::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $Model_Data->status = 1;
            $Model_Data->is_featured = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();
            Flash::success($this->msg_addFeature);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function removeFeatured($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == 0 && ($Auth_User->can($this->status_permission) || $Auth_User->can('all'))) {
            $Model_Data = Category::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->is_featured = 0;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_removeFeature);
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
    public function makeActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = Category::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_active);
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
            $Model_Data = Category::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            if ($Model_Data->is_default == 1) {
                Flash::error($this->msg_cant_inactive);
                return redirect(route($this->home_route));
            }

            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            Flash::success($this->msg_inactive);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->status_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    public function make_default($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->user_type == 0) {

            if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
                $Model_Data = Category::find($id);

                if (empty($Model_Data)) {
                    Flash::error($this->msg_not_found);
                    return redirect(route($this->home_route));
                }
                $cat_id = $Model_Data->id;

                if ($Model_Data->employer_id != 0) {
                    Flash::error($this->msg_cant_default);
                    return redirect(route($this->home_route));
                }
                $Defaultcategories = Category::where('is_default', 1)->get();
                foreach ($Defaultcategories as $key => $Defaultcategory) {
                    $Defaultcategory->is_default = 0;
                    $Defaultcategory->updated_by = $Auth_User->id;
                    $Defaultcategory->save();
                }

                if ($id >= 1) {
                    $Model_Data->is_default = 1;
                    $Model_Data->status = 1;
                    $Model_Data->updated_by = $Auth_User->id;
                    $Model_Data->save();

                    Flash::success($this->msg_default);
                }

                return redirect(route($this->home_route));
            }
            else {
                Flash::error($this->status_permission_error_message);
                return redirect()->route($this->home_route);
            }
        }
        else {
            Flash::error($this->status_permission_error_message);
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

        $Auth_User = Auth::user();
        if ($Auth_User->can($this->delete_permission) || $Auth_User->can('all')) {
            $Model_Data = Category::find($id);

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $Model_Data->delete();

            Flash::success($this->msg_deleted);
            return redirect(route($this->home_route));
        }
        else {
            Flash::error($this->delete_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

}
