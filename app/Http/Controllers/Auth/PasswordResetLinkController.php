<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Flash;
use App\Models\User;
use App\Models\Setting;
use App\Models\Banner;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Menu;
use App\Models\Section;
use App\Models\Topic;
use App\Models\TopicCategory;
use App\Models\Webmail;
use App\Models\WebmasterSection;
use App\Models\WebmasterSetting;

class PasswordResetLinkController extends Controller {

    /**

     * Display the password reset link request view.

     *

     * @return \Illuminate\View\View

     */
    public function create(Request $request, $lang = "") {
        if ($lang != "") {
            // Set Language
            App::setLocale($lang);
            \Session::put('locale', $lang);
        }
        // General Webmaster Settings
        $WebmasterSettings = WebmasterContactDetail::find(1);

        // General for all pages
        $Settings = ContactDetail::find(1);

        $FooterMenuLinks_father = Menu::find($WebmasterSettings->footer_menu_id);
        $FooterMenuLinks_name_fr = "";
        $FooterMenuLinks_name_en = "";
        if (!empty($FooterMenuLinks_father)) {
            $FooterMenuLinks_name_fr = $FooterMenuLinks_father->title_fr;
            $FooterMenuLinks_name_en = $FooterMenuLinks_father->title_en;
        }

        $site_desc_var = "site_desc_" . trans('backLang.boxCode');
        $site_keywords_var = "site_keywords_" . trans('backLang.boxCode');

        $PageTitle = "forgot-password";
        $PageDescription = $Settings->$site_desc_var;
        $PageKeywords = $Settings->$site_keywords_var;

        return view('auth.forgot-password',
                compact('WebmasterSettings',
                        'Settings',
                        'FooterMenuLinks_father',
                        'FooterMenuLinks_name_fr',
                        'FooterMenuLinks_name_en',
                        'site_desc_var', 'site_keywords_var',
                        'PageTitle', 'PageDescription',
                        'PageKeywords'));
    }

    /**

     * Handle an incoming password reset link request.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\RedirectResponse

     *

     * @throws \Illuminate\Validation\ValidationException

     */
    public function store(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.

        $status = Password::sendResetLink(
                        $request->only('email')
        );

        if ($status == "passwords.sent") {
            return response()->json(['status' => true, 'messages' => 'Password Reset Link is send to your Email.']);
        }
        else {
            return response()->json(['status' => false, 'messages' => 'Your Provided Email Address is Invalid. Please Provides Valid Email Address.']);
        }
    }

    public function storePhone(Request $request) {
        $request->validate([
            'phone' => 'required',
        ]);

        $phone_no = str_replace("+", "", $request->phone);
        $phone_no = "+" . ltrim(rtrim($phone_no));
        $User = User::where('phone', $phone_no)->first();

        if ($User != null) {
            if ($User->status == 0) {
                $errorMessage = 'Your Account is Inactive/Suspended by Admin.';
            }
            elseif ($User->verified == 0) {
                $errorMessage = 'Please verify your Phone No. first.';
            }
            elseif ($User->user_type == 3 && $User->admin_approved == 0) {
                $errorMessage = 'Phone Number Verified Successfully. Approval pending from Admin.';
            }
            elseif ($User->user_type == 3 && $User->admin_approved == 2) {
                $errorMessage = 'Your Account is rejected by Admin.';
            }
            else {
                $otp_response = $this->send_phone_otp($User);
                if ($otp_response) {
                    return response()->json(['status' => true, 'messages' => 'Verification code is sent to your phone no.']);
                }
                else {
                    $errorMessage = 'Error Sending SMS';
                }
            }

            return response()->json(['status' => false, 'messages' => $errorMessage]);
        }
        else {
            return response()->json(['status' => false, 'messages' => 'Your phone no. is not valid']);
        }
    }

}
