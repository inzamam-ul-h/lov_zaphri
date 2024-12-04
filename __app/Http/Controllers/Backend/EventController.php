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
use App\Models\AgeGroup;
use App\Models\Event;
use App\Models\UserInterest;
use App\Models\Subscriber;
use App\Models\ContactDetail;

class EventController extends MainController {

    private $views_path = "backend.events";
    private $home_route = "events.index";
    private $create_route = "events.create";
    private $edit_route = "events.edit";
    private $view_route = "events.show";
    private $delete_route = "events.destroy";
    private $active_route = "events.activate";
    private $inactive_route = "events.deactivate";
    private $msg_created = "Event added successfully.";
    private $msg_updated = "Event updated successfully.";
    private $msg_deleted = "Event deleted successfully.";
    private $msg_not_found = "Event not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same Event name";
    private $list_permission = "events-listing";
    private $add_permission = "events-add";
    private $edit_permission = "events-edit";
    private $view_permission = "events-view";
    private $status_permission = "events-status";
    private $delete_permission = "events-delete";
    private $list_permission_error_message = "Error: You are not authorized to View Listings of event. Please Contact Administrator.";
    private $add_permission_error_message = "Error: You are not authorized to Add event. Please Contact Administrator.";
    private $edit_permission_error_message = "Error: You are not authorized to Update event. Please Contact Administrator.";
    private $view_permission_error_message = "Error: You are not authorized to View product details. Please Contact Administrator.";
    private $status_permission_error_message = "Error: You are not authorized to change status of event. Please Contact Administrator.";
    private $delete_permission_error_message = "Error: You are not authorized to Delete event. Please Contact Administrator.";

