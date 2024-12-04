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
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobVisit;
use App\Models\SavedJob;
use App\Models\JobOffer;
use App\Models\JobApplicant;
use App\Models\Match;
use App\Models\MatchOffer;
use App\Models\JobHistory;
use App\Models\SeekerNotification;
use App\Models\EmployerLocation;
use App\Models\EmployerCategory;
use App\Models\EmployerBenefit;
use App\Models\EmployerSocial;
use App\Models\SeekerDocument;

class NotificationController extends MainController {

    public function notifications(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $notifications = SeekerNotification::where('seeker_id', $seeker_id)->orderby('id', 'desc')->paginate(10);

        $Settings = ContactDetail::find(1);

        return view('users.notifications.listing', compact(
                        'Settings',
                        'notifications'
        ));
    }

    public function notification_details(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->id;

        $Applicant_Record = "";
        $job_details = "";
        $employer = "";
        $job_id = "";
        $job_skills = "";
        $job_groups = "";
        $job_languages = "";
        $job_benefits = "";
        $employer_location = "";

        $notification = SeekerNotification::where('id', $id)->where('seeker_id', $seeker_id)->first();

        if (empty($notification)) {
            Flash::success(translate_it('Notification Not Found.'));
            return redirect(route('users.notifications'));
        }

        // $JobApplicant = JobApplicant::find($notification->type_id);
        // if(empty($JobApplicant))
        // {
        // 	Flash::success('Record Not Found.');
        // 	return redirect(route('users.notifications'));
        // }
        $job_details = JobApplicant::leftjoin('jobs', 'job_applicants.job_id', '=', 'jobs.id')
                ->select('jobs.*', 'job_applicants.id as application_id', 'job_applicants.id as applicant_status')
                ->where('job_applicants.seeker_id', '=', $seeker_id)
                ->where('job_applicants.status', '>=', 1)
                ->where('job_applicants.id', $notification->type_id)
                ->where('jobs.status', 1)
                ->orderby('job_applicants.id', 'desc')
                ->first();

        if (empty($job_details)) {
            Flash::success(translate_it('Job Not Found.'));
            return redirect(route('users.notifications'));
        }

        if ($notification->status = 1) {
            $notification->status = 0;
            $notification->read_time = time();
            $notification->save();
        }


        $Applicant_Record = JobApplicant::find($job_details->application_id);

        $id = $job_details->id;
        $employer_id = $job_details->employer_id;
        $employer = Employer::find($employer_id);

        // for Skill
        $job_skills = get_job_skills_array_by_id($id);

        // for group
        $job_groups = get_job_groups_array_by_id($id);

        // for Language
        $job_languages = get_job_languages_array_by_id($id);

        // for benefits
        $job_benefits = get_job_benefits_array_by_id($id);

        $job_id = $job_details->id;
        $employer_default_location = "";
        $employer_default_location_exists = 0;
        $employer_default_location = EmployerLocation::select(['*'])->where('employer_id', '=', $employer_id)->where('is_default', 1)->first();

        if ($employer_default_location != null && !empty($employer_default_location)) {
            $employer_default_location_exists = 1;
        }

        $employer_location = EmployerLocation::select(['address_en', 'lat', 'lng'])->where('employer_id', '=', $employer_id)->get();

        $cities_array = cities_pluck_data();

        $categories_array = array();

        $sub_categories_array = sub_categories_pluck_data();

        $periods_array = emp_periods_pluck_data();

        $ethnicities_array = ethnicities_pluck_data();

        $groups_array = emp_groups_pluck_data();

        $conditions_array = emp_conditions_pluck_data();

        $hours_array = emp_hours_pluck_data();

        $employers_array = employers_pluck_data();

        $skills_array = skills_pluck_data();

        $languages_array = emp_languages_pluck_data();

        $benefits_array = emp_benefits_pluck_data();

        $Settings = ContactDetail::find(1);

        return view('users.notifications.details', compact(
                        'Settings',
                        'notification',
                        'job_details',
                        'cities_array',
                        'sub_categories_array',
                        'hours_array',
                        'employers_array',
                        'employer',
                        'job_skills',
                        'job_groups',
                        'periods_array',
                        'ethnicities_array',
                        'conditions_array',
                        'skills_array',
                        'groups_array',
                        'job_languages',
                        'job_benefits',
                        'languages_array',
                        'benefits_array',
                        'job_details',
                        'employer_location',
                        'employer_default_location',
                        'employer_default_location_exists',
                        'Applicant_Record'
        ));
    }

