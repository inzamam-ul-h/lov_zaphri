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
use App\Models\Video;
use App\Models\Category;

class VideoController extends MainController {

    private $views_path = "backend.videos";
    private $home_route = "videos.index";
    private $create_route = "videos.create";
    private $edit_route = "videos.edit";
    private $view_route = "videos.show";
    private $delete_route = "videos.destroy";
    private $active_route = "videos.activate";
    private $inactive_route = "videos.deactivate";
    private $msg_created = "Video added successfully.";
    private $msg_updated = "Video updated successfully.";
    private $msg_deleted = "Video deleted successfully.";
    private $msg_not_found = "Video not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Video name";
    private $msg_active = "Video made Active successfully.";
    private $msg_inactive = "video made InActive successfully.";
    private $list_permission = "videos-listing";
    private $add_permission = "videos-add";
    private $edit_permission = "videos-edit";
    private $view_permission = "videos-view";
    private $status_permission = "videos-status";
    private $delete_permission = "videos-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of videos. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add videos. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update videos. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View product details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of videos. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete videos. Please Contact Administrator.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 1;

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
            $Records = Video::join('users', 'videos.user_id', '=', 'users.id')
                    ->join('categories', 'videos.category', '=', 'categories.id')
                    ->select('videos.id', 'videos.title', 'videos.created_by', 'users.user_type', 'videos.status', 'users.name as user_name', 'categories.name as category_name')
                    ->orderBy('videos.id', 'DESC');

