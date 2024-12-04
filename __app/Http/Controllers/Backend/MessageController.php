<?php

namespace App\Http\Controllers\Backend;

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
use App\Models\Job;
use App\Models\Seeker;
use App\Models\EmployerReport;
use App\Models\EmployerReview;
use App\Models\Employer;
use App\Models\EmployerLocation;
use App\Models\Chat;
use App\Models\Conversation;
use App\Models\Attachment;
use App\Models\LogEmailSeeker;
use App\Models\MatchOffer;
use App\Models\JobApplicant;
use App\Models\JobOffer;
use App\Models\JobHistory;
use Mail;
use Illuminate\Support\Facades\URL;

class MessageController extends MainController {

    private function make_notification($notify_type, $employer_id, $seeker_id, $type_id) {
        if ($notify_type != '') {
            $notification = array();
            $notification['user_type'] = 'employer';
            $notification['refer_id'] = $employer_id;
            $notification['type'] = $notify_type;
            $notification['type_id'] = $type_id;

            create_notification($notification);

            $notification = array();
            $notification['user_type'] = 'seeker';
            $notification['refer_id'] = $seeker_id;
            $notification['type'] = $notify_type;
            $notification['type_id'] = $type_id;

            create_notification($notification);
        }
    }

    private function make_job_history($type, $employer_id, $seeker_id, $type_id) {
        if ($type != '') {
            $notification = array();
            $notification['employer_id'] = $employer_id;
            $notification['seeker_id'] = $seeker_id;
            $notification['type'] = $type;
            $notification['type_id'] = $type_id;

            create_job_history($notification);
        }
    }

    public function job_apply(Request $request, $job_id) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $job_id = $request->job_id;

        $message = '';
        $time = time();

        $exists = 0;
        $AppliedJobs = JobApplicant::where([
                    ['job_applicants.job_id', $job_id],
                    ['job_applicants.seeker_id', $seeker_id]
        ]);
        $AppliedJobs = $AppliedJobs->get();

        foreach ($AppliedJobs as $record) {
            $id = $record->id;
            $status = $record->status;

            $exists = 1;

            if ($status >= 1 && $status <= 3) {
                $Model_Data = JobApplicant::find($id);
                $Model_Data->status = 0;
                $Model_Data->declined_time = $time;
                $Model_Data->updated_by = $Auth_User_id;
                $Model_Data->save();

                $Model_Data = Job::find($job_id);
                $employer_id = $Model_Data->employer_id;

                $notify_type = 'job_application_cancelled';

                if ($notify_type != '') {
                    $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
                }

                $message = '<h5 class="modal-title text-center mt-4 text-danger">';
                $message .= translate_it('This job has been removed from your job appications.');
                $message .= '</h5>';
            }
            elseif ($status == 4) {
                $message = '<h5 class="modal-title text-center mt-4 text-danger">';
                $message .= translate_it('Employer already declined your application for this job.');
                $message .= '</h5>';
            }
            elseif ($status >= 5) {
                $message = '<h5 class="modal-title text-center mt-4 text-danger">';
                $message .= translate_it('Employer already accepted your application for this job.');
                $message .= '</h5>';
            }
            else {
                $exists = 0;
            }
        }

        $OfferedJobs = JobOffer::where([
                    ['job_offers.job_id', $job_id],
                    ['job_offers.seeker_id', $seeker_id]
        ]);
        $OfferedJobs = $OfferedJobs->get();

        foreach ($OfferedJobs as $record) {
            $id = $record->id;
            $status = $record->status;

            $exists = 1;

            if ($status == 2) {
                $Model_Data = JobOffer::find($id);
                $Model_Data->status = 3;
                $Model_Data->declined_time = $time;
                $Model_Data->updated_by = $Auth_User_id;
                $Model_Data->save();

                $message = '<h5 class="modal-title text-center mt-4 text-danger">';
                $message .= translate_it('Job Offer has been declined for this job.');
                $message .= '</h5>';
            }
            elseif ($status == 3) {
                $exists = 0;
                /* $message = '<h5 class="modal-title text-center mt-4 text-danger">';
                  $message.= 'You have already declined an offer for this job.';
                  $message.= '</h5>'; */
            }
            elseif ($status >= 4) {
                $message = '<h5 class="modal-title text-center mt-4 text-danger">';
                $message .= translate_it('You have already accepted an offer for this job.');
                $message .= '</h5>';
            }
            else {
                $exists = 0;
            }
        }