    public function job_offer_notification_details(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->id;

        $Applicant_Record = "";
        $job_details = "";
        $employer = "";
        $job_id = "";
        $job_skills = "";
        $job_groups = "";
        $job_languages = "";
        $job_benefits = "";
        $employer_location = "";

        $notification = SeekerNotification::where('id', $id)->where('seeker_id', $seeker_id)->first();

        if (empty($notification)) {
            Flash::success(translate_it('Notification Not Found.'));
            return redirect(route('users.notifications'));
        }

        // $JobApplicant = JobApplicant::find($notification->type_id);
        // if(empty($JobApplicant))
        // {
        // 	Flash::success('Record Not Found.');
        // 	return redirect(route('users.notifications'));
        // }

        $job_details = JobOffer::leftjoin('jobs', 'job_offers.job_id', '=', 'jobs.id')
                ->select('jobs.*', 'job_offers.id as application_id', 'job_offers.status as offer_status')
                ->where('job_offers.seeker_id', '=', $seeker_id)
                ->where('job_offers.status', '>=', 2)->where('job_offers.id', $notification->type_id)
                ->where('jobs.status', 1)
                ->orderby('job_offers.id', 'desc')
                ->first();

        if (empty($job_details)) {
            Flash::success(translate_it('Job Not Found.'));
            return redirect(route('users.notifications'));
        }
        else {
            $Applicant_Record = JobOffer::find($job_details->application_id);

            $id = $job_details->id;
            $employer_id = $job_details->employer_id;
            $employer = Employer::find($employer_id);

            // for Skill
            $job_skills = get_job_skills_array_by_id($id);

            // for group
            $job_groups = get_job_groups_array_by_id($id);

            // for Language
            $job_languages = get_job_languages_array_by_id($id);

            // for benefits
            $job_benefits = get_job_benefits_array_by_id($id);

            $job_id = $job_details->id;
            $employer_default_location = "";
            $employer_default_location_exists = 0;

            $employer_default_location = EmployerLocation::select(['*'])->where('employer_id', '=', $employer_id)->where('is_default', 1)->first();

            if ($employer_default_location != null && !empty($employer_default_location)) {
                $employer_default_location_exists = 1;
            }

            $employer_location = EmployerLocation::select(['address_en', 'lat', 'lng'])->where('employer_id', '=', $employer_id)->get();
        }

        if ($notification->status = 1) {
            $notification->status = 0;
            $notification->read_time = time();
            $notification->save();
        }

        $cities_array = cities_pluck_data();

        $categories_array = array();

        $sub_categories_array = sub_categories_pluck_data();

        $periods_array = emp_periods_pluck_data();

        $ethnicities_array = ethnicities_pluck_data();

        $groups_array = emp_groups_pluck_data();

        $conditions_array = emp_conditions_pluck_data();

        $hours_array = emp_hours_pluck_data();

        $employers_array = employers_pluck_data();

        $skills_array = skills_pluck_data();

        $languages_array = emp_languages_pluck_data();

        $benefits_array = emp_benefits_pluck_data();

        $Settings = ContactDetail::find(1);

        return view('users.notifications.job_offer_details', compact(
                        'Settings',
                        'notification',
                        'job_details',
                        'cities_array',
                        'sub_categories_array',
                        'hours_array',
                        'employers_array',
                        'employer',
                        'job_skills',
                        'job_groups',
                        'periods_array',
                        'ethnicities_array',
                        'conditions_array',
                        'skills_array',
                        'groups_array',
                        'job_languages',
                        'job_benefits',
                        'languages_array',
                        'benefits_array',
                        'job_details',
                        'employer_location',
                        'employer_default_location',
                        'employer_default_location_exists',
                        'Applicant_Record'
        ));
    }

