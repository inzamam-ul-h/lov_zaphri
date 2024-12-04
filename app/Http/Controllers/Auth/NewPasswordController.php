<?php

namespace App\Http\Controllers\Auth;

use Flash;
use App\Models\Menu;
use App\Models\User;
use App\Models\Topic;
use App\Models\Banner;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Webmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ContactDetail;
use App\Models\TopicCategory;
use App\Models\WebmasterSection;
use App\Models\WebmasterSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRule;

class NewPasswordController extends Controller {

    /**

     * Display the password reset view.

     *

     * @return \Illuminate\View\View

     */
    public function create(Request $request) {
        // // General for all pages
        $Settings = ContactDetail::find(1);

        $PageTitle = "forgot-password";

        return view('auth.reset-password', compact('request', 'PageTitle', 'Settings'));
    }

    /**

     * Handle an incoming new password request.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\RedirectResponse

     *

     * @throws \Illuminate\Validation\ValidationException

     */
    public function store(Request $request) {

        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(6)->mixedCase()->numbers()]
                /* 'password' => 'required|string|confirmed|min:8', */
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.

        $status = Password::reset(
                        $request->only('email', 'password', 'password_confirmation', 'token'),
                        function ($user) use ($request) {

                            $user->forceFill([
                                'password'       => Hash::make($request->password),
                                'remember_token' => Str::random(60),
                            ])->save();

                            event(new PasswordReset($user));
                        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        // Flash::success("Password Reset successfully");
        return $status == Password::PASSWORD_RESET ? redirect()->route('home')->withSuccess('status', __($status)) : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

}