        if ($exists == 0) {
            $Model_Data = new JobApplicant();
            $Model_Data->seeker_id = $seeker_id;
            $Model_Data->job_id = $job_id;
            $Model_Data->applied_time = $time;
            $Model_Data->status = 1;
            $Model_Data->created_by = $Auth_User_id;
            $Model_Data->save();

            $Records = Job::find($job_id);
            $employer_id = $Records->employer_id;

            $notify_type = 'job_applied';

            if ($notify_type != '') {
                $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            $seeker_phone = 0;
            $seeker_has_phone = 0;
            $seeker_has_verified_phone = 0;

            $seeker_has_email = 0;
            $seeker_has_verified_email = 0;

            $Seeker = array();
            $Seeker = Seeker::find($seeker_id);
            if (!empty($Seeker)) {
                if (isset($Seeker->email) && !empty($Seeker->email)) {
                    $seeker_has_email = 1;
                }
                if (isset($Seeker->phone) && !empty($Seeker->phone)) {
                    $seeker_has_phone = 1;
                    $seeker_phone = trim($Seeker->phone);
                }
            }


            if ($seeker_has_phone == 1) {

                sms_to_seeker($Seeker, $employer_id, 20);
            }


            if ($seeker_has_email == 1) {
                $Job = Job::find($job_id);

                $full_name = $Seeker->name_en;
                $names = explode(' ', $full_name);
                $FIRST_NAME = $names[0];
                $LAST_NAME = $names[count($names) - 1];

                $JOB_TITLE = $Job->title_en;

                $CONDITION = '';
                $conditions_array = emp_conditions_pluck_data();
                if (isset($conditions_array[$Job->condition_id]))
                    $CONDITION = $conditions_array[$Job->condition_id];

                $PERIOD = '';
                $periods_array = emp_periods_pluck_data();
                if (isset($periods_array[$Job->period_id]))
                    $PERIOD = $periods_array[$Job->period_id];

                $HOUR = '';
                $hours_array = emp_hours_pluck_data();
                if (isset($hours_array[$Job->hour_id]))
                    $HOUR = $hours_array[$Job->hour_id];

                $SALARY = '-';
                $salary_type = $Job->salary_type;
                $salary = $Job->salary;
                if (!empty($salary) && !empty($salary_type)) {
                    if ($salary_type == 1)
                        $SALARY = '$' . $salary . ' per annum';
                    else
                        $SALARY = '$' . $salary . ' per hour';
                }

                $LOCATION = $Job->address_en;

                $messagess = get_email_template_body(9);

                $subject = $messagess->subject;
                $message = $messagess->description;
                //$message = nl2br($message);
                $message = get_email_content_in_template($subject, $message);
                $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
                $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
                $message = str_replace('[JOB_TITLE]', $JOB_TITLE, $message);
                $message = str_replace('[CONDITION]', $CONDITION, $message);
                $message = str_replace('[PERIOD]', $PERIOD, $message);
                $message = str_replace('[HOUR]', $HOUR, $message);
                $message = str_replace('[SALARY]', $SALARY, $message);
                $message = str_replace('[LOCATION]', $LOCATION, $message);

                //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
                $response = $this->send_email_to_seekers($request, $Seeker, $subject, $message);

                //echo $response;	
            }
            else {
                //echo '<p class="text-success">Job Offer Accepted. No Email sent as user did not added email address in the profile.</p>';exit;	                
            }

            $message = '<h5 class="modal-title text-center mt-4 text-success">';
            $message .= translate_it('Your have Successfully Applied to the Job.');
            $message .= '</h5>';
            {

                $log_array = array();
                $log_array['title'] = 'Job Applied';
                $log_array['description'] = 'Seeker has Applied For Publish Job';

                seeker_logs($Auth_User, $log_array);
            }
        }

        return $message;
    }

