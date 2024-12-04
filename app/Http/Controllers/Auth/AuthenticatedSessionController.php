<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller {

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginByEmail(LoginRequest $request) {
        $response = $request->authenticate($request);

        if ($response['response_Status'] == 'success') {
            $request->session()->regenerate();

            return response()->json(['status' => true, 'messages' => 'Successfully Signed in. Redirecting you to Dashboard']);
        }
        else {
            return response()->json(['status' => false, 'messages' => $response['response_Text']]);
        }
    }

    public function loginByPhone(LoginRequest $request) {
        $response = $request->authenticatePhone($request);

        if ($response['response_Status'] == 'success') {
            $request->session()->regenerate();

            return response()->json(['status' => true, 'messages' => 'Successfully Signed in. Redirecting you to Dashboard']);
        }
        else {
            return response()->json(['status' => false, 'messages' => $response]);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/home');
    }

}
