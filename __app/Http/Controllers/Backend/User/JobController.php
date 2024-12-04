<?php

namespace App\Http\Controllers\Backend\User;

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
use App\Models\Setting;
use App\Models\Seeker;
use App\Models\SeekerCertification;
use App\Models\SeekerDocument;
use App\Models\SeekerEducation;
use App\Models\SeekerExperience;
use App\Models\SeekerSocial;
use App\Models\SeekerSkill;
use App\Models\SeekerTraining;
use App\Models\SeekerLanguage;
use App\Models\DocumentType;
use App\Models\Skill;
use App\Models\SocialType;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\Employer;
use App\Models\EmployerCategory;
use App\Models\EmployerBenefit;
use App\Models\EmployerSocial;
use App\Models\EmployerLocation;
use App\Models\Job;
use App\Models\JobBenefit;
use App\Models\JobGroup;
use App\Models\JobLanguage;
use App\Models\JobSkill;
use App\Models\JobVisit;
use App\Models\SavedJob;
use App\Models\JobOffer;
use App\Models\JobApplicant;
use App\Models\Match;
use App\Models\MatchOffer;
use App\Models\JobHistory;

class JobController extends MainController {

    private $lang = "en";
    private $uploads_root = "uploads";
    private $uploads_path = "uploads/users/";
    private $views_path = "users";
    private $home_route = "dashboard";
    private $view_route = "users.profile";
    private $msg_updated = "User details updated successfully.";
    private $msg_not_found = "User not found. Please try again.";
    private $msg_required = "Please fill all required fields.";
    private $msg_exists = "Record Already Exists with same User name";

    public function JobFavorite(Request $request, $job_id) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $message = '';

        $exists = 0;
        $SavedJobs = SavedJob::where(['seeker_id' => $seeker_id, 'job_id' => $job_id, 'status' => 1])->get();
        foreach ($SavedJobs as $SavedJob) {
            $id = $SavedJob->id;
            $Model_Data = SavedJob::find($id);
            $Model_Data->status = 0;
            $Model_Data->updated_by = $Auth_User_id;
            $Model_Data->save();

            $exists = 1;

            $message = '<button class="save_job btn pxp-single-job-save-btn btn-light add_favorite_job" data-id="' . $job_id . '">';
            $message .= '<span class="fa fa-heart-o"></span>';
            $message .= '</button>';
        }

        if ($exists == 0) {
            $save_time = time();
            $Model_Data = new SavedJob();
            $Model_Data->seeker_id = $seeker_id;
            $Model_Data->job_id = $job_id;
            $Model_Data->save_time = $save_time;
            $Model_Data->status = 1;
            $Model_Data->created_by = $Auth_User_id;
            $Model_Data->save();

            $message = '<button class="save_job btn pxp-single-job-save-btn btn_save_job btn-primary btn-primary-save" data-toggle="' . $job_id . '">';
            $message .= '<span class="fa fa-heart-o"></span>';
            $message .= '</button>';
        }

