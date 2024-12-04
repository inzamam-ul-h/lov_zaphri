@extends('layouts.guest')

@section('content')


    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        
            <div class="login-brand login-brand-color">
                <img alt="image" src="{{ asset_url('img/logo.png') }}" />
                {{ env('APP_NAME', 'Zaphri') }}
            </div>
            
            <div class="card card-auth">
            
                <div class="card-header card-header-auth">
                    <h4>Registration Request received.</h4>
                </div>
                
                <div class="card-body">
                    <i class="far fa-check-circle display-2 my-3 text-success"></i>
                    <p>Dear User, thank you for showing interest and signing up on our platform. Your request has been received and is currently under review. One of our executives will contact you soon for onboarding.</p>
    
                    <div class="mb-4 text-muted">
                        Already onBoard? <a href="{{ url('/login') }}">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection