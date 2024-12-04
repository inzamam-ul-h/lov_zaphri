<?php

namespace App\Http\Controllers\Backend\Setups;

use App\Http\Controllers\MainController as MainController;
use App\Models\General;
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

class GeneralController extends MainController {

    // Define Default Settings ID
    private $id = 1;
    protected $uploads_root = "uploads";
    private $uploads_path = "uploads/settings/";
    private $views_path = "backend.setups.settings";
    private $home_route = "settings.index";
    private $create_route = "settings.index";
    private $edit_route = "settings.index";
    private $view_route = "settings.index";
    private $msg_updated = "Settings updated successfully.";
    private $msg_not_found = "Settings not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $edit_permission = "settings-edit";
    private $edit_permission_error_message = "Error: You are not authorized to Update Settings. Please Contact Administrator.";

    // private function create_uploads_directory()
    // {
    // 	$uploads_path = $this->uploads_path;
    // 	if(!is_dir($uploads_path))
    // 	{
    // 		mkdir($uploads_path);
    // 		$uploads_root = $this->uploads_root;
    // 		$src_file = $uploads_root."/index.html";
    // 		$dest_file = $uploads_path."/index.html";
    // 		copy($src_file,$dest_file);
    // 	}
    // }

    public function index() {

        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $id = $this->id;

            $Model_Data = ContactDetail::find($id);
            $Setting_Data = General::find($id);
            //  dd($Setting_Data);
            // dd($Model_Data);
            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }

            return view($this->views_path . '.edit', compact("Model_Data", "Setting_Data"));
        }
        else {

            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id = 1 for default settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $Auth_User = Auth::user();
        if ($Auth_User->can($this->edit_permission) || $Auth_User->can('all')) {
            $id = $this->id;

            $Model_Data = ContactDetail::find($id);

            if ($request->has('setting_submit_form')) {
                // Validate Form setting tab data
                // dd($request->all());

                $request->validate([
                    'site_url'          => 'required',
                    'support_email'     => 'required',
                    'paypal'            => 'required',
                    'paypal_account'    => 'required',
                    'paypal_client_id'  => 'required',
                    'paypal_secret_key' => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $modelData = General::find($id);
                $modelData->site_url = $request->input('site_url');
                $modelData->support_email = $request->input('support_email');
                $modelData->paypal = $request->input('paypal');
                $modelData->paypal_account = $request->input('paypal_account');
                $modelData->paypal_client_id = $request->input('paypal_client_id');
                $modelData->paypal_secret_key = $request->input('paypal_secret_key');
                // Update other fields as needed

                $modelData->save();
                Flash::success($this->msg_updated);

                return redirect()->back();
            }
            elseif ($request->has('submit_contact_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'about_zaphry'  => 'required',
                    'phone_no'      => 'required',
                    'contact_email' => 'required',
                    'address'       => 'required',
                    'whatsapp'      => 'required',
                    'facebook'      => 'required',
                    'twitter'       => 'required',
                    'dribble'       => 'required',
                    'linkdin'       => 'required',
                    'youtube'       => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $contact_detail = ContactDetail::find($id);
                $contact_detail->about_zaphry = $request->input('about_zaphry');
                $contact_detail->phone = $request->input('phone_no');
                $contact_detail->email = $request->input('contact_email');
                $contact_detail->address = $request->input('address');
                $contact_detail->facebook = $request->input('facebook');
                $contact_detail->whatsapp = $request->input('whatsapp');
                $contact_detail->twitter = $request->input('twitter');
                $contact_detail->dribble = $request->input('dribble');
                $contact_detail->linkdin = $request->input('linkdin');
                $contact_detail->youtube = $request->input('dribble');
                // Update other fields as needed

                $contact_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_verify_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'verify_subject' => 'required',
                    'verify_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->verify_subject = $request->input('verify_subject');
                $general_detail->verify_email = $request->input('verify_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_verification_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'verification_subject' => 'required',
                    'verification_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->verification_subject = $request->input('verification_subject');
                $general_detail->verification_email = $request->input('verification_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_welcome_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'welcome_subject' => 'required',
                    'welcome_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->welcome_subject = $request->input('welcome_subject');
                $general_detail->welcome_email = $request->input('welcome_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_forgot_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'forgot_subject' => 'required',
                    'forgot_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->forgot_subject = $request->input('forgot_subject');
                $general_detail->forgot_email = $request->input('forgot_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_reset_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'reset_subject' => 'required',
                    'reset_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->reset_subject = $request->input('reset_subject');
                $general_detail->reset_email = $request->input('reset_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_request_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'request_subject' => 'required',
                    'request_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->request_subject = $request->input('request_subject');
                $general_detail->request_email = $request->input('request_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_booking_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'booking_subject' => 'required',
                    'booking_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->booking_subject = $request->input('booking_subject');
                $general_detail->booking_email = $request->input('booking_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_reschedule_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'reschedule_subject' => 'required',
                    'reschedule_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->reschedule_subject = $request->input('reschedule_subject');
                $general_detail->reschedule_email = $request->input('reschedule_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
                // return redirect()->back()->with('success', 'Settings updated successfully');
            }
            elseif ($request->has('submit_cancel_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'cancel_subject' => 'required',
                    'cancel_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->cancel_subject = $request->input('cancel_subject');
                $general_detail->cancel_email = $request->input('cancel_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
            }
            elseif ($request->has('inquire_event_submit_form')) {
                // Validate Form setting tab data
                // dd($request->all());
                $request->validate([
                    'event_inquiry_subject' => 'required',
                    'event_inquiry_email'   => 'required',
                        // Add validation rules specific to the settings form
                ]);

                // Update the data
                $general_detail = General::find($id);
                $general_detail->inquire_event_subject = $request->input('event_inquiry_subject');
                $general_detail->inquire_event_email = $request->input('event_inquiry_email');

                // Update other fields as needed

                $general_detail->save();
                Flash::success($this->msg_updated);
                return redirect()->back();
            }

            if (empty($Model_Data)) {
                Flash::error($this->msg_not_found);
                return redirect(route($this->home_route));
            }




            $FR_check = FR_language_check();

            $this->validate($request, [
                'logo_en'    => 'mimes:png,jpeg,jpg,gif|max:4000',
                'logo_fr'    => 'mimes:png,jpeg,jpg,gif|max:4000',
                'fav_icon'   => 'mimes:png,jpeg,jpg,gif|max:2000',
                'apple_icon' => 'mimes:png,jpeg,jpg,gif|max:2000'
            ]);

            // Start of Upload Files
            $i = 0;
            $file_name = "logo_en";
            $logo_en = "";
            if (isset($request->$file_name) && $request->$file_name != "") {
                $i++;
                $file_uploaded = $request->file($file_name);
                $logo_en = date('YmdHis') . $i . "." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $logo_en);

                if ($Model_Data->logo_en != "") {
                    File::delete($uploads_path . "/" . $Model_Data->logo_en);
                }
            }
            if ($logo_en != "") {
                $Model_Data->logo_en = $logo_en;
            }

            $file_name = "logo_fr";
            $logo_fr = "";
            if (isset($request->$file_name) && $request->$file_name != "") {
                $i++;
                $file_uploaded = $request->file($file_name);
                $logo_fr = date('YmdHis') . $i . "." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $logo_fr);

                if ($Model_Data->logo_fr != "") {
                    File::delete($uploads_path . "/" . $Model_Data->logo_fr);
                }
            }
            if ($logo_fr != "") {
                $Model_Data->logo_fr = $logo_fr;
            }

            $file_name = "fav_icon";
            $fav_icon = "";
            if (isset($request->$file_name) && $request->$file_name != "") {
                $i++;
                $file_uploaded = $request->file($file_name);
                $fav_icon = date('YmdHis') . $i . "." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $fav_icon);

                if ($Model_Data->fav_icon != "") {
                    File::delete($uploads_path . "/" . $Model_Data->fav_icon);
                }
            }
            if ($fav_icon != "") {
                $Model_Data->fav_icon = $fav_icon;
            }


            $file_name = "apple_icon";
            $apple_icon = "";
            if (isset($request->$file_name) && $request->$file_name != "") {
                $i++;
                $file_uploaded = $request->file($file_name);
                $apple_icon = date('YmdHis') . $i . "." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $apple_icon);

                if ($Model_Data->apple_icon != "") {
                    File::delete($uploads_path . "/" . $Model_Data->apple_icon);
                }
            }
            if ($apple_icon != "") {
                $Model_Data->apple_icon = $apple_icon;
            }
            // End of Upload Files

            $Model_Data->site_title_en = $request->site_title_en;
            $Model_Data->site_title_fr = $request->site_title_fr;
            if ($FR_check == 1 && isset($request->site_title_fr) && $request->site_title_fr != '')
                $Model_Data->site_title_fr = $request->site_title_fr;

            $Model_Data->site_desc_en = $request->site_desc_en;
            $Model_Data->site_desc_fr = $request->site_desc_fr;
            if ($FR_check == 1 && isset($request->site_desc_fr) && $request->site_desc_fr != '')
                $Model_Data->site_desc_fr = $request->site_desc_fr;

            $Model_Data->site_keywords_en = $request->site_keywords_en;
            $Model_Data->site_keywords_fr = $request->site_keywords_fr;
            if ($FR_check == 1 && isset($request->site_keywords_fr) && $request->site_keywords_fr != '')
                $Model_Data->site_keywords_fr = $request->site_keywords_fr;

            $Model_Data->site_url = $request->site_url;

            $Model_Data->facebook_link = $request->facebook_link;
            $Model_Data->twitter_link = $request->twitter_link;
            $Model_Data->google_link = $request->google_link;
            $Model_Data->linkedin_link = $request->linkedin_link;
            $Model_Data->youtube_link = $request->youtube_link;
            $Model_Data->instagram_link = $request->instagram_link;
            $Model_Data->pinterest_link = $request->pinterest_link;
            $Model_Data->tumbler_link = $request->tumbler_link;
            $Model_Data->flicker_link = $request->flicker_link;
            $Model_Data->whatsapp_link = $request->whatsapp_link;

            $Model_Data->address_en_1 = $request->address_en_1;
            $Model_Data->address_fr_1 = $request->address_en_1;
            if ($FR_check == 1 && isset($request->address_fr_1) && $request->address_fr_1 != '')
                $Model_Data->address_fr_1 = $request->address_fr_1;

            $Model_Data->address_en_2 = $request->address_en_2;
            $Model_Data->address_fr_2 = $request->address_en_2;
            if ($FR_check == 1 && isset($request->address_fr_2) && $request->address_fr_2 != '')
                $Model_Data->address_fr_2 = $request->address_fr_2;

            $Model_Data->address_en_3 = $request->address_en_3;
            $Model_Data->address_fr_3 = $request->address_en_3;
            if ($FR_check == 1 && isset($request->address_fr_3) && $request->address_fr_3 != '')
                $Model_Data->address_fr_3 = $request->address_fr_3;

            $Model_Data->phone = $request->phone;
            $Model_Data->fax = $request->fax;
            $Model_Data->mobile = $request->mobile;
            $Model_Data->email = $request->email;

            $Model_Data->working_time_en = $request->working_time_en;
            $Model_Data->working_time_fr = $request->working_time_en;
            if ($FR_check == 1 && isset($request->working_time_fr) && $request->working_time_fr != '')
                $Model_Data->working_time_fr = $request->working_time_fr;

            if (isset($request->fr_lang))
            // $Model_Data->fr_lang = $request->fr_lang;
                if (isset($request->loader))
                    $Model_Data->loader = $request->loader;

            if (isset($request->site_status))
                $Model_Data->site_status = $request->site_status;

            if (isset($request->close_msg))
                $Model_Data->close_msg = $request->close_msg;

            $Model_Data->updated_by = Auth::user()->id;

            $Model_Data->save();

            Flash::success($this->msg_updated);
            return redirect(route($this->edit_route))->with('active_tab', $request->active_tab);
        }
        else {
            Flash::error($this->edit_permission_error_message);
            return redirect()->route($this->dashboard_route);
        }
    }

}