            switch ($Auth_User->user_type) {
                case $this->_CLUB_USER: {
                        $club = $Auth_User;
                        $coaches_ids = $this->get_club_coach_ids($club);
                        $Records = $Records->whereIn('videos.user_id', $coaches_ids)->orWhereIn('videos.user_id', [$club->id]);
                    }
                    break;

                case $this->_COACH_USER: {
                        $coach_id = $Auth_User->id;
                        $club = get_club_id($coach_id);
                        $Records = $Records->where('videos.user_id', '=', $coach_id)
                                ->orWhere('videos.user_id', '=', $club);
                    }
                    break;
                case $this->_PLAYER_USER: {
                        $player_id = $Auth_User->id;
                        $club = get_club_id($player_id);
                        $Records = $Records->where('videos.user_id', $club);
                    }
                    break;

                case $this->_PARENT_USER: {
                        $coach_id = 0;
                        $Records = $Records->where('videos.user_id', '=', $coach_id);
                    }
                    break;
                default: {
                        //
                    }
                    break;
            }
            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('title') && !empty($request->title)) {
                            $query->where('videos.plan_name', 'like', "%{$request->get('title')}%");
                        }

                        if ($request->has('status') && $request->get('status') != -1 && $request->get('status') != '') {
                            $query->where('videos.status', '=', "{$request->get('status')}");
                        }

                        if ($request->has('created_at') && $request->get('created_at') != '' && $request->get('created_at') != -1) {
                            $query->whereDate('videos.created_at', '=', "{$request->get('created_at')}");
                        }

                        if ($request->has('updated_at') && $request->get('updated_at') != '' && $request->get('updated_at') != -1) {
                            $query->whereDate('videos.updated_at', '=', "{$request->get('updated_at')}");
                        }
                    })
                    ->addColumn('sr_no', function ($Records) {
                        $str = '';
                        return $str;
                    })
                    ->addColumn('title', function ($Records) {
                        $record_id = $Records->id;
                        $title = $Records->title;

                        $str = '<a class="text-primary" href="' . route($this->view_route, $record_id) . '" title="View Details">' . $title . '</a>';

                        return $str;
                    })
                    ->addColumn('user', function ($Records) {
                        $str = $Records->user_name;
                        return $str;
                    })
                    ->addColumn('category', function ($Records) {
                        $str = $Records->category_name;
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
                    ->addColumn('status', function ($Records) {
                        $str = dispaly_status_in_table($Records->status);

                        return $str;
                    })
                    ->addColumn('created_by', function ($Records) {
                        $str = get_user_name($Records->created_by);
                        return $str;
                    })
                    ->addColumn('created_at', function ($Records) {
                        $str = dispaly_date_in_table($Records->created_at);
                        return $str;
                    })
                    ->addColumn('action', function ($Records) {
                        $record_id = $Records->id;
                        $Auth_User = Auth::user();
                        $status = $Records->status;

                        $str = '<div>';
                        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
                            $str .= view_link_in_table($this->view_route, $record_id) . '  &nbsp;';
                        }

                        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
                            $str .= edit_link_in_table($this->edit_route, $record_id) . '  &nbsp;';
                        }

                        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
                            if ($status == 1) {
                                $str .= inactive_link_in_table($this->inactive_route, $record_id) . '  &nbsp;';
                            }
                            else {
                                $str .= active_link_in_table($this->active_route, $record_id) . '  ';
                            }
                        }

                        $str .= '</div>';
                        return $str;
                    })
                    ->rawColumns(['title', 'user', 'user_type', 'created_by', 'category', 'status', 'action'])
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
            $categories = Category::where('status', 1)->get();

            return view($this->views_path . '.create', compact("categories"));
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
            $images = $request->image;
            $request->validate([
                'title'       => 'required',
                'duration'    => ['required', 'regex:/^([0-5]?[0-9]:[0-5][0-9])$/', 'after:00:00'],
                'category'    => 'required',
                'description' => 'required',
                'video'       => 'required|mimes:mp4,ogv,webm|max:20480',
            ]);
            foreach ($images as $img) {
                $request->validate([
                    'multi_img.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                ]);
            }

            $Model_Data = new Video();
            $Model_Data->user_id = $Auth_User->id;
            $Model_Data->title = $request->title;
            $Model_Data->category = $request->category;
            $Model_Data->duration = $request->duration;
            $Model_Data->description = $request->description;
            $Model_Data->author = $Auth_User->id;
            $Model_Data->recipients = 1;
            $Model_Data->status = 1;
            $Model_Data->created_by = $Auth_User->id;
            $Model_Data->save();

            $video_id = $Model_Data->id;

            $uploadsPath = $this->uploads_videos . '/' . $video_id;

            $file_video = "default_image";
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_video = $fileName;
            }

            $file_images = "default_image";
            if ($request->hasFile('image')) {
                $images = $request->image;
                foreach ($images as $file) {
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    if ($file_images == '' && $file_images == 'default_image')
                        $file_images = $fileName;
                    else
                        $file_images .= ',' . $fileName;
                }
            }

            $Model_Data = Video::find($video_id);
            $Model_Data->video = $file_video;
            $Model_Data->image = $file_images;
            $Model_Data->save();

            Flash::success($this->msg_created);
            return redirect()->route($this->home_route);
        }
        else {
            Flash::error($this->add_permission_error_message);
            return redirect()->route($this->home_route);
        }
    }

    private function get_model_data($video_id) {

        $Model_Data = Video::join('categories', 'videos.category', '=', 'categories.id')
                        ->join('users', 'videos.user_id', '=', 'users.id')
                        ->select('videos.*', 'categories.name as category_name', 'users.name as user_name')
                        ->where('videos.id', $video_id)->first();

        return $Model_Data;
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
            $Model_Data = Video::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $Model_Data = $this->get_model_data($id);
            $created_by = User:: select('*')->where('id', $Model_Data->created_by)->first();
            $Model_Data->created_by = $created_by->name;

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
    public function file_delete($id, $type, Request $request) {
        $Auth_User = Auth::user();
        if (($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {

            $file_name = $request->file_name;
            if (empty($file_name)) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }
            $Model_Data = Video::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $uploadsPath = $this->uploads_videos . '/' . $Model_Data->id;
            if ($type == 'video') {
                if ($file_name == $Model_Data->video) {

                    $old_file_path = $uploadsPath . '/' . $Model_Data->video;
                    File::delete($uploadsPath . "/" . $Model_Data->video);
                    $Model_Data->video = "default_image";
                }
            }
            elseif ($type == 'image') {
                $str = '';
                $images = explode(",", $Model_Data->image);
                foreach ($images as $image) {
                    $image = trim($image);
                    if ($file_name == $image) {
                        if ($image != "" && $image != "user.png" && $image != "default_image") {
                            File::delete($uploadsPath . "/" . $image);
                        }
                    }
                    else {
                        $str .= ',' . $image;
                    }
                }
                if (empty($str)) {
                    $str = "default_image";
                }
                $Model_Data->image = $str;
            }
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();
            return response()->json(['status' => true, 'messages' => 'File Successfully Deleted.']);
        }
        else {
            return response()->json(['status' => false, 'messages' => $this->edit_permission_error_message]);
        }
    }

    public function edit($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Video::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $Model_Data = $this->get_model_data($id);

            $categories = Category::where('status', 1)->get();

            return view($this->views_path . '.edit', compact("Model_Data", 'categories'));
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
            $Model_Data = Video::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $request->validate([
                'title'       => 'required',
                'duration'    => 'required',
                'category'    => 'required',
                'description' => 'required',
                'video'       => 'mimes:mp4,ogv,webm|max:20480',
            ]);
            if (isset($request->image)) {
                $images = $request->image;

                foreach ($images as $img) {
                    $request->validate([
                        'multi_img.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                    ]);
                }
            }

            $video_id = $Model_Data->id;
            $uploadsPath = $this->uploads_videos . '/' . $video_id;
            /*   $file_print = $Model_Data->print_image;
              $old_file = $Model_Data->print_image;
              if ($request->hasFile('print_image')) {
              $file = $request->file('print_image');
              $fileName = $this->upload_file_to_path($file, $uploadsPath);
              $file_print = $fileName;

              if ($old_file != "" && $old_file != "default_image") {
              $old_file_path = $uploadsPath .'/'. $old_file;
              if (file_exists($old_file_path)) {
              unlink($old_file_path);
              }
              }
              } */

            $file_video = $Model_Data->video;
            $old_file = $Model_Data->video;
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_video = $fileName;

                if ($old_file != "" && $old_file != "default_image") {
                    $old_file_path = $uploadsPath . '/' . $old_file;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }

            $file_images = $Model_Data->image;
            if ($request->hasFile('image')) {
                $images = $request->image;
                foreach ($images as $file) {
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    if ($file_images == '' && $file_images == 'default_image')
                        $file_images = $fileName;
                    else
                        $file_images .= ',' . $fileName;
                }
            }

            $Model_Data->title = $request->title;
            $Model_Data->print_title = $request->p_title;
            $Model_Data->category = $request->category;
            $Model_Data->duration = $request->duration;
            $Model_Data->description = $request->description;
            $Model_Data->recipients = 1;
            $Model_Data->video = $file_video;
            $Model_Data->image = $file_images;
            $Model_Data->status = 1;
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
            $Model_Data = Video::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
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
            $Model_Data = Video::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
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

    public function is_not_authorized($Model_Data, $Auth_User, $is_edit = TRUE) {
        $user_type = $Auth_User->user_type;
        $club_id = $Model_Data->user_id;
        $bool = 1;
        if ($Model_Data->user_id == $Auth_User->id) {
            $bool = 0;
        }
        else {
            if ($Auth_User->can('all')) {
                $bool = 0;
            }
            else if ($is_edit == FALSE && ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER)) {

                $user_club_id = get_club_id($Auth_User->id);

                if ($user_club_id == $club_id) {

                    $bool = 0;
                }
            }
        }

        return $bool;
    }

}