    public function job_offer_approved(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $job_id = $request->job_id;

        $Model_Data = JobOffer::where('job_id', '=', $job_id)->where('seeker_id', '=', $seeker_id)->first();
        if (!empty($Model_Data)) {
            $Records = Job::find($job_id);
            $employer_id = $Records->employer_id;

            $Employer = array();
            $employer_has_email = 0;
            $employer_has_phone = 0;
            $employer_has_verified_email = 0;
            $Employer = Employer::find($employer_id);
            if (!empty($Employer)) {
                if (isset($Employer->email) && !empty($Employer->email)) {
                    $employer_has_email = 1;
                }

                if (isset($Employer->phone) && !empty($Employer->phone)) {
                    $employer_has_email = 1;
                }
            }

            $message = '';
            if (isset($request->message) && !empty($request->message)) {
                $message = trim($request->message);
            }
            if ($message == '') {
                //echo '<p class="text-danger">Please add some message to send.</p>';exit;
            }

            $notify_type = $job_offered_type = 'job_offered_accepted';

            $Model_Data->status = 4;
            $Model_Data->seeker_comments = $request->seeker_comments;

            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            if ($notify_type != '') {
                $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            if ($job_offered_type != '') {
                $Job = Job::find($job_id);
                $employer_id = $Job->employer_id;

                $this->make_job_history($job_offered_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            if ($employer_has_phone == 1) {

                sms_to_employer($Employer, $seeker_id, 18);
            }

            if ($employer_has_email == 1) {
                $offer_id = $Model_Data->id;

                $Seeker = array();
                $seeker_has_email = 0;
                $seeker_has_verified_email = 0;
                $Seeker = Seeker::find($seeker_id);
                if (!empty($Seeker)) {
                    if (isset($Seeker->email) && !empty($Seeker->email)) {
                        $seeker_has_email = 1;
                    }
                }

                $Job = Job::find($job_id);

                $full_name = $Employer->name_en;
                $names = explode(' ', $full_name);
                $FIRST_NAME = $names[0];
                $LAST_NAME = $names[count($names) - 1];

                $JOB_TITLE = $Job->title_en;

                $CONDITION = '';
                $conditions_array = emp_conditions_pluck_data();
                if (isset($conditions_array[$Job->condition_id]))
                    $CONDITION = $conditions_array[$Job->condition_id];

                $PERIOD = '';
                $periods_array = emp_periods_pluck_data();
                if (isset($periods_array[$Job->period_id]))
                    $PERIOD = $periods_array[$Job->period_id];

                $HOUR = '';
                $hours_array = emp_hours_pluck_data();
                if (isset($hours_array[$Job->hour_id]))
                    $HOUR = $hours_array[$Job->hour_id];

                $SALARY = '-';
                $salary_type = $Job->salary_type;
                $salary = $Job->salary;
                if (!empty($salary) && !empty($salary_type)) {
                    if ($salary_type == 1)
                        $SALARY = '$' . $salary . ' per annum';
                    else
                        $SALARY = '$' . $salary . ' per hour';
                }

                $LOCATION = $Job->address_en;

                $subject = $request->subject;
                $message = trim($request->message);
                //$message = nl2br($message);
                $message = get_email_content_in_template($subject, $message);
                $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
                $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
                $message = str_replace('[JOB_TITLE]', $JOB_TITLE, $message);
                $message = str_replace('[CONDITION]', $CONDITION, $message);
                $message = str_replace('[PERIOD]', $PERIOD, $message);
                $message = str_replace('[HOUR]', $HOUR, $message);
                $message = str_replace('[SALARY]', $SALARY, $message);
                $message = str_replace('[LOCATION]', $LOCATION, $message);

                //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
                $response = $this->send_email_to_employers($request, $Employer, $subject, $message);

                //echo $response;	
            }
            else {
                //echo '<p class="text-success">Job Offer Accepted. No Email sent as user did not added email address in the profile.</p>';exit;	                
            } {

                $log_array = array();
                $log_array['title'] = 'Job Offer Accepted';
                $log_array['description'] = 'Seeker has Accepted Your Job Offer';

                seeker_logs($Auth_User, $log_array);
            }

            echo '<p class="text-success">' . translate_it('Job Offer Accepted.') . '</p>';
            exit;
        }
        else {
            echo '<p class="text-danger">' . translate_it('Record Not Found.') . '</p>';
            exit;
        }

        //return redirect()->back();
    }

    public function job_offer_declined(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $job_id = $request->job_id;

        $Model_Data = JobOffer::where('job_id', '=', $job_id)->where('seeker_id', '=', $seeker_id)->first();
        if (!empty($Model_Data)) {
            $Records = Job::find($request->job_id);
            $employer_id = $Records->employer_id;

            $Employer = array();
            $employer_has_email = 0;
            $employer_has_phone = 0;
            $employer_has_verified_email = 0;
            $Employer = Employer::find($employer_id);
            if (!empty($Employer)) {
                if (isset($Employer->email) && !empty($Employer->email)) {
                    $employer_has_email = 1;
                }
                if (isset($Employer->phone) && !empty($Employer->phone)) {
                    $employer_has_phone = 1;
                }
            }

            $message = '';
            if (isset($request->message) && !empty($request->message)) {
                $message = trim($request->message);
            }
            if ($message == '') {
                //echo '<p class="text-danger">Please add some message to send.</p>';exit;
            }

            $notify_type = 'job_offered_declined';

            $Model_Data->status = 3;
            //$Model_Data->seeker_comments = $message;

            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            if ($notify_type != '') {
                $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            if ($employer_has_phone == 1) {

                sms_to_employer($Employer, $seeker_id, 19);
            }

            if ($employer_has_email == 1) {
                $offer_id = $Model_Data->id;

                $Seeker = array();
                $seeker_has_email = 0;
                $seeker_has_verified_email = 0;
                $Seeker = Seeker::find($seeker_id);
                if (!empty($Seeker)) {
                    if (isset($Seeker->email) && !empty($Seeker->email)) {
                        $seeker_has_email = 1;
                    }
                }

                $Job = Job::find($job_id);

                $full_name = $Employer->name_en;
                $names = explode(' ', $full_name);
                $FIRST_NAME = $names[0];
                $LAST_NAME = $names[count($names) - 1];

                $JOB_TITLE = $Job->title_en;

                $CONDITION = '';
                $conditions_array = emp_conditions_pluck_data();
                if (isset($conditions_array[$Job->condition_id]))
                    $CONDITION = $conditions_array[$Job->condition_id];

                $PERIOD = '';
                $periods_array = emp_periods_pluck_data();
                if (isset($periods_array[$Job->period_id]))
                    $PERIOD = $periods_array[$Job->period_id];

                $HOUR = '';
                $hours_array = emp_hours_pluck_data();
                if (isset($hours_array[$Job->hour_id]))
                    $HOUR = $hours_array[$Job->hour_id];

                $SALARY = '-';
                $salary_type = $Job->salary_type;
                $salary = $Job->salary;
                if (!empty($salary) && !empty($salary_type)) {
                    if ($salary_type == 1)
                        $SALARY = '$' . $salary . ' per annum';
                    else
                        $SALARY = '$' . $salary . ' per hour';
                }

                $LOCATION = $Job->address_en;

                $subject = $request->subject;

                $message = trim($request->message);
                //$message = nl2br($message);
                $message = get_email_content_in_template($subject, $message);
                $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
                $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
                $message = str_replace('[JOB_TITLE]', $JOB_TITLE, $message);
                $message = str_replace('[CONDITION]', $CONDITION, $message);
                $message = str_replace('[PERIOD]', $PERIOD, $message);
                $message = str_replace('[HOUR]', $HOUR, $message);
                $message = str_replace('[SALARY]', $SALARY, $message);
                $message = str_replace('[LOCATION]', $LOCATION, $message);

                //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
                $response = $this->send_email_to_employers($request, $Employer, $subject, $message);

                //echo $response;
            }
            else {
                //echo '<p class="text-success">Job Offer Declined. No Email sent as user did not added email address in the profile.</p>';exit;
            } {

                $log_array = array();
                $log_array['title'] = 'Job Offer Declined';
                $log_array['description'] = 'Seeker has Declined Your Job Offer';

                seeker_logs($Auth_User, $log_array);
            }

            echo '<p class="text-success">' . translate_it('Job Offer Declined.') . '</p>';
            exit;
        }
        else {
            echo '<p class="text-danger">' . translate_it('Record Not Found.') . '</p>';
            exit;
        }

        //return redirect()->back();
    }

    public function open_offer_approved(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $open_offer_id = $request->open_offer_id;

        $Model_Data = MatchOffer::where('id', '=', $request->open_offer_id)->where('seeker_id', '=', $seeker_id)->first();

        if (!empty($Model_Data)) {
            $employer_id = $Model_Data->employer_id;

            $Employer = array();
            $employer_has_email = 0;
            $employer_has_phone = 0;
            $employer_has_verified_email = 0;
            $Employer = Employer::find($employer_id);
            if (!empty($Employer)) {
                if (isset($Employer->email) && !empty($Employer->email)) {
                    $employer_has_email = 1;
                }
                if (isset($Employer->phone) && !empty($Employer->phone)) {
                    $employer_has_phone = 1;
                }
            }

            $message = '';
            if (isset($request->message) && !empty($request->message)) {
                $message = trim($request->message);
            }
            if ($message == '') {
                //echo '<p class="text-danger">Please add some message to send.</p>';exit;
            }

            $notify_type = $open_offered_type = 'open_offered_accepted';

            $Model_Data->status = 3;
            //$Model_Data->seeker_comments = $message;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            if ($notify_type != '') {
                $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            if ($open_offered_type != '') {
                $this->make_job_history($open_offered_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            if ($employer_has_phone == 1) {

                sms_to_employer($Employer, $seeker_id, 16);
            }

            if ($employer_has_email == 1) {
                $offer_id = $Model_Data->id;

                $Seeker = array();
                $seeker_has_email = 0;
                $seeker_has_verified_email = 0;
                $Seeker = Seeker::find($seeker_id);
                if (!empty($Seeker)) {
                    if (isset($Seeker->email) && !empty($Seeker->email)) {
                        $seeker_has_email = 1;
                    }
                }

                $full_name = $Employer->name_en;
                $names = explode(' ', $full_name);
                $FIRST_NAME = $names[0];
                $LAST_NAME = $names[count($names) - 1];

                $CONDITION = '';
                $conditions_array = emp_conditions_pluck_data();
                if (isset($conditions_array[$Model_Data->condition_id]))
                    $CONDITION = $conditions_array[$Model_Data->condition_id];

                $DAYS = '';
                if (isset($Model_Data->days))
                    $DAYS = $Model_Data->days . ' Days';

                $SALARY = '-';
                $salary_type = $Model_Data->salary_type;
                $salary = $Model_Data->salary;
                if (!empty($salary) && !empty($salary_type)) {
                    if ($salary_type == 1)
                        $SALARY = '$' . $salary . ' per annum';
                    else
                        $SALARY = '$' . $salary . ' per hour';
                }

                $LOCATION = $Model_Data->address_en;

                $subject = $request->subject;

                $message = trim($request->message);
                //$message = nl2br($message);
                $message = get_email_content_in_template($subject, $message);
                $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
                $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
                $message = str_replace('[CONDITION]', $CONDITION, $message);
                $message = str_replace('[SALARY]', $SALARY, $message);
                $message = str_replace('[LOCATION]', $LOCATION, $message);

                //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
                $response = $this->send_email_to_employers($request, $Employer, $subject, $message);

                //echo $response;	
            }
            else {
                //echo '<p class="text-success">Open Offer Accepted. No Email sent as user did not added email address in the profile.</p>';exit;
            } {

                $log_array = array();
                $log_array['title'] = 'Open Offer Accepted';
                $log_array['description'] = 'Seeker has Accepted Your Open Offer';

                seeker_logs($Auth_User, $log_array);
            }

            echo '<p class="text-success">' . translate_it('Open Offer Accepted.') . '</p>';
            exit;
        }
        else {
            echo '<p class="text-danger">' . translate_it('Record Not Found.') . '</p>';
            exit;
        }

        //return redirect()->back();
    }

    public function open_offer_declined(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $open_offer_id = $request->open_offer_id;

        $Model_Data = MatchOffer::where('id', '=', $request->open_offer_id)->where('seeker_id', '=', $seeker_id)->first();

        if (!empty($Model_Data)) {
            $employer_id = $Model_Data->employer_id;

            $Employer = array();
            $employer_has_email = 0;
            $employer_has_phone = 0;
            $employer_has_verified_email = 0;
            $Employer = Employer::find($employer_id);
            if (!empty($Employer)) {
                if (isset($Employer->email) && !empty($Employer->email)) {
                    $employer_has_email = 1;
                }
                if (isset($Employer->phone) && !empty($Employer->phone)) {
                    $employer_has_phone = 1;
                }
            }

            $message = '';
            if (isset($request->message) && !empty($request->message)) {
                $message = trim($request->message);
            }
            if ($message == '') {
                //echo '<p class="text-danger">Please add some message to send.</p>';exit;
            }

            $notify_type = 'open_offered_declined';

            $Model_Data->status = 2;
            //$Model_Data->seeker_comments = $message;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            if ($notify_type != '') {
                $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            }

            if ($employer_has_phone == 1) {

                sms_to_employer($Employer, $seeker_id, 17);
            }

            if ($employer_has_email == 1) {
                $offer_id = $Model_Data->id;

                $Seeker = array();
                $seeker_has_email = 0;
                $seeker_has_verified_email = 0;
                $Seeker = Seeker::find($seeker_id);
                if (!empty($Seeker)) {
                    if (isset($Seeker->email) && !empty($Seeker->email)) {
                        $seeker_has_email = 1;
                    }
                }

                $full_name = $Employer->name_en;
                $names = explode(' ', $full_name);
                $FIRST_NAME = $names[0];
                $LAST_NAME = $names[count($names) - 1];

                $CONDITION = '';
                $conditions_array = emp_conditions_pluck_data();
                if (isset($conditions_array[$Model_Data->condition_id]))
                    $CONDITION = $conditions_array[$Model_Data->condition_id];

                $DAYS = '';
                if (isset($Model_Data->days))
                    $DAYS = $Model_Data->days . ' Days';

                $SALARY = '-';
                $salary_type = $Model_Data->salary_type;
                $salary = $Model_Data->salary;
                if (!empty($salary) && !empty($salary_type)) {
                    if ($salary_type == 1)
                        $SALARY = '$' . $salary . ' per annum';
                    else
                        $SALARY = '$' . $salary . ' per hour';
                }

                $LOCATION = $Model_Data->address_en;

                $subject = $request->subject;

                $message = trim($request->message);
                //$message = nl2br($message);
                $message = get_email_content_in_template($subject, $message);
                $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
                $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
                $message = str_replace('[CONDITION]', $CONDITION, $message);
                $message = str_replace('[SALARY]', $SALARY, $message);
                $message = str_replace('[LOCATION]', $LOCATION, $message);

                //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
                $response = $this->send_email_to_employers($request, $Employer, $subject, $message);

                //echo $response;		
            }
            else {
                //echo '<p class="text-success">Open Offer Declined. No Email sent as user did not added email address in the profile.</p>';exit;
            } {

                $log_array = array();
                $log_array['title'] = 'Open Offer Declined';
                $log_array['description'] = 'Seeker has Declined Your Open Offer';

                seeker_logs($Auth_User, $log_array);
            }

            echo '<p class="text-success">' . translate_it('Open Offer Declined') . '</p>';
            exit;
        }
        else {
            echo '<p class="text-danger">' . translate_it('Record Not Found.') . '</p>';
            exit;
        }

        //return redirect()->back();
    }

    public function send_email(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;

        $message = '';
        if (isset($request->message) && !empty($request->message)) {
            $message = trim($request->message);
        }
        if ($message == '') {
            echo '<p class="text-danger">' . translate_it('Please add some message to send.') . '</p>';
            exit;
        }

        $employer_id = 0;
        $Employer = array();
        $employer_has_email = 0;
        $employer_has_verified_email = 0;
        if (isset($request->employer_id) && !empty($request->employer_id)) {
            $Employer = Employer::find($request->employer_id);
            if (!empty($Employer)) {
                $employer_id = $request->employer_id;
                if (isset($Employer->email) && !empty($Employer->email)) {
                    $employer_has_email = 1;
                }
            }
        }
        if ($employer_id == 0) {
            echo '<p class="text-danger">' . translate_it('Please select valid profile.') . '</p>';
            exit;
        }
        elseif ($employer_has_email == 0) {
            echo '<p class="text-danger">' . translate_it('No Email address added in the profile.') . '</p>';
            exit;
        }

        $subject = translate_it('Laravel Test Mail');

        $message = trim($request->message);

        //$message = nl2br($message);
        $body = get_email_content_in_template($subject, $message);

        $response = $this->send_email_to_employers($request, $Employer, $subject, $body);

        echo $response;
    }

    public function send_message(Request $request) {
        $Auth_User = Auth::user();

        $message = '';
        if (isset($request->message) && !empty($request->message)) {
            $message = trim($request->message);
        }
        if ($message == '') {
            echo '<p class="text-danger">' . translate_it('Please add some message to send.') . '</p>';
            exit;
        }

        $seeker_id = 0;
        if (isset($request->seeker_id) && !empty($request->seeker_id)) {
            $Seeker = Seeker::find($request->seeker_id);
            if (!empty($Seeker)) {
                $seeker_id = $request->seeker_id;
            }
        }
        if ($seeker_id == 0) {
            echo '<p class="text-danger">' . translate_it('Please select valid profile.') . '</p>';
            exit;
        }

        $sender_id = $Auth_User->id;
        $sender_type = $Auth_User->user_type;

        $receiver_id = $seeker_id;
        $receiver_type = 'seeker';

        $Records = Chat::select(['id']);
        $Records = $Records->where([
            ['sender_type', $sender_type],
            ['sender_id', $sender_id],
            ['receiver_type', $receiver_type],
            ['receiver_id', $receiver_id]
        ]);
        $Records = $Records->orWhere([
            ['sender_type', $receiver_type],
            ['sender_id', $receiver_id],
            ['receiver_type', $sender_type],
            ['receiver_id', $sender_id]
        ]);
        $Records = $Records->orderBy('id', 'desc');
        $Records = $Records->first();

        $chat_id = 0;
        if (empty($Records)) {
            $Chat = new Chat();
            $Chat->sender_type = $sender_type;
            $Chat->sender_id = $sender_id;
            $Chat->receiver_type = $receiver_type;
            $Chat->receiver_id = $receiver_id;
            $Chat->created_by = $Auth_User->id;
            $Chat->save();

            $chat_id = $Chat->id;
        }
        else {
            $chat_id = $Records->id;
        }

        $Conversation = new Conversation();
        $Conversation->chat_id = $chat_id;
        $Conversation->user_type = $sender_type;
        $Conversation->user_id = $sender_id;
        $Conversation->message = $message;
        $Conversation->message_time = time();
        $Conversation->created_by = $Auth_User->id;
        $Conversation->save();

        echo '<p class="text-success">' . translate_it('Your Message Successfully Sent') . '</p>';
    }

    public function send_email_to_employers(Request $request, $Employer, $subject, $body) {
        $Auth_User = Auth::user();

        $employer_id = $Employer->id;

        $seeker_id = $Auth_User->refer_id;
        $Seeker = Seeker::find($seeker_id);

        $mail_sender_name = trim($Seeker->name_en);
        $mail_sender_email = trim($Seeker->email);

        $mail_receiver_name = trim($Employer->name_en);
        $mail_receiver_email = trim($Employer->email);

        $from = array(
            $mail_sender_name => $mail_sender_email
        );

        $toArr = array(
            $mail_receiver_name => $mail_receiver_email
        );

        $ccArr = array();

        $Log = new LogEmailSeeker();

        $Log->seeker_id = $seeker_id;
        $Log->employer_id = $employer_id;

        $Log->from = json_encode($from);
        $Log->to = json_encode($toArr);
        $Log->cc = $ccArr ? json_encode($ccArr) : NULL;
        $Log->subject = $subject;
        $Log->body = $body;
        // $Log->no_of_tries = 1;       
        // $Log->is_process = 1;       
        // $Log->process_time = time();

        $Log->save();

        //dispatch(new \App\Jobs\SendEmailJob($data));

        $response = '<p class="text-success">' . translate_it('Email Successfully Sent') . '</p>';

        return $response;
    }

    public function send_email_to_seekers(Request $request, $Seeker, $subject, $body) {
        $Auth_User = Auth::user();
        $seeker_id = $Auth_User->refer_id;

        $mail_sender_name = trim($Seeker->name_en);
        $mail_sender_email = trim($Seeker->email);

        $mail_receiver_name = trim($Seeker->name_en);
        $mail_receiver_email = trim($Seeker->email);

        $from = array(
            $mail_sender_name => $mail_sender_email
        );

        $toArr = array(
            $mail_receiver_name => $mail_receiver_email
        );

        $ccArr = array();

        $Log = new LogEmailSeeker();

        $Log->seeker_id = $seeker_id;

        $Log->from = json_encode($from);
        $Log->to = json_encode($toArr);
        $Log->cc = $ccArr ? json_encode($ccArr) : NULL;
        $Log->subject = $subject;
        $Log->body = $body;
        // $Log->no_of_tries = 1;       
        // $Log->is_process = 1;       
        // $Log->process_time = time();

        $Log->save();
        //dispatch(new \App\Jobs\SendEmailJob($data));

        $response = '<p class="text-success">' . translate_it('Email Successfully Sent') . '</p>';

        return $response;
    }

    public function report_job(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $history_id = $request->history_id;
        $type_id = $request->type_id;
        {
            $Model_Data = JobHistory::find($history_id);
            $employer_id = $Model_Data->employer_id;

            $Model_Data->employer_reported = 1;
            $Model_Data->save();

            // $Employer = array();
            // $employer_has_email = 0;
            // $employer_has_verified_email = 0;
            // $Employer = Employer::find($employer_id);
            // if(!empty($Employer))
            // {
            //     if(isset($Employer->email) && !empty($Employer->email))
            //     {
            //         $employer_has_email = 1;
            //     }
            // }               

            $message = '';
            if (isset($request->message) && !empty($request->message)) {
                $message = trim($request->message);
            }
            if ($message == '') {
                //echo '<p class="text-danger">Please add some message to send.</p>';exit;
            }

            // $notify_type = 'open_offered_declined';
            $Model_Data = new EmployerReport();
            $Model_Data->seeker_id = $seeker_id;
            $Model_Data->employer_id = $employer_id;
            $Model_Data->history_id = $history_id;
            $Model_Data->type_id = $type_id;
            $Model_Data->comments_en = $message;
            $Model_Data->comments_fr = $message;
            $Model_Data->report_time = time();
            $Model_Data->status = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            // if($notify_type != '')
            // {       
            //     $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            // }       
            // if($employer_has_email == 1)
            // { 
            //     $offer_id = $Model_Data->id;
            //     $Seeker = array();
            //     $seeker_has_email = 0;
            //     $seeker_has_verified_email = 0;
            //     $Seeker = Seeker::find($seeker_id);
            //     if(!empty($Seeker))
            //     {
            //         if(isset($Seeker->email) && !empty($Seeker->email))
            //         {
            //             $seeker_has_email = 1;
            //         }
            //     }
            //     $full_name = $Employer->name_en;
            //     $names = explode(' ', $full_name);
            //     $FIRST_NAME = $names[0];
            //     $LAST_NAME = $names[count($names)-1];
            //     $CONDITION = '';
            //     $conditions_array = emp_conditions_pluck_data();
            //     if(isset($conditions_array[$Model_Data->days]))
            //         $CONDITION = $conditions_array[$Model_Data->days];
            //     $DAYS = '';
            //     if(isset($Model_Data->days))
            //         $DAYS = $Model_Data->days.' Days';
            //     $SALARY = '-';
            //     $salary_type = $Model_Data->salary_type;
            //     $salary = $Model_Data->salary;
            //     if(!empty($salary) && !empty($salary_type))
            //     {
            //         if($salary_type ==1)
            //             $SALARY = '$'.$salary.' per annum';
            //         else
            //             $SALARY = '$'.$salary.' per hour';
            //     }
            //     $LOCATION = $Model_Data->address_en;
            //     $subject = 'Zaphri: Open Offer Declined';
            //     $message = trim($request->message);        
            //     //$message = nl2br($message);
            //     $message = get_email_content_in_template($subject, $message);
            //     $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
            //     $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
            //     $message = str_replace('[CONDITION]', $CONDITION, $message);
            //     $message = str_replace('[SALARY]', $SALARY, $message);
            //     $message = str_replace('[LOCATION]', $LOCATION, $message);
            //     //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
            //     $response = $this->send_email_to_employers($request, $Employer, $subject, $message);
            //     //echo $response;       
            // }
            // else
            // {
            //     //echo '<p class="text-success">Open Offer Declined. No Email sent as user did not added email address in the profile.</p>';exit;
            // }  

            echo '<p class="text-success">' . translate_it('Job Has Been Reported') . '</p>';
            exit;
        }


        //return redirect()->back();  
    }

    public function review_job(Request $request) {
        $Auth_User = Auth::User();
        $Auth_User_id = $Auth_User->id;
        $seeker_id = $Auth_User->refer_id;
        $history_id = $request->history_id;
        {
            $Model_Data = JobHistory::find($history_id);
            $employer_id = $Model_Data->employer_id;

            $Model_Data->employer_rated = 1;
            $Model_Data->save();
            // $Employer = array();
            // $employer_has_email = 0;
            // $employer_has_verified_email = 0;
            // $Employer = Employer::find($employer_id);
            // if(!empty($Employer))
            // {
            //     if(isset($Employer->email) && !empty($Employer->email))
            //     {
            //         $employer_has_email = 1;
            //     }
            // }               

            $message = '';
            if (isset($request->message) && !empty($request->message)) {
                $message = trim($request->message);
            }
            if ($message == '') {
                echo '<p class="text-danger">' . translate_it('Please add some comments.') . '</p>';
                exit;
            }

            // $notify_type = 'open_offered_declined';
            $Model_Data = new EmployerReview();
            $Model_Data->seeker_id = $seeker_id;
            $Model_Data->employer_id = $employer_id;
            $Model_Data->history_id = $history_id;
            $Model_Data->rating = $request->rating;
            $Model_Data->comments_en = $message;
            $Model_Data->comments_fr = $message;
            $Model_Data->review_time = time();
            $Model_Data->status = 1;
            $Model_Data->updated_by = $Auth_User->id;
            $Model_Data->save();

            // if($notify_type != '')
            // {       
            //     $this->make_notification($notify_type, $employer_id, $seeker_id, $Model_Data->id);
            // }       
            // if($employer_has_email == 1)
            // { 
            //     $offer_id = $Model_Data->id;
            //     $Seeker = array();
            //     $seeker_has_email = 0;
            //     $seeker_has_verified_email = 0;
            //     $Seeker = Seeker::find($seeker_id);
            //     if(!empty($Seeker))
            //     {
            //         if(isset($Seeker->email) && !empty($Seeker->email))
            //         {
            //             $seeker_has_email = 1;
            //         }
            //     }
            //     $full_name = $Employer->name_en;
            //     $names = explode(' ', $full_name);
            //     $FIRST_NAME = $names[0];
            //     $LAST_NAME = $names[count($names)-1];
            //     $CONDITION = '';
            //     $conditions_array = emp_conditions_pluck_data();
            //     if(isset($conditions_array[$Model_Data->days]))
            //         $CONDITION = $conditions_array[$Model_Data->days];
            //     $DAYS = '';
            //     if(isset($Model_Data->days))
            //         $DAYS = $Model_Data->days.' Days';
            //     $SALARY = '-';
            //     $salary_type = $Model_Data->salary_type;
            //     $salary = $Model_Data->salary;
            //     if(!empty($salary) && !empty($salary_type))
            //     {
            //         if($salary_type ==1)
            //             $SALARY = '$'.$salary.' per annum';
            //         else
            //             $SALARY = '$'.$salary.' per hour';
            //     }
            //     $LOCATION = $Model_Data->address_en;
            //     $subject = 'Zaphri: Open Offer Declined';
            //     $message = trim($request->message);        
            //     //$message = nl2br($message);
            //     $message = get_email_content_in_template($subject, $message);
            //     $message = str_replace('[FIRST_NAME]', $FIRST_NAME, $message);
            //     $message = str_replace('[LAST_NAME]', $LAST_NAME, $message);
            //     $message = str_replace('[CONDITION]', $CONDITION, $message);
            //     $message = str_replace('[SALARY]', $SALARY, $message);
            //     $message = str_replace('[LOCATION]', $LOCATION, $message);
            //     //[FIRST_NAME], [LAST_NAME], [JOB_TITLE], [CONDITION], [PERIOD], [HOUR], [SALARY], [LOCATION]
            //     $response = $this->send_email_to_employers($request, $Employer, $subject, $message);
            //     //echo $response;       
            // }
            // else
            // {
            //     //echo '<p class="text-success">Open Offer Declined. No Email sent as user did not added email address in the profile.</p>';exit;
            // }  

            echo '<p class="text-success">' . translate_it('Job Has Been Rated') . '</p>';
            exit;
        }
        // else
        // {
        //    echo '<p class="text-danger">'.translate_it('Record Not Found.').'</p>';exit;
        // }
        //return redirect()->back();  
    }

    // public function start_job(Request $request)
    // {
    //     $Auth_User = Auth::User();
    //     $Auth_User_id = $Auth_User->id;         
    //     $seeker_id = $Auth_User->refer_id;
    //     $start_job_id = $request->start_job_id;
    //     $Model_Data = JobHistory::where('id', $start_job_id)->where('seeker_id',$seeker_id)->first();
    //     if(empty($Model_Data))
    //     {
    //        echo '<p class="text-danger">'.translate_it('Record Not Found.').'</p>';exit;
    //     }
    //     else
    //     {
    //       $Model_Data->start_time = time();
    //       $Model_Data->save();
    //       echo '<p class="text-success">'.translate_it('The Job Has Been Started.').'</p>';exit;  
    //     }
    // }
    // public function end_job(Request $request)
    // {
    //     $Auth_User = Auth::User();
    //     $Auth_User_id = $Auth_User->id;         
    //     $seeker_id = $Auth_User->refer_id;
    //     $end_job_id = $request->end_job_id;
    //     $Model_Data = JobHistory::where('id', $end_job_id)->where('seeker_id',$seeker_id)->first();
    //     if(empty($Model_Data))
    //     {
    //        echo '<p class="text-danger">'.translate_it('Record Not Found.').'</p>';exit;
    //     }
    //     elseif($Model_Data->start_time == null)
    //     {
    //        echo '<p class="text-wanning">Job Has Not Started Yet.</p>';exit; 
    //     }
    //     else
    //     {
    //       $Model_Data->end_time = time();
    //       $Model_Data->save();
    //       echo '<p class="text-success">The Job Has Been Ended.</p>';exit;  
    //     }
    // }
}
