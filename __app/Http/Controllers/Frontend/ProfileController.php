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

class ProfileController extends MainController {

    public function user_profile($rec_no) {
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

}
