<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\MainController as MainController;
use App;
use Mail;
use Auth;
use File;
use Flash;
use Response;
use Illuminate\Http\Request;
use App\Models\ContactDetail;
use App\Models\User;
use App\Models\UserPersonal;
use App\Models\Webmail;
use App\Models\Category;
use App\Models\Subscriber;
use App\Models\ContactRequest;

class FrontendHomeController extends MainController {

    public function HomePage() {
        // General for all pages
        $Settings = ContactDetail::find(1);
        $coaches = UserPersonal::join('users', 'users.id', '=', 'user_personals.user_id')
                ->select(
                        'users.id',
                        'users.public_url',
                        'user_personals.first_name',
                        'user_personals.last_name',
                        'user_personals.about_me',
                        'user_personals.coachpic'
                )
                ->where('users.user_type', 1)
                ->where('users.status', 1)
                ->where('users.verified', 1)
                ->orderBy('users.rating', 'ASC')
                ->limit(6)
                ->get();

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = ""; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view("frontend.home",
                compact(
                        "coaches",
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    public function PlayersPage() {
        // General for all pages
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "Players"; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view("frontend.players",
                compact(
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    public function CoachesPage() {
        // General for all pages
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "Coaches"; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view("frontend.coaches",
                compact(
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    public function ClubsPage() {
        // General for all pages
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "Clubs"; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view("frontend.clubs",
                compact(
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    public function EventsPage() {
        // General for all pages
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "Events"; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view("frontend.events",
                compact(
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    public function ContactusPage() {
        // General for all pages
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "Contact Us"; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view("frontend.contactus",
                compact(
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords"
        ));
    }

    public function subscribeNews(Request $request) {
        $name = $request->subscribe_name;
        $email = $request->subscribe_email;
        $contact = $request->subscribe_contact;
        $timestamp = date("Y-m-d H:i:s");

        $subscription = Subscriber::where('email', $email)->first();

        if ($subscription == null) {
            $model = new Subscriber();
            $model->name = $name;
            $model->email = $email;
            $model->contact = $contact;
            $model->date_time = $timestamp;
            $model->status = 0;
            $model->save();
        }
        else {
            return response()->json(['status' => 'exist', 'message' => 'This email address is already subscribed']);
        }

        return response()->json(['status' => 'success', 'message' => 'subscribed successfully']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function ContactPageSubmit(Request $request) {
        $this->validate($request, [
            'contact_name'    => 'required',
            'contact_email'   => 'required|email',
            'contact_phone'   => 'required',
            'contact_comment' => 'required'
        ]);
        $name = $request->contact_name;
        $email = $request->contact_email;
        $contact = $request->contact_phone;
        $description = $request->contact_comment;

        $model = new ContactRequest();
        $model->name = $name;
        $model->email = $email;
        $model->contact = $contact;
        $model->description = $description;
        $model->status = 0;
        $model->save();

        /* if (env('NOCAPTCHA_STATUS', false)) 
          {
          $this->validate($request, [
          'g-recaptcha-response' => 'required|captcha'
          ]);
          }

          // SITE SETTINGS
          $Settings = ContactDetail::find(1);
          $site_title_var = "site_title_" . trans('backLang.boxCode');
          $site_email = $Settings->email;
          $site_url = $Settings->site_url;
          $site_title = $Settings->$site_title_var;

          $Webmail = new Webmail;
          $Webmail->cat_id = 0;
          $Webmail->group_id = null;
          $Webmail->title = $request->contact_subject;
          $Webmail->details = $request->contact_message;
          $Webmail->date = date("Y-m-d H:i:s");
          $Webmail->from_email = $request->contact_email;
          $Webmail->from_name = $request->contact_name;
          $Webmail->from_phone = $request->contact_phone;
          $Webmail->to_email = $Settings->site_webmails;
          $Webmail->to_name = $site_title;
          $Webmail->status = 0;
          $Webmail->flag = 0;
          $Webmail->save();

          // SEND Notification Email

          if ($Settings->notify_messages_status)
          {
          if (env('MAIL_USERNAME') != "")
          {

          }
          }

          //return "OK"; */
        //$this->email_to_admin($request);
        Flash::success('Successfully sent your query');
        return redirect()->route('ContactusPage');
    }

    public function email_to_admin(Request $request) {
        $mail_sender_name = $request->contact_name;
        $mail_sender_email = trim($request->contact_email);
        $subject = trim($request->contact_subject);
        $message = trim($request->contact_message);

        $body = get_email_content_in_template($subject, $message);

        $mail_receiver_name = env('MAIL_FROM_NAME');
        $mail_receiver_email = env('MAIL_FROM_ADDRESS');

        $data = array(
            'mail_sender_name'    => $mail_sender_name,
            'mail_sender_email'   => $mail_sender_email,
            'mail_receiver_name'  => $mail_receiver_name,
            'mail_receiver_email' => $mail_receiver_email,
            'name'                => $mail_sender_name,
            'subject'             => $subject,
            'body'                => $body
        );

        Mail::send('emails.emails', $data, function ($message) use ($subject, $mail_receiver_name, $mail_receiver_email, $mail_sender_name, $mail_sender_email) {
            $message->to($mail_receiver_email, $mail_receiver_name);
            $message->subject($subject);
            $message->from($mail_sender_email, $mail_sender_name);
        });

        // //dispatch(new \App\Jobs\SendEmailJob($data));
        // $response = '<p class="text-success">Email Successfully Sent</p>';
        // return $response;
    }

}
