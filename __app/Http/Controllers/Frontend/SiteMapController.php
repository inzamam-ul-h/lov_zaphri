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

class SiteMapController extends MainController {

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string $lang
     * @return \Illuminate\Http\Response
     */
    public function siteMap($lang = "") {
        //
    }

}
