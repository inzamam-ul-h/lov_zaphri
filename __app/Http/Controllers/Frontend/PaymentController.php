<?php

namespace App\Http\Controllers\Frontend;

use PDF;
use Auth;
use File;
use Flash;
use DateTime;
use Response;
use Attribute;
use Datatables;
use DateTimeZone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\MainController as MainController;
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
use App\Models\PaymentDetail;

class PaymentController extends MainController {

    public function pay_checkout(Request $request) {
        $AUTH_USER = Auth::user();
        $slot_exists = 0;
        $message = "No sessions found";
        if (isset($request->slots)) {
            $slots_array = ltrim(rtrim($request->slots));
            $slots = explode(',', $slots_array);
            if (count($slots) > 0) {
                $response = $this->save_booking_payments($slots, $AUTH_USER);
                $message = $response->message;
                if ($response->status) {
                    $slot_exists = 1;

                    Flash::success($message);
                    return redirect()->route('bookings.upcoming');
                    /* exit;
                      $payment_id = $response->payment_id;

                      return redirect()->route('payments.pay_now', $payment_id);
                      exit;

                      $Settings = General::find(1);
                      $paypal_account = $Settings->paypal_account;
                      $paypal_client_id = $Settings->paypal_client_id;
                      $paypal_secret_key = $Settings->paypal_secret_key;
                      $paypal = $Settings->paypal;
                      $paypal_url = ($paypal == 1) ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr";
                      return view('frontend.pay_checkout', compact("paypal_account", "paypal_client_id", "paypal_secret_key", "paypal_url", "payment_id", "total_payment")); */
                }
            }
        }

        if ($slot_exists == 0) {
            Flash::error($message);
            return redirect()->route('session_search');
        }
    }

}