    /**
     * Display a listing of the resource.
     *
     */
    public function index() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->list_permission) || $Auth_User->can('all')) {
            $records_exists = 0;
            $records = Event::select(['id'])->where('id', '>=', 1)->limit(1)->get();
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

            $Records = Event::join('users', 'events.user_id', '=', 'users.id')
                    ->select(['events.*', 'users.name as user_name']);

            if ($Auth_User->user_type == $this->_CLUB_USER) {
                $Records = Event::join('users', 'events.user_id', '=', 'users.id')
                                ->select(['events.*', 'users.name as user_name'])->where('events.user_id', $Auth_User->id);
            }

            $response = Datatables::of($Records)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('title') && !empty($request->title)) {
                            $query->where('events.title', 'like', "%{$request->get('title')}%");
                        }

                        if ($request->has('name') && !empty($request->name)) {
                            $query->where('users.name', 'like', "%{$request->get('name')}%");
                        }

                        if ($request->has('status') && !empty($request->status) && $request->get('status') != -1) {
                            $query->where('events.status', '=', "{$request->get('status')}");
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
                    ->addColumn('start_date', function ($Records) {
                        $str = date('Y-m-d H-i-s', $Records->start_date_time);

                        return $str;
                    })
                    ->addColumn('age_group', function ($Records) {
                        $str = $Records->age_group;

                        return $str;
                    })
                    ->addColumn('attendees', function ($Records) {
                        $str = $Records->inquiry_status;
                        return $str;
                    })
                    ->addColumn('status', function ($Records) {
                        $str = dispaly_status_in_table($Records->status);
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
                    ->rawColumns(['sr_no', 'title', 'user', 'start_date', 'age_group', 'attendees', 'status', 'action'])
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
     */
    public function create() {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            $age_group = AgeGroup::select()->get();
            return view($this->views_path . '.create', compact('age_group'));
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
     */
    public function store(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->add_permission) || $Auth_User->can('all')) {
            $request->validate([
                'age_group'       => 'required',
                'title'           => 'required',
                'meeting_link'    => 'required',
                'description'     => 'required',
                'start_date_time' => 'required',
                'video'           => 'required|mimes:mp4,ogv,webm|max:20480',
                'banner'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $documents = $request->documents;
            foreach ($documents as $document) {
                $request->validate([
                    'multi_img.*' => ['required', 'mimes:doc,docx,pdf,jpeg,png,jpg,gif,svg', 'max:2048'],
                ]);
            }
            $images = $request->images;

            foreach ($images as $img) {
                $request->validate([
                    'multi_img.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                ]);
            }

            $title = $request->title;
            $meeting_link = $request->meeting_link;
            $description = $request->description;
            $age_groups = $request->age_group;
            $start_date_time = strtotime($request->start_date_time);
            $status = 1;
            $user_id = $Auth_User->id;

            $Model_Data = new Event;
            $Model_Data->user_id = $user_id;
            $Model_Data->title = $title;
            $Model_Data->description = $description;
            $Model_Data->meeting_link = $meeting_link;
            $Model_Data->age_group = $age_groups;
            $Model_Data->start_date_time = $start_date_time;
            $Model_Data->status = 1;
            $Model_Data->created_by = $Auth_User->id;

            $Model_Data->save();
            $event_id = $Model_Data->id;

            $uploadsPath = $this->uploads_events . '/' . $event_id;
            $file_banner = "default_image";
            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_banner = $fileName;
            }

            $file_video = "default_image";
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_video = $fileName;
            }

            $images_attachment = "default_image";
            if ($request->hasFile('images')) {
                $images = $request->images;
                foreach ($images as $file) {
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    if ($images_attachment == '' && $images_attachment == 'default_image')
                        $images_attachment = $fileName;
                    else
                        $images_attachment .= ',' . $fileName;
                }
            }

            $documents = "default_image";
            $original_documents_name = "default_image";
            if ($request->hasFile('documents')) {
                $images = $request->documents;
                foreach ($images as $file) {
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    if ($documents == '' && $documents == 'default_image') {
                        $documents = $fileName;
                        $original_documents_name = $fileName;
                    }
                    else {
                        $documents .= ',' . $fileName;
                        $original_documents_name .= ',' . $file->getClientOriginalName();
                    }
                }
            }
            $Model_Data = Event::find($event_id);
            $Model_Data->banner = $file_banner;
            $Model_Data->video = $file_video;
            $Model_Data->images = $images_attachment;
            $Model_Data->documents = $documents;
            $Model_Data->original_documents_name = $original_documents_name;
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
     */
    public function show($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->view_permission) || $Auth_User->can('all')) {
            $Model_Data = Event::find($id);

            if (empty($Model_Data)) {

                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $age_group = AgeGroup::select('*')->where('id', $Model_Data->age_group)->where('status', 1)->first();
            $Model_Data->age_group = $age_group->title;
            $Model_Data->start_date_time = date('Y-m-d', $Model_Data->start_date_time);
            return view($this->views_path . '.show', compact('Model_Data'));
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
     */
    public function file_delete($id, $type, Request $request) {
        $Auth_User = Auth::user();
        if (($Auth_User->can($this->edit_permission) || $Auth_User->can('all'))) {
            $file_name = $request->file_name;
            if (empty($file_name)) {
                return response()->json(['status' => false, 'messages' => $this->msg_not_found]);
            }
            $Model_Data = Event::find($id);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $uploadsPath = $this->uploads_events . '/' . $Model_Data->id;
            if ($type == 'video') {
                if ($file_name == $Model_Data->video) {
                    $old_file_path = $uploadsPath . '/' . $Model_Data->video;
                    File::delete($uploadsPath . "/" . $Model_Data->video);
                    $Model_Data->video = "default_image";
                }
            }
            elseif ($type == 'images') {
                $str = '';
                $images = explode(",", $Model_Data->images);
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

                $Model_Data->images = $str;
            }
            elseif ($type == 'banner') {
                if ($file_name == $Model_Data->banner) {

                    $old_file_path = $uploadsPath . '/' . $Model_Data->banner;
                    File::delete($uploadsPath . "/" . $Model_Data->banner);
                    $Model_Data->banner = "default_image";
                }
            }
            elseif ($type == 'documents') {
                $str = '';
                $documents = explode(",", $Model_Data->documents);

                foreach ($documents as $document) {
                    $document = trim($document);
                    if ($file_name == $document) {
                        if ($document != "" && $document != "user.png" && $document != "default_image") {
                            File::delete($uploadsPath . "/" . $document);
                        }
                    }
                    else {
                        $str .= ',' . $document;
                    }
                }

                if (empty($str)) {
                    $str = "default_image";
                }
                $Model_Data->documents = $str;
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
            $Model_Data = Event::find($id);

            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }
            $age_group = AgeGroup::select()->get();
            $Model_Data->start_date_time = date('Y-m-d', $Model_Data->start_date_time);
            return view($this->views_path . '.edit', compact('Model_Data', 'age_group'));
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
     */
    public function update(Request $request, $id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $Model_Data = Event::find($id);
            if (empty($Model_Data) || $this->is_not_authorized($Model_Data, $Auth_User)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            $request->validate([
                'age_group' => 'required',
                'title'     => 'required',
                'video'     => 'mimes:mp4,ogv,webm|max:20480',
                'banner'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);

            $documents = $request->documents;
            if (isset($request->documents)) {
                foreach ($documents as $document) {
                    $request->validate([
                        'multi_img.*' => ['required', 'mimes:doc,docx,pdf,jpeg,png,jpg,gif,svg', 'max:2048'],
                    ]);
                }
            }
            $images = $request->images;
            if (isset($request->images)) {
                foreach ($images as $img) {
                    $request->validate([
                        'multi_img.*' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                    ]);
                }
            }
            $event_id = $Model_Data->id;
            $uploadsPath = $this->uploads_events . '/' . $event_id;
            $file_banner = $Model_Data->banner;
            $old_file = $Model_Data->banner;
            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_banner = $fileName;

                if ($old_file != "" && $old_file != "default_image") {
                    $old_file_path = $uploadsPath . '/' . $old_file;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }

            $file_video = $Model_Data->video;
            $old_video = $Model_Data->video;
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $fileName = $this->upload_file_to_path($file, $uploadsPath);
                $file_video = $fileName;

                if ($old_video != "" && $old_video != "default_image") {
                    $old_file_path = $uploadsPath . '/' . $old_video;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }

            $images_attachment = $Model_Data->images;
            if ($request->hasFile('images')) {
                $images = $request->images;
                foreach ($images as $file) {
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    if ($images_attachment == '' && $images_attachment == 'default_image')
                        $images_attachment = $fileName;
                    else
                        $images_attachment .= ',' . $fileName;
                }
            }

            $documents = $Model_Data->documents;
            $original_documents_name = $Model_Data->original_documents_name;
            if ($request->hasFile('documents')) {

                $images = $request->documents;
                foreach ($images as $file) {
                    $fileName = $this->upload_file_to_path($file, $uploadsPath);
                    if ($documents == '' && $documents == 'default_image') {
                        $documents = $fileName;
                        $original_documents_name = $fileName;
                    }
                    else {
                        $documents .= ',' . $fileName;
                        $original_documents_name .= ',' . $file->getClientOriginalName();
                    }
                }
            }
            $Model_Data->banner = $file_banner;
            $Model_Data->video = $file_video;
            $Model_Data->images = $images_attachment;
            $Model_Data->documents = $documents;
            $Model_Data->original_documents_name = $original_documents_name;
            $Model_Data->title = $request->title;
            $Model_Data->meeting_link = $request->meeting_link;
            $Model_Data->description = $request->description;
            $Model_Data->age_group = $request->age_group;
            $Model_Data->start_date_time = strtotime($request->start_date_time);
            $Model_Data->status = 1;
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

     */
    public function makeActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = Event::find($id);
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

     */
    public function makeInActive($id) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->status_permission) || $Auth_User->can('all')) {
            $Model_Data = Event::find($id);
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
