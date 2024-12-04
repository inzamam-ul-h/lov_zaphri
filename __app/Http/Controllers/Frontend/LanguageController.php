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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class LanguageController extends MainController {

    //
    public function index() {
        if (!\Session::has('locale')) {
            \Session::put('locale', Input::get('locale'));
        }
        else {
            \Session::put('locale', Input::get('locale'));
        }
        return redirect()->back();
    }

    public function change(Request $request) {
        App::setLocale($request->lang);
        Session::put('locale', $request->lang);

        return redirect()->back();
    }

}
