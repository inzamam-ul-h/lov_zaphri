<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Template;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        $array = array();
        {
            // 1 Open Offer Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Open Offer";
                    $sub_array['subject'] = "Zaphri: New Open Offer";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>We have a new opening if you may be interested. Details are provided here</div><div><br></div><div>Employment Conditions: [CONDITION]</div><div>No of Days: [DAYS]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div><div><br></div><div>[VIEW_OFFER]</div><div><br></div><div>if you are interested please visit the link provided.</div><div><br></div><div>Waiting for your earliest response.</div>';

                $array[] = $sub_array;
            }
            
            // 2 Job Offer Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Offer";
                    $sub_array['subject'] = "Zaphri: New Job Offer";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>We have a new opening for [JOB_TITLE] if you may be interested. Details are provided here</div><div><br></div><div>Employment Conditions: [CONDITION]</div>Employment Period: [PERIOD]<div>Employment Hours: [HOUR]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div><div><br></div><div>[VIEW_OFFER]</div><div><br></div><div>if you are interested please visit the link provided.</div><div><br></div><div>Waiting for your earliest response.</div>';

                $array[] = $sub_array;
            }
            
            // 3 Job Application Accepted Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Application Accepted";
                    $sub_array['subject'] = "Zaphri: Job Application Approved";
                    $sub_array['description'] = '<div><span style="font-size: 0.875rem;">Hi [FIRST_NAME],</span><br></div><div><div><br></div><div><span style="font-size: 0.875rem;">Thank you for applying to the [JOB_TITLE]. I am delighted to accept the application formally</span>. Job Details are provided here</div><div><br></div><div>Employment Conditions: [CONDITION]</div>Employment Period: [PERIOD]<div>Employment Hours: [HOUR]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div><div><br></div><div><span style="font-size: 0.875rem;">Best Regards</span></div></div><div><br></div>';

                $array[] = $sub_array;
            }
            
            // 4 Job Application Declined Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Application Declined";
                    $sub_array['subject'] = "Zaphri: Job Application Declined";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>Thank you for applying to the [JOB_TITLE]. We appreciate your interest and for all the time you have put into the application process. Although we were impressed with your profile, we have decided not to move your application forward. We wish you the best of luck with your job search.<br></div><div><br></div><div>Best Regards</div>';

                $array[] = $sub_array;
            }
            
            // 5 Open Offer Accepted Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Open Offer Accepted";
                    $sub_array['subject'] = "Zaphri: Open Offer Accepted";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>An Open Offer has been accepted.&nbsp;<span style="font-size: 0.875rem;">Details are provided here</span></div><div><br></div><div>Employment Conditions: [CONDITION]</div><div>No of Days: [DAYS]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div>';

                $array[] = $sub_array;
            }
            
            // 6 Open Offer Declined Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Open Offer Declined";
                    $sub_array['subject'] = "Zaphri: Open Offer Declined";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>An Open Offer has been declined.&nbsp;<span style="font-size: 0.875rem;">Details are provided here</span></div><div><br></div><div>Employment Conditions: [CONDITION]</div><div>No of Days: [DAYS]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div>';

                $array[] = $sub_array;
            }
            
            // 7 Job Offer Accepted Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Job Offer Accepted";
                    $sub_array['subject'] = "Zaphri: Job Offer Accepted";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>Job Offer has been accepted.&nbsp;<span style="font-size: 0.875rem;">Details are provided here</span></div><div><br></div><div>Employment Conditions: [CONDITION]</div>Employment Period: [PERIOD]<div>Employment Hours: [HOUR]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div>';

                $array[] = $sub_array;
            }
            
            // 8 Job Offer Declined Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Job Offer Declined";
                    $sub_array['subject'] = "Zaphri: Job Offer Declined";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>Job Offer has been declined.&nbsp;<span style="font-size: 0.875rem;">Details are provided here</span></div><div><br></div><div>Employment Conditions: [CONDITION]</div>Employment Period: [PERIOD]<div>Employment Hours: [HOUR]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div>';

                $array[] = $sub_array;
            }
            
            // 9 Job Application Received Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Application Received";
                    $sub_array['subject'] = "Zaphri: Job Application Received";
                    $sub_array['description'] = '<div>Hi [FIRST_NAME],</div><div><br></div><div>Thank you for applying for the [JOB_TITLE].&nbsp;<span style="font-size: 0.875rem;">Details are provided here</span></div><div><br></div><div>Employment Conditions: [CONDITION]</div>Employment Period: [PERIOD]<div>Employment Hours: [HOUR]</div><div>Salary: [SALARY]</div><div>Location: [LOCATION]</div>';

                $array[] = $sub_array;
            }

            // 10 Documents is Expired Employer Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Documents is Expired";
                    $sub_array['subject'] = "Zaphri: Documents is Expired";
                    $sub_array['description'] = '<div><span style="font-size: 0.875rem;">Hi [FIRST_NAME],</span><br></div><div><div><br></div><div>We are notify you that your document is expired<br></div><div><br></div><div>Document Name: [DOCUMENT_NAME]</div><div><br></div><div><span style="font-size: 0.875rem;">Best Regards</span></div></div>';

                $array[] = $sub_array;
            }

            // 11 Documents is Expired Seeker Email
            {
                $sub_array = array();
                    $sub_array['type'] = "email";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Documents is Expired";
                    $sub_array['subject'] = "Zaphri: Documents is Expired";
                    $sub_array['description'] = '<div><span style="font-size: 0.875rem;">Hi [FIRST_NAME],</span><br></div><div><div><br></div><div>We are notify you that your document is expired<br></div><div><br></div><div>Document Name: [DOCUMENT_NAME]</div><div><br></div><div><span style="font-size: 0.875rem;">Best Regards</span></div></div>';

                $array[] = $sub_array;
            }

            // 12 Open Offer SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Open Offer";
                    $sub_array['subject'] = "Zaphri: New Open Offer";
                    $sub_array['description'] = 'Zaphri: New Open Offer';

                $array[] = $sub_array;
            }
            
            // 13 Job Offer SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Offer";
                    $sub_array['subject'] = "Zaphri: New Job Offer";
                    $sub_array['description'] = 'Zaphri: New Job Offer';

                $array[] = $sub_array;
            }
            
            // 14 Job Application Accepted SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Application Accepted";
                    $sub_array['subject'] = "Zaphri: Job Application Approved";
                    $sub_array['description'] = 'Zaphri: Job Application Approved';

                $array[] = $sub_array;
            }
            
            // 15 Job Application Declined SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Application Declined";
                    $sub_array['subject'] = "Zaphri: Job Application Declined";
                    $sub_array['description'] = 'Zaphri: Job Application Declined';

                $array[] = $sub_array;
            }
            
            // 16 Open Offer Accepted SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Open Offer Accepted";
                    $sub_array['subject'] = "Zaphri: Open Offer Accepted";
                    $sub_array['description'] = 'Zaphri: Open Offer Accepted';

                $array[] = $sub_array;
            }
            
            // 17 Open Offer Declined SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Open Offer Declined";
                    $sub_array['subject'] = "Zaphri: Open Offer Declined";
                    $sub_array['description'] = 'Zaphri: Open Offer Declined';

                $array[] = $sub_array;
            }
            
            // 18 Job Offer Accepted SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Job Offer Accepted";
                    $sub_array['subject'] = "Zaphri: Job Offer Accepted";
                    $sub_array['description'] = 'Zaphri: Job Offer Accepted';

                $array[] = $sub_array;
            }
            
            // 19 Job Offer Declined SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Job Offer Declined";
                    $sub_array['subject'] = "Zaphri: Job Offer Declined";
                    $sub_array['description'] = 'Zaphri: Job Offer Declined';

                $array[] = $sub_array;
            }
            
            // 20 Job Application Received SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Job Application Received";
                    $sub_array['subject'] = "Zaphri: Job Application Received";
                    $sub_array['description'] = 'Zaphri: Job Application Received';

                $array[] = $sub_array;
            }

            // 21 Documents is Expired Employer SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "employer";
                    $sub_array['title'] = "Documents is Expired";
                    $sub_array['subject'] = "Zaphri: Documents is Expired";
                    $sub_array['description'] = 'Zaphri: Documents is Expired';

                $array[] = $sub_array;
            }

            // 22 Documents is Expired Seeker SMS
            {
                $sub_array = array();
                    $sub_array['type'] = "sms";
                    $sub_array['type_for'] = "seeker";
                    $sub_array['title'] = "Documents is Expired";
                    $sub_array['subject'] = "Zaphri: Documents is Expired";
                    $sub_array['description'] = 'Zaphri: Documents is Expired';

                $array[] = $sub_array;
            }


        }
		
		
		foreach($array as $sub_array)
		{		
            if(!empty($sub_array))
            {
                $model = new Template();
                    $model->type = $sub_array['type'];
                    // $model->type_for = $sub_array['type_for'];
                    $model->title = $sub_array['title'];
                    $model->subject = $sub_array['subject'];
                    $model->description = $sub_array['description'];
                    $model->status = 1;
                    $model->created_by = 1;
                $model->save();
            }
		}
    }
}