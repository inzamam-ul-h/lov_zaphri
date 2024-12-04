<?php

namespace App\Http\Controllers\Auth;

/* use App\Http\Controllers\Controller;


  use Illuminate\Auth\Events\Verified;

  use Illuminate\Foundation\Auth\EmailVerificationRequest; */

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use App\Models\User;

class VerifyEmailController extends Controller {

    public function __invoke(Request $request): RedirectResponse {
        $user = User::find($request->route('id')); //takes user ID from verification link. Even if somebody would hijack the URL, signature will be fail the request
        if ($user->hasVerifiedEmail()) {
            //return redirect()->route('home')->with('verified',1);
            return redirect()->intended('home?verified=1');
            //return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        //return redirect()->route('home')->with('verified',1);
        return redirect()->intended('home?verified=1');
        //return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }

    /**

     * Mark the authenticated user's email address as verified.

     *

     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request

     * @return \Illuminate\Http\RedirectResponse

     */
    /* public function __invoke(EmailVerificationRequest $request)

      {

      if ($request->user()->hasVerifiedEmail()) {

      return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');

      }



      if ($request->user()->markEmailAsVerified()) {

      event(new Verified($request->user()));

      }



      return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');

      } */
}
