<?php

namespace App\Http\Controllers\Backend\User;

use Auth;
use File;
use Flash;
use Response;
use Attribute;
use Datatables;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\ContactDetail;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\MainController as MainController;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends MainController {

    private $lang = "en";
    protected $uploads_root = "uploads";
    private $uploads_path = "uploads/users/";
    private $views_path = "users";
    private $home_route = "dashboard";
    private $view_route = "users.profile";
    private $msg_updated = "User details updated successfully.";
    private $msg_not_found = "User not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same User name";

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
    public function user_profile($rec_no) {

        // $Model_Data = User::where('rec_no', '=', $rec_no)->where('status', '=', 1)->first();


        $User = User::join('user_personals', 'users.id', '=', 'user_personals.user_id')
                ->select(
                        'users.id',
                        'users.user_type',
                        'users.email',
                        'users.phone',
                        'users.email_verified',
                        'users.phone_no_verified',
                        'user_personals.coachpic',
                        'user_personals.zip_code',
                        'user_personals.gender',
                        'user_personals.about_me'
                )
                ->where('users.id', $rec_no)
                ->first();

        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;
        $PageTitle = '';

        return view('frontend.profile', compact(
                        "User",
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show() {
        $Auth_User = Auth::user();
        $Model_Data = User::find($Auth_User->id);

        if (empty($Model_Data)) {
            Flash::error(__($this->msg_not_found));
            return redirect(route($this->home_route));
        }

        $lang = $this->lang;

        $seeker_id = $Model_Data->refer_id;

        $Model_Data = User::find($seeker_id); //



        $country_id = 0;

        $pr_id = City::select('pr_id')->where('id', $Model_Data->city_id)->first();

        if ($pr_id != null && !empty($pr_id)) {
            $pr_id = $pr_id->pr_id;

            $country_id = Province::select('country_id')->where('id', $pr_id)->first();

            $country_id = $country_id->country_id;
        }



        $Seeker_Languages = array();
        $Seeker_Languages_pluck = SeekerLanguage::leftjoin('employement_languages', 'seeker_languages.language_id', '=', 'employement_languages.id')
                ->leftjoin('seekers', 'seeker_languages.seeker_id', '=', 'seekers.id')
                ->select(['employement_languages.id'])
                ->where('seeker_languages.seeker_id', '=', $seeker_id)
                ->where('seeker_languages.status', '=', 1)
                ->where('employement_languages.status', '=', 1)
                ->get();
        foreach ($Seeker_Languages_pluck as $language) {
            $Seeker_Languages[] = $language->id;
        }

        $Seeker_Certifications = seekers_certification_data($seeker_id);

        $Seeker_Educations = seekers_education_data($seeker_id);

        $Seeker_Experiences = seekers_experience_data($seeker_id);

        $Seeker_Trainings = seekers_training_data($seeker_id);

        $Skills = skills_pluck_data($lang);
        $Seeker_Skills = seeker_skills_pluck_data($seeker_id, $lang);

        $Document_Types = DocumentType::where('type_for', '=', 'seeker')->where('status', '=', 1)->get();
        $Seeker_Documents = seekers_document_data($seeker_id, $Document_Types, false);
        //dd($Seeker_Documents);
        $SocialTypes = social_types_pluck_data($lang);
        $Seeker_Socials = seekers_social_data($seeker_id, $SocialTypes, false);

        $Settings = ContactDetail::find(1);

        return view($this->views_path . '.show', compact(
                        "Model_Data",
                        "cities_array",
                        "conditions_array",
                        "hours_array",
                        "periods_array",
                        "languages_array",
                        "Ethnicities",
                        "Seeker_Certifications",
                        "Document_Types",
                        "Seeker_Documents",
                        "Seeker_Educations",
                        "Seeker_Experiences",
                        "Skills",
                        "Seeker_Skills",
                        "SocialTypes",
                        "Seeker_Socials",
                        "Seeker_Trainings",
                        "Seeker_Languages",
                        "eligibilities_array",
                        "industries_array",
                        'provinces_array',
                        'countries_array',
                        "pr_id",
                        "country_id",
                        "Settings"
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        if ($request->edit_type == 'contact_update') {
            $request->validate([
                'name'  => 'required',
                'phone' => 'required',
            ]);
        }

        if ($request->edit_type == 'image_update') {
            $request->validate([
                'image'       => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048',
                'cover_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            ]);
        }


        $Auth_User = Auth::user();
        $Model_Data = User::find($Auth_User->id);
        $seeker_id = $Model_Data->refer_id;

        $id = $Model_Data->id;

        if ($request->edit_type == 'image_update') {
            $image = $Model_Data->photo;
            if (isset($request->image) && $request->file('image') != null) {
                $file_uploaded = $request->file('image');
                $image = date('YmdHis') . "1." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $image);

                if ($Model_Data->photo != "" && $Model_Data->photo != "user.png") {
                    File::delete($uploads_path . "/" . $Model_Data->photo);
                }
            }


            $cover_image = $Model_Data->cover_image;
            if (isset($request->cover_image) && $request->file('cover_image') != null) {
                $file_uploaded = $request->file('cover_image');
                $cover_image = date('YmdHis') . "2." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $cover_image);

                if ($Model_Data->cover_image != "" && $Model_Data->cover_image != "seeker_cover.png") {
                    File::delete($uploads_path . "/" . $Model_Data->cover_image);
                }
            }
        }

        if ($request->edit_type == 'contact_update') {
            $Model_Data->name = $request->name;
            $Model_Data->phone = $request->phone;
        }
        if ($request->edit_type == 'image_update') {
            $Model_Data->photo = $image;
        }
        $Model_Data->updated_by = $id;
        $Model_Data->save();

        $Model_Data = Seeker::find($seeker_id);

        if ($request->edit_type == 'contact_update') {
            $Model_Data->name_en = $request->name;
            $Model_Data->phone = $request->phone;
            $Model_Data->emergency_phone = $request->emergency_phone;
            $Model_Data->heading_en = $request->heading;
            $Model_Data->updated_by = $id;
            $Model_Data->save();
        }
        elseif ($request->edit_type == 'location_update') {
            $Model_Data->address_en = $request->address;
            $Model_Data->lat = $request->lat;
            $Model_Data->lng = $request->lng;
            $Model_Data->city_id = $request->city_id;
            $Model_Data->updated_by = $id;
            $Model_Data->save();
        }
        elseif ($request->edit_type == 'expectation_update') {
            // $Model_Data->ethnicity_id = $request->ethnicity_id;
            $Model_Data->eligibility_id = $request->eligibility_id;
            $Model_Data->industry_id = $request->industry_id;

            $Model_Data->expected_salary = $request->expected_salary;
            $Model_Data->salary_type = $request->salary_type;

            $Model_Data->updated_by = $id;
            $Model_Data->save();

            $language_ids = $request->get('language_id');
            if (!empty($language_ids) && $language_ids != null) {
                foreach ($language_ids as $key => $value) {
                    $_exist = 0;
                    $seeker_languages = SeekerLanguage::where(['seeker_id' => $seeker_id, 'language_id' => $value])->get();
                    foreach ($seeker_languages as $language) {
                        $seeker_language_id = $language->id;
                        if ($language->status == 0) {
                            $Model_Data = SeekerLanguage::find($seeker_language_id);
                            $Model_Data->status = 1;
                            $Model_Data->updated_by = $Auth_User->id;
                            $Model_Data->save();
                        }
                        $_exist = 1;
                    }
                    if ($_exist == 0) {
                        $Model_Data = new SeekerLanguage();
                        $Model_Data->seeker_id = $seeker_id;
                        $Model_Data->language_id = $value;
                        $Model_Data->status = 1;
                        $Model_Data->created_by = $Auth_User->id;
                        $Model_Data->save();
                    }
                }
            }
        }
        elseif ($request->edit_type == 'work_condition_update') {
            $Model_Data->period_id = $request->period_id;
            $Model_Data->condition_id = $request->condition_id;
            $Model_Data->hour_id = $request->hour_id;

            $Model_Data->updated_by = $id;
            $Model_Data->save();
        }
        elseif ($request->edit_type == 'about_update') {
            $Model_Data->overview_en = $request->overview;

            $Model_Data->updated_by = $id;
            $Model_Data->save();
        }
        elseif ($request->edit_type == 'image_update') {

            if ($image != '')
                $Model_Data->photo = $image;
            $Model_Data->photo_src = 0;

            if ($cover_image != '')
                $Model_Data->cover_image = $cover_image;
            $Model_Data->cover_src = 0;

            $Model_Data->updated_by = $id;
            $Model_Data->save();
        } {

            $log_array = array();
            $log_array['title'] = 'Updated Basic Information';
            $log_array['description'] = 'Seeker has updated Basic Information';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(__($this->msg_updated));
        return redirect(route($this->view_route));
    }

    /**
     * Show the form for change password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword() {
        $Settings = ContactDetail::find(1);

        return view($this->views_path . '.password', compact("Settings"));
    }

    /**
     * Update the password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request) {
        $request->validate([
            'current_password'     => ['required', new MatchOldPassword],
            'new_password'         => ['required', PasswordRule::min(6)->mixedCase()->numbers()],
            'new_confirm_password' => ['same:new_password'],
        ]);

        $Auth_User = Auth::user();
        $id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        User::find($id)->update(['password' => \Hash::make($request->new_password), 'updated_by' => $id]);
        {

            $log_array = array();
            $log_array['title'] = 'Updated Password';
            $log_array['description'] = 'Seeker has updated password';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Password updated successfully.'));
        return redirect(route($this->view_route));
    }

    private function is_not_authorized($id, $Auth_User) {
        $user_type = $Auth_User->user_type;

        $bool = 1;
        if ($id == $Auth_User->id) {
            $bool = 0;
        }
        return $bool;
    }

    public function upload_document(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $i = 0;
        $Document_Types = DocumentType::where('type_for', '=', 'seeker')->where('status', '=', 1)->get();
        foreach ($Document_Types as $Document_Type) {
            $type_id = $Document_Type->id;
            $field_name = 'file_' . $type_id;
            $field_expiry = 'expiry_date_' . $type_id;
            $field_has_expiry = 'has_expiry_' . $type_id;
            $request->validate([
                $field_name => 'max:10000|mimes:doc,docx,xls,xlsx,csv,ppt,pptx,txt,pdf,jpeg,png,jpg,gif,svg'
            ]);

            $add_update = 0;

            $expiry_date = '';
            if (isset($request->$field_has_expiry) && !empty($request->$field_has_expiry)) {
                if (isset($request->$field_expiry) && !empty($request->$field_expiry)) {
                    $add_update = 1;
                    $expiry_date = (strtotime($request->$field_expiry) + ((24 * 60 * 60) - 1));
                }
                else {
                    $expiry_date = 0;
                }
            }
            else {
                $expiry_date = 0;
                $add_update = 1;
            }

            $image = '';
            if (isset($request->$field_name) && $request->file($field_name) != null) {
                $i++;
                $add_update = 1;
                $file_uploaded = $request->file($field_name);
                $image = date('YmdHis') . $i . "." . $file_uploaded->getClientOriginalExtension();

                $this->create_uploads_directory();

                $uploads_path = $this->uploads_path;
                $file_uploaded->move($uploads_path, $image);
            }

            //echo "$type_id - $expiry_date - $image";

            if ($add_update == 1) {
                $exists = 0;
                $Records = SeekerDocument::where('seeker_id', '=', $seeker_id)->where('type_id', '=', $type_id)->where('status', '=', 1)->get();
                foreach ($Records as $Record) {
                    $exists = 1;
                    if ($image != '') {
                        File::delete($uploads_path . "/" . $Record->file_name);
                    }
                }
                if ($exists == 1) {
                    $Model_Data = SeekerDocument::find($Record->id);
                    $status = 1;
                    if ($image == '') {
                        $image = $Model_Data->file_name;
                    }
                    if ($expiry_date == 0 && $image == '') {
                        $status = 0;
                    }
                    if ($image != '')
                        $Model_Data->file_name = $image;
                    // if ($expiry_date != '')
                    $Model_Data->expiry_date = $expiry_date;
                    $Model_Data->document_src = 0;
                    $Model_Data->status = $status;
                    $Model_Data->updated_by = $Auth_User_id;
                    $Model_Data->save();
                }
                elseif ($exists == 0) {
                    if ($expiry_date == 0 && empty($image)) {
                        // return redirect(route($this->view_route));
                    }
                    else {
                        $Model_Data = new SeekerDocument();
                        $Model_Data->seeker_id = $seeker_id;
                        $Model_Data->type_id = $type_id;
                        if ($image != '')
                            $Model_Data->file_name = $image;
                        // if ($expiry_date != '')
                        $Model_Data->expiry_date = $expiry_date;
                        $Model_Data->document_src = 0;
                        $Model_Data->status = 1;
                        $Model_Data->created_by = $Auth_User_id;
                        $Model_Data->save();
                    }
                }
            }
        }
        //exit;
        {

            $log_array = array();
            $log_array['title'] = 'Documents Uploaded';
            $log_array['description'] = 'Seeker has Uploaded Documents';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Documents uploaded successfully.'));
        return redirect(route($this->view_route));
    }

    public function update_social(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $SocialTypes = social_types_pluck_data();
        $Seeker_Socials = array();
        foreach ($SocialTypes as $type_id => $SocialType) {
            $field_name = 'link_' . $type_id;
            $_exist = 0;
            $link = '';
            if (isset($request->$field_name))
                $link = trim($request->$field_name);

            $SeekerSocials = SeekerSocial::where(['seeker_id' => $seeker_id, 'type_id' => $type_id, 'status' => 1])->get();
            foreach ($SeekerSocials as $SeekerSocial) {
                $Model_Data = SeekerSocial::find($SeekerSocial->id);
                if (!empty($Model_Data)) {
                    if (empty($link)) {
                        $Model_Data->delete();
                    }
                    else {
                        $Model_Data->link = $link;
                        $Model_Data->updated_by = $Auth_User_id;
                        $Model_Data->save();
                    }
                }
                $_exist = 1;
            }
            if (!empty($link)) {

                if ($_exist == 0) {
                    $Model_Data = new SeekerSocial();
                    $Model_Data->seeker_id = $seeker_id;
                    $Model_Data->type_id = $type_id;
                    $Model_Data->link = $link;
                    $Model_Data->created_by = $Auth_User_id;
                    $Model_Data->save();
                }
            }
        } {

            $log_array = array();
            $log_array['title'] = 'Social Account Updated';
            $log_array['description'] = 'Seeker has Updated Social Account';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Social Links updated successfully.'));
        return redirect(route($this->view_route));
    }

    public function add_skill(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $skill_ids = $request->get('skill_ids');

        $records = SeekerSkill::where(['seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($records as $record) {
            $record_id = $record->id;
            $Model_Data = SeekerSkill::find($record_id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();
            $skill = "skill";
        }

        if (isset($skill_ids)) {

            foreach ($skill_ids as $skill_id) {
                $_exist = 0;

                $SeekerSkills = SeekerSkill::where(['seeker_id' => $seeker_id, 'skill_id' => $skill_id])->get();
                foreach ($SeekerSkills as $SeekerSkill) {
                    if ($SeekerSkill->status == 0) {
                        $Model_Data = SeekerSkill::find($SeekerSkill->id);
                        $Model_Data->status = 1;
                        $Model_Data->updated_by = $Auth_User_id;
                        $Model_Data->save();
                    }
                    $_exist = 1;
                }

                if ($_exist == 0) {
                    $Model_Data = new SeekerSkill();
                    $Model_Data->seeker_id = $seeker_id;
                    $Model_Data->skill_id = $skill_id;
                    $Model_Data->created_by = $Auth_User_id;
                    $Model_Data->save();
                }
            }
        } {

            $log_array = array();
            $log_array['title'] = 'Skills Added';
            $log_array['description'] = 'Seeker has Added New Skills';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Skills updated successfully.'));
        return redirect(route($this->view_route));
    }

    public function add_education(Request $request) {
        $request->validate([
            'title'     => 'required',
            'institute' => 'required',
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $Model_Data = new SeekerEducation();
        $Model_Data->seeker_id = $seeker_id;
        $Model_Data->title = trim($request->title);
        $Model_Data->major = 'major';
        $Model_Data->institute = trim($request->institute);
        $Model_Data->country = 'country';
        $Model_Data->grade = 'grade';
        $Model_Data->from_date = strtotime($request->from_date);
        $Model_Data->to_date = strtotime($request->to_date);
        $Model_Data->created_by = $Auth_User_id;
        $Model_Data->save();
        {

            $log_array = array();
            $log_array['title'] = 'Education Added';
            $log_array['description'] = 'Seeker has Added Education';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Education Added successfully.'));
        return redirect(route($this->view_route));
    }

    public function add_experience(Request $request) {
        //dd($request);

        $request->validate([
            'designation'  => 'required',
            'organization' => 'required',
            'from_date'    => 'required',
                /* 'to_date' => 'required', */
        ]);

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $Model_Data = new SeekerExperience();
        $Model_Data->seeker_id = $seeker_id;
        $Model_Data->designation = trim($request->designation);
        $Model_Data->organization = trim($request->organization);
        $Model_Data->responsibilities = trim($request->responsibilities);
        $Model_Data->from_date = strtotime($request->from_date);
        if (isset($request->to_date))
            $Model_Data->to_date = strtotime($request->to_date);
        $Model_Data->created_by = $Auth_User_id;
        $Model_Data->save();
        {

            $log_array = array();
            $log_array['title'] = 'Experience Added';
            $log_array['description'] = 'Seeker has Added Experience';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Experience Added successfully.'));
        return redirect(route($this->view_route));
    }

    public function add_training(Request $request) {

        $request->validate([
            'title'     => 'required',
            'institute' => 'required',
            'from_date' => 'required',
            'to_date'   => 'required',
        ]);

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $Model_Data = new SeekerTraining();
        $Model_Data->seeker_id = $seeker_id;
        $Model_Data->title = trim($request->title);
        $Model_Data->institute = trim($request->institute);
        $Model_Data->country = 'country';
        $Model_Data->from_date = strtotime($request->from_date);
        $Model_Data->to_date = strtotime($request->to_date);
        $Model_Data->created_by = $Auth_User_id;
        $Model_Data->save();
        {

            $log_array = array();
            $log_array['title'] = 'Training Added';
            $log_array['description'] = 'Seeker has Added Training';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Trainings Added successfully.'));
        return redirect(route($this->view_route));
    }

    public function add_certification(Request $request) {
        $request->validate([
            'title'     => 'required',
            'institute' => 'required',
            'from_date' => 'required',
                /* 'to_date' => 'required', */
        ]);

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $Model_Data = new SeekerCertification();
        $Model_Data->seeker_id = $seeker_id;
        $Model_Data->title = trim($request->title);
        $Model_Data->institute = trim($request->institute);
        $Model_Data->country = 'country';
        $Model_Data->from_date = strtotime($request->from_date);
        if (isset($request->to_date))
            $Model_Data->to_date = strtotime($request->to_date);
        $Model_Data->created_by = $Auth_User_id;
        $Model_Data->save();
        {

            $log_array = array();
            $log_array['title'] = 'Certification Added';
            $log_array['description'] = 'Seeker has Added Certification';

            seeker_logs(Auth::user(), $log_array);
        }

        Flash::success(translate_it('Certifications Added successfully.'));
        return redirect(route($this->view_route));
    }

    public function update_education(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->edit_id;
        $SeekerDocuments = SeekerEducation::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerDocuments as $SeekerDocument) {
            $Model_Data = SeekerEducation::find($id);
            $Model_Data->title = trim($request->title);
            $Model_Data->major = 'major';
            $Model_Data->institute = trim($request->institute);
            $Model_Data->country = 'country';
            $Model_Data->grade = 'grade';
            $Model_Data->from_date = strtotime($request->from_date);
            $Model_Data->to_date = strtotime($request->to_date);
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Education Updated';
            $log_array['description'] = 'Seeker has Updated Education';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function update_experience(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->edit_id;
        $SeekerDocuments = SeekerExperience::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerDocuments as $SeekerDocument) {
            $Model_Data = SeekerExperience::find($id);
            $Model_Data->designation = trim($request->title);
            $Model_Data->organization = trim($request->institute);
            $Model_Data->responsibilities = trim($request->responsibilities);
            $Model_Data->from_date = strtotime($request->from_date);
            if (isset($request->to_date))
                $Model_Data->to_date = strtotime($request->to_date);
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Experience Updated';
            $log_array['description'] = 'Seeker has Updated Experience';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function update_training(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->edit_id;
        $SeekerDocuments = SeekerTraining::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerDocuments as $SeekerDocument) {
            $Model_Data = SeekerTraining::find($id);
            $Model_Data->title = trim($request->title);
            $Model_Data->institute = trim($request->institute);
            $Model_Data->country = 'country';
            $Model_Data->from_date = strtotime($request->from_date);
            $Model_Data->to_date = strtotime($request->to_date);
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Training Updated';
            $log_array['description'] = 'Seeker has Updated Training';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function update_certification(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->edit_id;
        $SeekerDocuments = SeekerCertification::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerDocuments as $SeekerDocument) {
            $Model_Data = SeekerCertification::find($id);
            $Model_Data->title = trim($request->title);
            $Model_Data->institute = trim($request->institute);
            $Model_Data->country = 'country';
            $Model_Data->from_date = strtotime($request->from_date);
            if (isset($request->to_date))
                $Model_Data->to_date = strtotime($request->to_date);
            else
                $Model_Data->to_date = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Certification Updated';
            $log_array['description'] = 'Seeker has Updated Certification';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_document(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->del_id;
        $SeekerDocuments = SeekerDocument::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerDocuments as $SeekerDocument) {
            $Model_Data = SeekerDocument::find($id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Document Removed';
            $log_array['description'] = 'Seeker has Removed Document';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_cover_image(Request $request) {

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $Seeker = Seeker::where('id', $seeker_id)->first();

        if ($Seeker->cover_image == 'seeker_cover.png') {
            $bool = true;
        }
        else {
            $uploads_path = $this->uploads_path;
            if ($Seeker->cover_image != "" && $Seeker->cover_image != "seeker_cover.png") {
                File::delete($uploads_path . "/" . $Seeker->cover_image);
            }
            $Seeker->cover_image = 'seeker_cover.png';
            $Seeker->updated_by = $Auth_User_id;
            $Seeker->save();
            $bool = true;
        }
        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Cover Image Removed';
            $log_array['description'] = 'Seeker has Removed Cover Image';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_profile_image(Request $request) {
        // dd($request->all());
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $Seeker = Seeker::where('id', $seeker_id)->first();
        $Model_Data = User::find($Auth_User_id);

        if ($Seeker->photo == 'user.png') {
            $bool = true;
        }
        else {
            $uploads_path = $this->uploads_path;
            if ($Seeker->photo != "" && $Seeker->photo != "user.png") {
                File::delete($uploads_path . "/" . $Seeker->photo);
            }
            $Seeker->photo = 'user.png';
            $Model_Data->photo = 'user.png';
            $Seeker->updated_by = $Auth_User_id;
            $Seeker->save();
            $Model_Data->save();
            $bool = true;
        }
        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Profile Image Removed';
            $log_array['description'] = 'Seeker has Removed Profile Image';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_skill(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->del_id;
        $SeekerSkills = SeekerSkill::where(['seeker_id' => $seeker_id, 'skill_id' => $id, 'status' => 1])->get();
        foreach ($SeekerSkills as $SeekerSkill) {
            $Model_Data = SeekerSkill::find($SeekerSkill->id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Skill Removed';
            $log_array['description'] = 'Seeker has Removed Skill';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_education(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->del_id;
        $SeekerEducations = SeekerEducation::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerEducations as $SeekerEducation) {
            $Model_Data = SeekerEducation::find($id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Education Removed';
            $log_array['description'] = 'Seeker has Removed Education';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_experience(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->del_id;
        $SeekerExperiences = SeekerExperience::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerExperiences as $SeekerExperience) {
            $Model_Data = SeekerExperience::find($id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Experience Removed';
            $log_array['description'] = 'Seeker has Removed Experience';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_training(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->del_id;
        $SeekerEducations = SeekerTraining::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerEducations as $SeekerEducation) {
            $Model_Data = SeekerTraining::find($id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Training Removed';
            $log_array['description'] = 'Seeker has Removed Training';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function remove_certification(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $bool = false;
        $id = $request->del_id;
        $SeekerEducations = SeekerCertification::where(['id' => $id, 'seeker_id' => $seeker_id, 'status' => 1])->get();
        foreach ($SeekerEducations as $SeekerEducation) {
            $Model_Data = SeekerCertification::find($id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $bool = true;
        }

        return $bool;
        {

            $log_array = array();
            $log_array['title'] = 'Certification Removed';
            $log_array['description'] = 'Seeker has Removed Certification';

            seeker_logs(Auth::user(), $log_array);
        }
    }

    public function seeker_availability(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $seeker_availability = $request->availability;

        $seeker = Seeker::find($seeker_id);
        if (!empty($seeker)) {
            $seeker->availability = $seeker_availability;
            $seeker->save();

            $Auth_User->availability = $seeker_availability;
            $Auth_User->save();
        }
        if ($request->availability == 1) {
            return 0;
        }
        else {
            return 1;
        }
    }

    public function save_city(Request $request) {
        $Auth_User = Auth::User();
        $city_id = 0;
        $pr_id = 0;

        $state = substr($request->state, 0, strrpos($request->state, ' '));

        $state = substr($state, 0, strrpos($state, ' '));

        $state = Province::where('code', $state)->first();

        $pr_id = $state->id;

        $city = City::select('id')->where('name_en', $request->city)->first();
        if (!empty($city)) {
            $city_id = $city->id;
        }
        else {
            $city = new City();
            $city->employer_id = 0;
            $city->pr_id = $pr_id;

            $city->name_en = $request->city;
            $city->name_fr = $request->city;
            $city->created_by = $Auth_User->id;

            $city->save();

            $city_id = $city->id;
        }

        return $city_id;
    }

}
