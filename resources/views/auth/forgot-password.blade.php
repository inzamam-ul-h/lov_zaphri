@extends('layouts.guest')

@section('content')
<section class="pxp-hero vh-100" style="background-color: var(--pxpMainColorLight);">
<div class="row align-items-center pxp-sign-hero-container">
<div class="col-xl-6 pxp-column">
<div class="pxp-sign-hero-fig text-center pb-100 pt-100">
<img src="{{ asset_url('images/signin-fig.png') }}" alt="forgot password">
<h1 class="mt-4 mt-lg-5">Welcome back!</h1>
</div>
</div>
<div class="col-xl-6 pxp-column pxp-is-light">
<div class="pxp-sign-hero-form pb-100 pt-100">
<div class="row justify-content-center">
    <div class="col-lg-6 col-xl-7 col-xxl-5">
        <div class="pxp-sign-hero-form-content">
            <h5 class="text-center">Forgot Password</h5>
            @include('flash::message')
            @include('coreui-templates::common.errors')
            <form class="mt-4" form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-floating mb-3">
                     <input type="email" id="email" name="email" class="form-control {{ $errors->has('email')?'is-invalid':'' }}" value="{{ old('email') }}" placeholder="Email" tabindex="1" required>
                    <label for="pxp-signin-page-email">Enter Email address</label>
                    <span class="fa fa-envelope-o"></span>
                </div>
                <button type="submit" role="button" class="btn rounded-pill pxp-sign-hero-form-cta" style="width: 100%">Email Password Reset Link
                </button>
            </form>
        </div>
    </div>
</div>
</div>
</div>
</div>
</section>
@endsection
    
