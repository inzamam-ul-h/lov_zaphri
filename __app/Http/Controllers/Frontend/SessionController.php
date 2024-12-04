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
use App\Models\UserCalendar;
use App\Models\UserProfessional;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\SessionType;
use App\Models\Session;
use App\Models\Booking;
use App\Models\Payment;

class SessionController extends MainController {

    public function search(Request $request) {
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;
        $PageTitle = '';

        $start = strtotime($request->query('start', '2023-06-01 00:00:00'));
        $end = strtotime($request->query('end', '2023-06-01 00:00:00'));

        $availabilities = Session::select('id', 'user_id', 'type', 'price', 'color', 'time_start', 'time_end', 'booked')
                ->where('time_start', '>=', $start)
                ->where('time_end', '<=', $end)
                ->where('booked', '<', 1)
                ->get();

        return view('frontend.search', compact("Settings", "PageTitle", "PageDescription", "PageKeywords", "availabilities", "start", "end",));
    }

}
