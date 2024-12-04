<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController as AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController as ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController as EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController as EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController as NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController as PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController as RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController as VerifyEmailController;
use App\Http\Controllers\Frontend\FrontendHomeController as FrontHomeController;

Route::get('/home', [FrontHomeController::class, 'HomePage'])->name('HomePage');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest')
        ->name('password.request');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest')
        ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->middleware('auth')
        ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('auth');

Route::post('/register/email', [RegisteredUserController::class, 'storeEmail'])
        ->middleware('guest')->name('register.email');

Route::post('/forgot-password-email', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

Route::post('/login-email', [AuthenticatedSessionController::class, 'loginByEmail'])
        ->middleware('guest');

Route::post('/resend-code-email', [RegisteredUserController::class, 'resendCodeEmail'])
        ->middleware('guest')
        ->name('resendCode.email');

Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->middleware('auth')
        ->name('verification.notice');

Route::post('/verify-code-email', [RegisteredUserController::class, 'verifyCodeEmail'])
        ->middleware('guest')
        ->name('verifyCode.email');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])//,'auth'
        ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');

Route::post('/register/phone', [RegisteredUserController::class, 'storePhone'])
        ->middleware('guest')->name('register.phone');

Route::post('/forgot-password-phone', [PasswordResetLinkController::class, 'storePhone'])
        ->middleware('guest')
        ->name('password.phone');

Route::post('/login-phone', [AuthenticatedSessionController::class, 'loginByPhone'])
        ->middleware('guest')
        ->name('login.phone');

Route::post('/resend-code-phone', [RegisteredUserController::class, 'resendCodePhone'])
        ->middleware('guest')
        ->name('resendCode.phone');

Route::post('/verify-code-phone', [RegisteredUserController::class, 'verifyCodePhone'])
        ->middleware('guest')
        ->name('verifyCode.phone');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