    public function open_offer_notification_details(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->id;

        $employer_location = "";
        $conditions_array = "";
        $categories_array = array();
        $category_exists = 0;

        $benefit_exists = 0;
        $benefits_array = array();

        $social_links_array = array();
        $social_links_exists = 0;

        $notification = SeekerNotification::where('id', $id)->where('seeker_id', $seeker_id)->first();

        if (empty($notification)) {
            Flash::success(translate_it('Notification Not Found.'));
            return redirect(route('users.notifications'));
        }

        // $JobApplicant = JobApplicant::find($notification->type_id);
        // if(empty($JobApplicant))
        // {
        // 	Flash::success('Record Not Found.');
        // 	return redirect(route('users.notifications'));
        // }

        $employer_details = MatchOffer::leftjoin('employers', 'match_offers.employer_id', '=', 'employers.id')
                ->select('employers.*', 'match_offers.id as match_offer_id', 'match_offers.condition_id', 'match_offers.days', 'match_offers.salary', 'match_offers.salary_type', 'match_offers.address_en as match_address_en', 'match_offers.lat', 'match_offers.lng', 'match_offers.status as match_offer_status', 'match_offers.created_at as offer_created_at')
                ->where('match_offers.seeker_id', '=', $seeker_id)
                ->where('match_offers.status', '>=', 1)->where('match_offers.id', $notification->type_id)
                ->where('employers.status', 1)
                ->orderby('match_offers.id', 'desc')
                ->first();

        if (empty($employer_details)) {
            Flash::success(translate_it('Record Not Found.'));
            return redirect(route('users.notifications'));
        }
        else {
            $employer_id = $employer_details->match_offer_id;

            $conditions_array = emp_conditions_pluck_data();
            $id = $employer_details->id;
            $records = EmployerCategory::select(['id'])->where('employer_id', '=', $id)->limit(1)->get();
            foreach ($records as $record) {
                $category_exists = 1;
                $categories_array = employer_categories_pluck_data($id);
            }

            $records = EmployerBenefit::select(['id'])->where('employer_id', '=', $id)->limit(1)->get();
            foreach ($records as $record) {
                $benefit_exists = 1;
                $benefits_array = employer_benefits_pluck_data($id);
            }
            $records = EmployerSocial::select(['id'])->where('employer_id', '=', $id)->limit(1)->get();
            foreach ($records as $record) {
                $social_links_exists = 1;
                $social_links_array = employer_socilals_links_pluck_data($id);
            }

            $employer_default_location = "";
            $employer_default_location_exists = 0;

            $employer_default_location = EmployerLocation::select(['*'])->where('employer_id', '=', $id)->where('is_default', 1)->first();
            if ($employer_default_location != null && !empty($employer_default_location)) {
                $employer_default_location_exists = 1;
            }

            $employer_location = EmployerLocation::select(['address_en', 'lat', 'lng'])->where('employer_id', '=', $id)->get();
        }

        if ($notification->status = 1) {
            $notification->status = 0;
            $notification->read_time = time();
            $notification->save();
        }
        $Settings = ContactDetail::find(1);
        return view('users.notifications.open_offer_details', compact(
                        'Settings', 'employer_details', 'employer_id', 'conditions_array', 'categories_array', 'category_exists', 'benefit_exists', 'benefits_array', 'social_links_array', 'social_links_exists', 'employer_default_location', 'employer_default_location_exists', 'employer_location', 'notification'
        ));
    }

    public function document_expiry_notification_details(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $id = $request->id;

        $notification = SeekerNotification::where('id', $id)->where('seeker_id', $seeker_id)->first();

        if (empty($notification)) {
            Flash::success(translate_it('Notification Not Found.'));
            return redirect(route('users.notifications'));
        }

        $seeker_documents = SeekerDocument::where('id', $notification->type_id)->where('seeker_id', $seeker_id)->first();
        $document_types = document_types_pluck_data('seeker');

        if ($notification->status = 1) {
            $notification->status = 0;
            $notification->read_time = time();
            $notification->save();
        }
        $Settings = ContactDetail::find(1);
        return view('users.notifications.expiry_document_details', compact(
                        'Settings', 'seeker_documents', 'document_types', 'notification'
        ));
    }

}