        return $message;
        {

            $log_array = array();
            $log_array['title'] = 'Add To Favorite';
            $log_array['description'] = 'Seeker has Add Job To Favorite Listing';

            seeker_logs($seeker_id, $log_array);
        }
    }

    public function favorite_jobs(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $jobs = SavedJob::leftjoin('jobs', 'saved_jobs.job_id', '=', 'jobs.id')
                ->select('jobs.*')
                ->where('saved_jobs.seeker_id', '=', $seeker_id)
                ->where('saved_jobs.status', '!=', 0)
                ->where('jobs.status', 1)
                ->orderby('saved_jobs.id', 'desc');
        $jobs = $jobs->paginate(env('SEEKER_DASHBOARD_PAGINATION'));

        if ($request->ajax()) {
            $str = $this->common_jobs_sidebar_listing($jobs, $list_view = "favorite_job");

            if (!empty($str)) {
                return $str;
            }
            else {
                return $str = 0;
            }
        }
        else {
            $cities_array = cities_pluck_data();

            $hours_array = emp_hours_pluck_data();

            $employers_array = employers_pluck_data();

            $Settings = ContactDetail::find(1);

            // code for job Details
            $job_details = "";
            $employer = "";
            $job_id = "";

            $job_details = SavedJob::leftjoin('jobs', 'saved_jobs.job_id', '=', 'jobs.id')
                    ->select('jobs.*', 'saved_jobs.job_id')
                    ->where('saved_jobs.seeker_id', '=', $seeker_id)
                    ->where('saved_jobs.status', '!=', 0)
                    ->where('jobs.status', 1)
                    ->orderby('saved_jobs.id', 'desc')
                    ->first();

            if (!empty($job_details)) {
                $id = $job_details->id;
                $employer_id = $job_details->employer_id;
                $employer = Employer::find($employer_id);

                $job_id = $job_details->id;
            }


            $listing = 'favorites';

            return view('users.favorite_jobs.listing', compact(
                            'Settings',
                            'jobs',
                            'job_details',
                            'cities_array',
                            'hours_array',
                            'employers_array',
                            'employer',
                            'job_id',
                            'job_details',
                            'listing'
            ));
        }
    }

    public function favoritejobsDetailsByAjax(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->job_id;

        $job_details = Job::where('id', '=', $id)->where('status', '=', 1)->first();
        if (empty($job_details) || $job_details == null) {
            return '<div class="mt-5"><h4>' . translate_it('Record Not Available') . '</h4></div>';
        }

        $Record = SavedJob::where('job_id', $id)
                ->where('seeker_id', $seeker_id)
                ->where('status', 1)
                ->first();

        if (empty($Record) || $Record == null) {
            return '<div class="mt-5"><h4>' . translate_it('No Favorite Job Available') . '</h4></div>';
        }


        $this->common_job_listing_details($job_details, 'favorites', $Record);
    }

    public function applied_jobs(Request $request) {

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $application_type = "all";
        if (!empty($request->application_type) && $request->application_type != '' && $request->application_type != 'all') {
            $application_type = $request->application_type;
            if ($application_type == 'pending') {
                $status = '1';
            }
            if ($application_type == 'viewed') {
                $status = '2';
            }
            if ($application_type == 'shortlisted') {
                $status = '3';
            }
            if ($application_type == 'declined') {
                $status = '4';
            }
            if ($application_type == 'accepted') {
                $status = '5';
            }

            $jobs = JobApplicant::leftjoin('jobs', 'job_applicants.job_id', '=', 'jobs.id')
                    ->select('jobs.*', 'job_applicants.id as application_id', 'job_applicants.status as applicant_status')
                    ->where('job_applicants.seeker_id', '=', $seeker_id)
                    ->where('job_applicants.status', '=', $status)
                    ->where('jobs.status', 1)
                    ->orderby('job_applicants.id', 'desc');
        }
        else {

            $application_type = "all";
            $status = "1";
            $jobs = JobApplicant::leftjoin('jobs', 'job_applicants.job_id', '=', 'jobs.id')
                    ->select('jobs.*', 'job_applicants.id as application_id', 'job_applicants.status as applicant_status')
                    ->where('job_applicants.seeker_id', '=', $seeker_id)
                    ->where('job_applicants.status', '>=', $status)
                    ->where('jobs.status', 1)
                    ->orderby('job_applicants.id', 'desc');
        }





        $jobs = $jobs->paginate(env('SEEKER_DASHBOARD_PAGINATION'));

        if ($request->ajax()) {
            $str = $this->common_jobs_sidebar_listing($jobs, $list_view = "apply_job");

            if (!empty($str)) {
                return $str;
            }
            else {
                return $str = 0;
            }
        }
        else {
            $cities_array = cities_pluck_data();

            $hours_array = emp_hours_pluck_data();

            $employers_array = employers_pluck_data();

            $Settings = ContactDetail::find(1);

            // code for job Details
            $Applicant_Record = "";
            $job_details = "";
            $employer = "";
            $job_id = "";

            if (!empty($request->application_type) && $request->application_type != '' && $request->application_type != 'all') {
                $application_type = $request->application_type;
                if ($application_type == 'pending') {
                    $status = '1';
                }
                if ($application_type == 'viewed') {
                    $status = '2';
                }
                if ($application_type == 'shortlisted') {
                    $status = '3';
                }
                if ($application_type == 'declined') {
                    $status = '4';
                }
                if ($application_type == 'accepted') {
                    $status = '5';
                }
                $job_details = JobApplicant::leftjoin('jobs', 'job_applicants.job_id', '=', 'jobs.id')
                        ->select('jobs.*', 'job_applicants.id as application_id', 'job_applicants.id as applicant_status')
                        ->where('job_applicants.seeker_id', '=', $seeker_id)
                        ->where('job_applicants.status', '=', $status)
                        ->where('jobs.status', 1)
                        ->orderby('job_applicants.id', 'desc')
                        ->first();
            }
            else {
                $application_type = "all";
                $status = "1";
                $job_details = JobApplicant::leftjoin('jobs', 'job_applicants.job_id', '=', 'jobs.id')
                        ->select('jobs.*', 'job_applicants.id as application_id', 'job_applicants.id as applicant_status')
                        ->where('job_applicants.seeker_id', '=', $seeker_id)
                        ->where('job_applicants.status', '>=', $status)
                        ->where('jobs.status', 1)
                        ->orderby('job_applicants.id', 'desc')
                        ->first();
            }


            if (!empty($job_details)) {
                $Applicant_Record = JobApplicant::find($job_details->application_id);

                $id = $job_details->id;
                $employer_id = $job_details->employer_id;
                $employer = Employer::find($employer_id);

                $job_id = $job_details->id;
            }

            $listing = 'applications';

            return view('users.job_applications.listing', compact(
                            'Settings',
                            'jobs',
                            'job_details',
                            'Applicant_Record',
                            'cities_array',
                            'hours_array',
                            'employers_array',
                            'employer',
                            'job_id',
                            'application_type',
                            'listing'
            ));
        }
    }

    public function applicantjobsDetailsByAjax(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->job_id;

        $job_details = Job::where('id', '=', $id)->where('status', '=', 1)->first();
        if (empty($job_details) || $job_details == null) {
            return '<div class="mt-5"><h4>' . translate_it('Record Not Available') . '</h4></div>';
        }

        $Record = JobApplicant::where('job_id', $id)
                ->where('seeker_id', $seeker_id)
                ->where('status', '>=', 1)
                ->first();

        if (empty($Record) || $Record == null) {
            return '<div class="mt-5"><h4>' . translate_it('No Applicant Available') . '</h4></div>';
        }


        $this->common_job_listing_details($job_details, 'applications', $Record);
    }

    public function offer_jobs(Request $request) {

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $offer_type = 'all';
        if (!empty($request->offer_type) && $request->offer_type != '' && $request->offer_type != 'all') {
            $offer_type = $request->offer_type;

            if ($offer_type == 'offered') {
                $status = '2';
            }
            if ($offer_type == 'declined') {
                $status = '3';
            }
            if ($offer_type == 'accepted') {
                $status = '4';
            }
            $jobs = JobOffer::leftjoin('jobs', 'job_offers.job_id', '=', 'jobs.id')
                    ->select('jobs.*', 'job_offers.id as application_id', 'job_offers.status as offer_status')
                    ->where('job_offers.seeker_id', '=', $seeker_id)
                    ->where('job_offers.status', '=', $status)
                    ->where('jobs.status', 1)
                    ->orderby('job_offers.id', 'desc');
        }
        else {

            $offer_type == 'all';
            $status = '2';
            $jobs = JobOffer::leftjoin('jobs', 'job_offers.job_id', '=', 'jobs.id')
                    ->select('jobs.*', 'job_offers.id as application_id', 'job_offers.status as offer_status')
                    ->where('job_offers.seeker_id', '=', $seeker_id)
                    ->where('job_offers.status', '>=', $status)
                    ->where('jobs.status', 1)
                    ->orderby('job_offers.id', 'desc');
        }



        $jobs = $jobs->paginate(env('SEEKER_DASHBOARD_PAGINATION'));

        if ($request->ajax()) {
            $str = $this->common_jobs_sidebar_listing($jobs, $list_view = 'offer_job');

            if (!empty($str)) {
                return $str;
            }
            else {
                return $str = 0;
            }
        }
        else {
            $cities_array = cities_pluck_data();

            $hours_array = emp_hours_pluck_data();

            $employers_array = employers_pluck_data();

            $Settings = ContactDetail::find(1);

            // code for job Details
            $Applicant_Record = "";
            $job_details = "";
            $employer = "";
            $job_id = "";

            if (!empty($request->offer_type) && $request->offer_type != '' && $request->offer_type != 'all') {
                $offer_type = $request->offer_type;

                if ($offer_type == 'offered') {
                    $status = '2';
                }
                if ($offer_type == 'declined') {
                    $status = '3';
                }
                if ($offer_type == 'accepted') {
                    $status = '4';
                }
                $job_details = JobOffer::leftjoin('jobs', 'job_offers.job_id', '=', 'jobs.id')
                        ->select('jobs.*', 'job_offers.id as application_id', 'job_offers.status as offer_status')
                        ->where('job_offers.seeker_id', '=', $seeker_id)
                        ->where('job_offers.status', '=', $status)
                        ->where('jobs.status', 1)
                        ->orderby('job_offers.id', 'desc')
                        ->first();
            }
            else {

                $offer_type == 'all';
                $status = '2';
                $job_details = JobOffer::leftjoin('jobs', 'job_offers.job_id', '=', 'jobs.id')
                        ->select('jobs.*', 'job_offers.id as application_id', 'job_offers.status as offer_status')
                        ->where('job_offers.seeker_id', '=', $seeker_id)
                        ->where('job_offers.status', '>=', $status)
                        ->where('jobs.status', 1)
                        ->orderby('job_offers.id', 'desc')
                        ->first();
            }


            if (!empty($job_details)) {
                $Applicant_Record = JobOffer::find($job_details->application_id);

                $id = $job_details->id;
                $employer_id = $job_details->employer_id;
                $employer = Employer::find($employer_id);

                $job_id = $job_details->id;
            }
            // dd($request->offer_type);

            $listing = 'offers';

            return view('users.job_offers.listing', compact(
                            'Settings',
                            'jobs',
                            'job_details',
                            'Applicant_Record',
                            'cities_array',
                            'hours_array',
                            'employers_array',
                            'employer',
                            'job_id',
                            'offer_type',
                            'listing'
            ));
        }
    }

    public function offerdjobsDetailsByAjax(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->job_id;

        $job_details = Job::where('id', '=', $id)->where('status', '=', 1)->first();
        if (empty($job_details) || $job_details == null) {
            return '<div class="mt-5"><h4>' . translate_it('Record Not Available') . '</h4></div>';
        }

        $Record = JobOffer::where('job_id', $id)
                ->where('seeker_id', $seeker_id)
                ->where('status', '>=', 2)
                ->where('status', '<=', 4)
                ->first();

        if (empty($Record) || $Record == null) {
            return '<div class="mt-5"><h4>' . translate_it('No Offered Job Available') . '</h4></div>';
        }


        $this->common_job_listing_details($job_details, 'offers', $Record);
    }

    public function active_jobs(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $job_histories = JobHistory::where([
                    ['job_histories.seeker_id', $seeker_id],
                    ['job_histories.status', '=', 1],
        ]);

        $job_histories = $job_histories->select('job_histories.*');
        $job_histories = $job_histories->orderby('job_histories.id', 'desc');
        $job_histories = $job_histories->paginate(env('SEEKER_DASHBOARD_PAGINATION'));

        if ($request->ajax()) {

            $str = $this->common_active_job_listing_sidebar($job_histories, $list_view = "active_job");
            if (!empty($str)) {
                return $str;
            }
            else {
                return $str = 0;
            }
        }
        else {
            $cities_array = cities_pluck_data();

            $conditions_array = emp_conditions_pluck_data();

            $hours_array = emp_hours_pluck_data();

            $employers_array = employers_pluck_data();

            $Settings = ContactDetail::find(1);

            // code for job Details


            $job_histories_details = JobHistory::where([
                        ['job_histories.seeker_id', $seeker_id],
                        ['job_histories.status', '=', 1],
            ]);
            $job_histories_details = $job_histories_details->select('job_histories.*');
            $job_histories_details = $job_histories_details->orderby('job_histories.id', 'desc');
            $job_histories_details = $job_histories_details->first();

            return view('users.active_jobs.listing', compact(
                            'Settings',
                            'job_histories',
                            'job_histories_details',
                            'cities_array',
                            'hours_array',
                            'employers_array',
                            'conditions_array'
            ));
        }
    }

    public function activejobsDetailsByAjax(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->job_id;
        $job_history_type = $request->job_history_type;
        $job_history_id = $request->job_history_id;
        $job_details = Job::where('id', '=', $id)->where('status', '=', 1)->first();
        if (empty($job_details) || $job_details == null) {
            return '<div class="mt-5"><h4>' . translate_it('Record Not Available') . '</h4></div>';
        }

        $Record = JobHistory::where('id', $job_history_id)
                ->where('seeker_id', $seeker_id)
                ->where('type', $job_history_type)
                ->where('status', '=', 1)
                ->first();

        if (empty($Record) || $Record == null) {
            return '<div class="mt-5"><h4>' . translate_it('No Active Job Available') . '</h4></div>';
        }

        $this->common_job_listing_details($job_details, 'active', $Record);
    }

    public function common_active_job_listing_sidebar($job_histories, $list_view = '') {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        foreach ($job_histories as $job_history) {
            $type = $job_history->type;
            $type_id = $job_history->type_id;

            if ($type != 'open_offered_accepted') {
                $jobs = job_data($type, $type_id, $seeker_id);
                $employers_array = employers_pluck_data();

                $cities_array = cities_pluck_data();

                $hours_array = emp_hours_pluck_data();

                $str = array();
                foreach ($jobs as $job) {
                    $class = "";

                    $rec_str = '';

                    $rec_str .= '<div class="pxp-jobs-card-4 pxp-has-border ' . $class . ' list_job pxp-single-job-side-panel" data-toggle="' . $job->id . '" data-type="' . $job_history->type . '" data-id="' . $job_history->id . '">';

                    $rec_str .= '<div class="pxp-single-job-side-company">';

                    $rec_str .= '<div class="pxp-single-job-side-company-logo pxp-cover" style="background-image:  url(' . asset_url("images/company-logo-1.png") . ');"></div>';

                    $rec_str .= '<div class="pxp-single-job-side-company-profile ">';
                    $rec_str .= '<div class="pxp-single-job-side-company-name">' . get_lang_field_data($job, 'title') . '</div>';
                    $rec_str .= '<a href="#">' . $employers_array[$job->employer_id] . '</a>';
                    $rec_str .= '</div>';

                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label">';
                    $rec_str .= '<span class="fa fa-globe"></span> ' . $cities_array[$job->city_id];
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label">';
                    $rec_str .= '<span class="fa fa-clock-o"></span> ' . $hours_array[$job->hour_id];
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label ">';
                    $rec_str .= '<span class="fa fa-clock-o"></span> ' . rephraseTime($job->created_at, 0);
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= 'Status: ';
                    if ($list_view == 'apply_job') {
                        $rec_str .= '' . application_listing_status($job);
                    }
                    if ($list_view == 'offer_job') {
                        $rec_str .= '' . offer_listing_status($job);
                    }

                    if ($list_view == 'favorite_job') {
                        $id = $job->id;
                        $Auth_User = Auth::User();
                        $seeker_id = $Auth_User->refer_id;
                        $rec_str .= '' . favorite_listing_status($job->id, $seeker_id);
                    }

                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $str[] = $rec_str;
                }

                return $str;
            }
            else {
                $jobs = job_data($type, $type_id, $seeker_id);

                $employer = $jobs;

                $conditions_array = emp_conditions_pluck_data();
                $str = array();
                foreach ($employer as $employer) {
                    $class = "";

                    $rec_str = '';

                    $rec_str .= '<div class="pxp-jobs-card-4 pxp-has-border ' . $class . ' match_offer pxp-single-job-side-panel" data-toggle="' . $employer->match_offer_id . '" data-type="' . $job_history->type . '" data-id="' . $job_history->id . '" id="match_offer_' . $employer->match_offer_id . '">';

                    $rec_str .= '<div class="pxp-single-job-side-company">';

                    $rec_str .= '<div class="pxp-single-job-side-company-logo pxp-cover" style="background-image:  url(' . employer_profile_image_path($employer->id) . ');"></div>';

                    $rec_str .= '<div class="pxp-single-job-side-company-profile ">';
                    $rec_str .= '<div class="pxp-single-job-side-company-name">' . get_lang_field_data($employer, 'name') . '</div>';
                    $rec_str .= '<a href="#"></a>';
                    $rec_str .= '</div>';

                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label">';
                    $rec_str .= '<span class="fa fa-globe"></span>No of days: ' . $employer->days;
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label">';
                    if (!empty($employer->condition_id))
                        $rec_str .= '<span class="fa fa-clock-o"></span>Employement Condition: ' . $conditions_array[$employer->condition_id];
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label">';
                    if ($employer->salary_type == 1) {
                        if (!empty($employer->salary))
                            $rec_str .= '<span class="fa fa-clock-o"></span>Salary: ' . $employer->salary . 'Annual';
                    }
                    else {
                        $rec_str .= '<span class="fa fa-clock-o"></span>Salary: ' . $employer->salary . '/ Hour';
                    }
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '<div class="mt-1">';
                    $rec_str .= '<div class="pxp-single-job-side-info-label ">';
                    $rec_str .= '<span class="fa fa-clock-o"></span> ' . rephraseTime($employer->offer_created_at, 0);
                    $rec_str .= '</div>';
                    $rec_str .= '</div>';

                    $rec_str .= '</div>';

                    $str[] = $rec_str;
                }

                return $str;
            }
        }
    }

    public function common_jobs_sidebar_listing($jobs, $list_view = '', $job_history = '') {

        $employers_array = employers_pluck_data();

        $cities_array = cities_pluck_data();

        $hours_array = emp_hours_pluck_data();

        $str = array();
        foreach ($jobs as $job) {
            $class = "";

            $rec_str = '';

            if ($list_view == 'active_job') {

                $rec_str .= '<div class="pxp-jobs-card-4 pxp-has-border ' . $class . ' list_job pxp-single-job-side-panel" data-toggle="' . $job->id . '" data-type="' . $job_history->type . '" data-id="' . $job_history->id . '">';
            }
            else {
                $rec_str .= '<div class="pxp-jobs-card-4 pxp-has-border ' . $class . ' list_job pxp-single-job-side-panel" data-toggle="' . $job->id . '">';
            }

            $rec_str .= '<div class="pxp-single-job-side-company">';

            $rec_str .= '<div class="pxp-single-job-side-company-logo pxp-cover" style="background-image:  url(' . asset_url("images/company-logo-1.png") . ');"></div>';

            $rec_str .= '<div class="pxp-single-job-side-company-profile ">';
            $rec_str .= '<div class="pxp-single-job-side-company-name">' . get_lang_field_data($job, 'title') . '</div>';
            $rec_str .= '<a href="#">' . $employers_array[$job->employer_id] . '</a>';
            $rec_str .= '</div>';

            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label">';
            $rec_str .= '<span class="fa fa-globe"></span> ' . (key_exist($job->city_id, $cities_array)) && array_key_exists($job->city_id, $cities_array) ? $cities_array[$job->city_id] : '';
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label">';
            $rec_str .= '<span class="fa fa-clock-o"></span> ' . (key_exist($job->hour_id, $hours_array)) && array_key_exists($job->hour_id, $hours_array) ? $hours_array[$job->hour_id] : '';
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label ">';
            $rec_str .= '<span class="fa fa-clock-o"></span> ' . rephraseTime($job->created_at, 0);
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= 'Status: ';
            if ($list_view == 'apply_job') {
                $rec_str .= '' . application_listing_status($job);
            }
            if ($list_view == 'offer_job') {
                $rec_str .= '' . offer_listing_status($job);
            }

            if ($list_view == 'favorite_job') {
                $id = $job->id;
                $Auth_User = Auth::User();
                $seeker_id = $Auth_User->refer_id;
                $rec_str .= '' . favorite_listing_status($job->id, $seeker_id);
            }

            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $str[] = $rec_str;
        }

        return $str;
    }

    public function common_job_listing_details($job_details, $listing, $Record = null) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $job_details->id;

        $employer_id = $job_details->employer_id;
        $employer = Employer::find($employer_id);

        $provinces_array = provinces_pluck_data(35);

        $cities_array = cities_pluck_data();

        $hours_array = emp_hours_pluck_data();

        $employers_array = employers_pluck_data();
        ?>

        <div class="tab-pane active">

            <div class="pxp-jobs-tab-pane-cover pxp-cover" style="background-image: url(<?= employer_cover_image_path($job_details->employer_id); ?>);">
                <div class="row">
                    <div class="col-xl-3 col-xxl-3 pr-5">
                        <div class="pxp-jobs-tab-pane-logo mt-4" style="background-image: url(<?= employer_profile_image_path($job_details->employer_id); ?>);">
                        </div>
                    </div>
                    <div class="col-xl-8 col-xl-6 mt-4">

                        <h3 class="text-white"><?= get_lang_field_data($job_details, 'title'); ?></h3>


                        <div class="pxp-jobs-tab-pane-company-location text-white">
                            by 
                            <a href="<?= route('EmployerDetails', $employer->rec_no); ?>" class="pxp-jobs-tab-pane-company"><?= get_lang_field_data($employer, 'name'); ?></a> 

                        </div>
                    </div>
                </div>
                <div class="row float-end">
                    <div class="col-auto">
                        <div class="pxp-jobs-tab-pane-options mt-4 mt-xl-0">
                            <?php
                            if ($listing == 'applications') {
                                echo application_listing_actions($Record, $id);
                            }
                            elseif ($listing == 'favorites') {
                                echo favorite_listing_actions($Record, $id, $seeker_id);
                            }
                            elseif ($listing == 'offers') {
                                echo offer_listing_actions($Record);
                            }
                            elseif ($listing == 'active') {
                                // echo active_listing_actions($Record);
                                echo active_listing_actions($Record, $Auth_User);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>



            <div class="pxp-jobs-tab-pane-content">
                <?php
                if ($listing == 'active') {
                    ?>
                    <?= job_general_data_html($job_details, 'active', $Record); ?>
                    <?php
                }
                else {
                    ?>
                    <?= job_general_data_html($job_details); ?>
                    <?php
                }
                ?>
            </div>

        <?php /* ?><button class="btn rounded-pill pxp-jobs-tab-pane-close-btn d-inline-block d-lg-none"><span class="fa fa-angle-left"></span> Back</button><?php */ ?>

            <input type="hidden" id="list_job_details_open" value="<?= $job_details->id; ?>">
            <?php
            if ($listing != 'active') {
                ?>
                <input type="hidden" id="applied_job_open" value="<?= $Record->id; ?>">
                <?php
            }
            ?>


        </div>

        <?php
    }

    public function common_employer_sidebar_listing($employer, $list_view = '', $job_history = '') {
        $conditions_array = emp_conditions_pluck_data();
        $str = array();
        foreach ($employer as $employer) {
            $class = "";

            $rec_str = '';
            if ($list_view == 'active_job') {
                $rec_str .= '<div class="pxp-jobs-card-4 pxp-has-border ' . $class . ' match_offer pxp-single-job-side-panel" data-toggle="' . $employer->match_offer_id . '" data-type="' . $job_history->type . '" data-id="' . $job_history->id . '" id="match_offer_' . $employer->match_offer_id . '">';
            }
            else {
                $rec_str .= '<div class="pxp-jobs-card-4 pxp-has-border ' . $class . ' match_offer pxp-single-job-side-panel" data-toggle="' . $employer->match_offer_id . '">';
            }

            $rec_str .= '<div class="pxp-single-job-side-company">';

            $rec_str .= '<div class="pxp-single-job-side-company-logo pxp-cover" style="background-image:  url(' . employer_profile_image_path($employer->id) . ');"></div>';

            $rec_str .= '<div class="pxp-single-job-side-company-profile ">';
            $rec_str .= '<div class="pxp-single-job-side-company-name">' . get_lang_field_data($employer, 'name') . '</div>';
            $rec_str .= '<a href="#"></a>';
            $rec_str .= '</div>';

            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label">';
            $rec_str .= '<span class="fa fa-globe"></span>No of days: ' . $employer->days;
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label">';
            if (!empty($employer->condition_id))
                $rec_str .= '<span class="fa fa-clock-o"></span>Employement Condition: ' . $conditions_array[$employer->condition_id];
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label">';
            if ($employer->salary_type == 1) {
                if (!empty($employer->salary))
                    $rec_str .= '<span class="fa fa-clock-o"></span>Salary: ' . $employer->salary . 'Annual';
            }
            else {
                $rec_str .= '<span class="fa fa-clock-o"></span>Salary: ' . $employer->salary . '/ Hour';
            }
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '<div class="mt-1">';
            $rec_str .= '<div class="pxp-single-job-side-info-label ">';
            $rec_str .= '<span class="fa fa-clock-o"></span> ' . rephraseTime($employer->offer_created_at, 0);
            $rec_str .= '</div>';
            $rec_str .= '</div>';

            $rec_str .= '</div>';

            $str[] = $rec_str;
        }

        return $str;
    }

    public function match_offer(Request $request) {

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $open_offer_type = 'all';

        if (!empty($request->open_offer_type) && $request->open_offer_type != '' && $request->open_offer_type != 'all') {
            $open_offer_type = $request->open_offer_type;
            if ($open_offer_type == 'offered') {
                $status = '1';
            }
            if ($open_offer_type == 'declined') {
                $status = '2';
            }
            if ($open_offer_type == 'accepted') {
                $status = '3';
            }
            $employer = MatchOffer::leftjoin('employers', 'match_offers.employer_id', '=', 'employers.id')
                    ->select('employers.*', 'match_offers.id as match_offer_id', 'match_offers.condition_id', 'match_offers.days', 'match_offers.salary', 'match_offers.salary_type', 'match_offers.status as match_offer_status', 'match_offers.created_at as offer_created_at')
                    ->where('match_offers.seeker_id', '=', $seeker_id)
                    ->where('match_offers.status', '=', $status)
                    ->where('employers.status', 1)
                    ->orderby('match_offers.id', 'desc');
        }
        else {
            $open_offer_type = "all";
            $status = "1";
            $employer = MatchOffer::leftjoin('employers', 'match_offers.employer_id', '=', 'employers.id')
                    ->select('employers.*', 'match_offers.id as match_offer_id', 'match_offers.condition_id', 'match_offers.days', 'match_offers.salary', 'match_offers.salary_type', 'match_offers.status as match_offer_status', 'match_offers.created_at as offer_created_at')
                    ->where('match_offers.seeker_id', '=', $seeker_id)
                    ->where('match_offers.status', '>=', $status)
                    ->where('employers.status', 1)
                    ->orderby('match_offers.id', 'desc');
        }


        $employer = $employer->paginate(env('SEEKER_DASHBOARD_PAGINATION'));

        if ($request->ajax()) {

            $str = $this->common_employer_sidebar_listing($employer, $list_view = 'open_offer');

            if (!empty($str)) {
                return $str;
            }
            else {
                return $str = 0;
            }
        }
        else {

            $Settings = ContactDetail::find(1);

            // code for job Details

            $employer_details = "";
            $employer_id = "";

            if (!empty($request->open_offer_type) && $request->open_offer_type != '' && $request->open_offer_type != 'all') {
                $open_offer_type = $request->open_offer_type;
                if ($open_offer_type == 'offered') {
                    $status = '1';
                }
                if ($open_offer_type == 'declined') {
                    $status = '2';
                }
                if ($open_offer_type == 'accepted') {
                    $status = '3';
                }

                $employer_details = MatchOffer::leftjoin('employers', 'match_offers.employer_id', '=', 'employers.id')
                        ->select('employers.*', 'match_offers.id as match_offer_id', 'match_offers.condition_id', 'match_offers.days', 'match_offers.salary', 'match_offers.salary_type', 'match_offers.lat', 'match_offers.lng', 'match_offers.address_en as match_address_en', 'match_offers.status as match_offer_status', 'match_offers.created_at as offer_created_at')
                        ->where('match_offers.seeker_id', '=', $seeker_id)
                        ->where('match_offers.status', '=', $status)
                        ->where('employers.status', 1)
                        ->orderby('match_offers.id', 'desc')
                        ->first();
            }
            else {
                $open_offer_type = "all";
                $status = "1";

                $employer_details = MatchOffer::leftjoin('employers', 'match_offers.employer_id', '=', 'employers.id')
                        ->select('employers.*', 'match_offers.id as match_offer_id', 'match_offers.condition_id', 'match_offers.days', 'match_offers.salary', 'match_offers.salary_type', 'match_offers.lat', 'match_offers.lng', 'match_offers.address_en as match_address_en', 'match_offers.status as match_offer_status', 'match_offers.created_at as offer_created_at')
                        ->where('match_offers.seeker_id', '=', $seeker_id)
                        ->where('match_offers.status', '>=', $status)
                        ->where('employers.status', 1)
                        ->orderby('match_offers.id', 'desc')
                        ->first();
            }



            $conditions_array = "";

            if (!empty($employer_details)) {

                $employer_id = $employer_details->match_offer_id;

                $conditions_array = emp_conditions_pluck_data();
                $id = $employer_details->id;
                $records = EmployerCategory::select(['id'])->where('employer_id', '=', $id)->limit(1)->get();
                foreach ($records as $record) {
                    $category_exists = 1;
                    $categories_array = employer_categories_pluck_data($id);
                }
            }
        }



        return view('users.open_offers.listing', compact('Settings', 'employer_details', 'employer_id', 'employer', 'conditions_array', 'open_offer_type'));
    }

    public function matchofferDetailsByAjax(Request $request) {

        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->job_id;

        if (isset($request->view) && $request->view == 'active') {
            $job_history_type = $request->job_history_type;
            $job_history_id = $request->job_history_id;

            $Record = JobHistory::where('id', $job_history_id)
                    ->where('seeker_id', $seeker_id)
                    ->where('type', $job_history_type)
                    ->where('status', '=', 1)
                    ->first();

            if (empty($Record) || $Record == null) {
                return '<div class="mt-5"><h4>' . translate_it('No Active Job Available') . '</h4></div>';
            }
        }

        $employer_details = MatchOffer::leftjoin('employers', 'match_offers.employer_id', '=', 'employers.id')
                ->select('employers.*', 'match_offers.id as match_offer_id', 'match_offers.condition_id', 'match_offers.address_en as match_address_en', 'match_offers.days', 'match_offers.lat', 'match_offers.lng', 'match_offers.status as match_offer_status', 'match_offers.created_at as offer_created_at')
                ->where('match_offers.seeker_id', '=', $seeker_id)
                ->where('match_offers.status', '>=', 1)
                ->where('employers.status', 1)
                ->where('match_offers.id', $request->match_id)
                ->orderby('match_offers.id', 'desc')
                ->first();

        if (empty($employer_details) || $employer_details == null) {
            return "<div class='mt-5'><h4>Record Not Available</h4></div>";
        }
        ?>
        <div class="tab-pane active">

            <div class="pxp-jobs-tab-pane-cover pxp-cover" style="background-image: url(<?= employer_cover_image_path($employer_details->id); ?>);">
                <div class="row">
                    <div class="col-xl-3 col-xxl-3 pr-5">
                        <div class="pxp-jobs-tab-pane-logo mt-4" style="background-image: url(<?= employer_profile_image_path($employer_details->id); ?>);">

                        </div>
                    </div>
                    <div class="col-xl-8 col-xl-6 mt-4">

                        <h3 class="text-white mt-2"><?= get_lang_field_data($employer_details, 'title'); ?></h3>


                        <div class="pxp-jobs-tab-pane-company-location text-white">
                            by 
                            <a href="<?= route('EmployerDetails', $employer_details->rec_no); ?>" class="pxp-jobs-tab-pane-company"><?= get_lang_field_data($employer_details, 'name'); ?></a> 

                            <a href="#" class="pxp-jobs-tab-pane-location"></a>
                        </div>
                    </div>
                </div>
                <div class="row float-end">
                    <div class="col-auto">
                        <div class="pxp-jobs-tab-pane-options mt-xl-0">
                            <?php
                            if (isset($request->view) && $request->view == 'active') {
                                ?>
                                <?= active_listing_actions($Record, $Auth_User); ?>
                                <?php
                            }
                            else {
                                ?>
                                <?= open_offer_listing_actions($employer_details); ?>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>



            <div class="pxp-jobs-tab-pane-content">


        <?= seeker_offer_data_html('open_offer_view', $employer_details) ?>

                <button class="btn rounded-pill pxp-jobs-tab-pane-close-btn d-inline-block d-lg-none"><span class="fa fa-angle-left"></span> Back</button>

                <input type="hidden" id="match_offer_details_open" value="<?= $employer_details->match_offer_id ?>">

            </div>

        </div>
        <?php
    }

}
