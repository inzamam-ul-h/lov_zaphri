<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email'    => 'string|email',
            'password' => 'string',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function signup_logout_msg(LoginRequest $request, $User) {

        $response = array();
        $logOut = 0;
        $logOut_msg = '';
        if ($User->status == 0) {
            $logOut = 1;
            $logOut_msg = "Your Account is Inactive/Suspended by Admin.";
        }
        elseif ($User->user_type == 3 && $User->admin_approved == 0) {
            $logOut = 1;
            $logOut_msg = "Email Address verified Successfully. Approval pending from Admin.";
        }
        elseif ($User->user_type == 3 && $User->admin_approved == 2) {
            $logOut = 1;
            $logOut_msg = "Your Account is rejected by Admin.";
        }

        if ($logOut == 1) {

            $request->session()->invalidate();
            // $request->session()->regenerateToken();
            //RateLimiter::clear($this->throttleKey());
            RateLimiter::clear($this->throttleKey());
            $response['status'] = 'error';
            $response['message'] = $logOut_msg;
        }
        else {
            RateLimiter::clear($this->throttleKey());
            $response['status'] = 'success';
            $response['message'] = 'Login Successfull';
        }
        return $response;
    }

    public function authenticate(LoginRequest $request) {
        $email = $request->email;
        $password = $request->password;
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $email, 'password' => $password, 'status' => 1], $this->filled('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                        'email' => __('auth.failed'),
            ]);
        }

        $User = Auth::user();
        $response = $this->signup_logout_msg($request, $User);

        RateLimiter::clear($this->throttleKey());
        return ['response_Status' => $response['status'], 'response_Text' => $response['message']];
    }

    public function authenticatePhone(LoginRequest $request) {
        $phone_no = str_replace("+", "", $request->phone);
        $phone_no = "+" . ltrim(rtrim($phone_no));
        $password = $request->password;
        if (!Auth::attempt(['phone' => $phone_no, 'password' => $password, 'status' => 1], $this->filled('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                        'phone' => __('auth.failed'),
            ]);
        }
        $User = Auth::user();
        $response = $this->signup_logout_msg($request, $User);

        return ['response_Status' => $response['status'], 'response_Text' => $response['message']];
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited() {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
                    'email' => trans('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey() {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }

}
