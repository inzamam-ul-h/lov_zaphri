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
use App\Models\General;
use App\Models\User;
use App\Models\UserInterest;
use App\Models\Event;

class EventController extends MainController {

    private $home_route = "events.index";
    private $success_msg = "Inquiry sent successfully.";
    private $msg_not_found = "Event not found. Please try again.";

    public function listing() {
        $current_time = time();
        $events = Event::where('status', 1)->where('start_date_time', '>', $current_time)->paginate(2);

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
                        "PageKeywords",
                        "events"
        ));
    }

    public function detail($id) {
        $User = Auth::user();
        $user_id = null;
        $eventIntrest = null;

        $current_time = time();
        $event = Event::where('status', 1)->where('start_date_time', '>', $current_time)->where('id', $id)->first();

        // General for all pages
        $Settings = ContactDetail::find(1);

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "Event"; // will show default site Title
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        if ($User != null) {
            $user_id = $User->id;
            $eventIntrest = UserInterest::where('event_id', $id)->where('user_id', $user_id)->first();
        }

        return view("frontend.events-details",
                compact(
                        "Settings",
                        "PageTitle",
                        "PageDescription",
                        "PageKeywords",
                        "eventIntrest",
                        "event"
        ));
    }

    public function intrested($id) {
        if (Auth::user()) {
            $Auth_User = Auth::user();

            $user_id = $Auth_User->id;
            $current_time = time();
            $Model_Data = Event::where('status', 1)->where('start_date_time', '>', $current_time)->where('id', $id)->first();
            if (empty($Model_Data)) {
                return response()->json(['status' => 'false', 'message' => $this->msg_not_found]);
            }

            $userEventInterest = new UserInterest();
            $userEventInterest->user_id = $user_id;
            $userEventInterest->event_id = $id;
            $userEventInterest->save();

            return response()->json(['status' => 'success', 'message' => 'Event successfully intrested']);
        }
        return redirect()->route($this->home_route);
    }

    /**
     * update status of the specified resource in storage.
     *
     */
    public function notIntrested($id) {
        if (Auth::user()) {
            $Auth_User = Auth::user();

            $user_id = $Auth_User->id;
            $current_time = time();
            $Model_Data = Event::where('status', 1)->where('start_date_time', '>', $current_time)->where('id', $id)->first();
            if (empty($Model_Data)) {
                return response()->json(['status' => 'false', 'message' => $this->msg_not_found]);
            }

            UserInterest::where('user_id', $user_id)
                    ->where('event_id', $id)
                    ->delete();

            return response()->json(['status' => 'success', 'message' => 'Event successfully removed from intrested.']);
        }
        return redirect()->route($this->home_route);
    }

    public function inquery(Request $request, $event_id) {
        if (Auth::user()) {
            $User = Auth::user();
            $user_type = $User->user_type;
            if ($user_type == $this->_COACH_USER || $user_type == $this->_PLAYER_USER) {
                $Model_Data = Event::find($event_id);
                if (empty($Model_Data)) {
                    Flash::success('Event Details not found');
                    return redirect()->route('EventsPage');
                }

                $response = $this->send_inquiry_email($request, $User, $event_id);
                Flash::success($response['responseText']);
                return redirect()->route('EventsPage');
            }
        }
        return redirect()->route($this->home_route);
    }

}
