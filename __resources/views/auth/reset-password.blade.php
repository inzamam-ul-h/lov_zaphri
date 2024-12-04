@extends('frontend.layouts.guest')

@section('css_after')
    <style>

        .alert{
            margin-bottom: 0;  
        }
    </style>
@endsection

@section('content')

<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>Reset Password</h1>

                <ul class="ritekhela-breadcrumb">

                    <li><a href="{{url('/home')}}">Home</a></li>

                    <li>Reset Password</li>


                </ul>

            </div>

        </div>

    </div>

</div>

<div class="ritekhela-main-content">

    <div class="ritekhela-main-section ritekhela-fixture-list-full">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="ritekhela-fancy-title-two">
                        <h2>Reset Password</h2>
                    </div>
                    <div class="ritekhela-form">

                        @include('flash::message')
                        @include('coreui-templates::common.errors')
                            
                
                        <form name="settings_form" method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="content-group">

                                <div class="row">
                                    <div class="col-12">
                                        <p>
                                            <span id="error_message">
                                            </span>
                                        </p>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <input type="email" id="email" name="email" readonly class="form-control"
                                                    value="{{ $request->email }}" placeholder="Email" tabindex="1" required>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">New Password</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Password" tabindex="1" required>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Confirm New Password</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control" placeholder="Confirm Password" tabindex="1" required>

                                                </div>

                                            </div>

                                        </div>



                                        <div class="form-group row">

                                            <div class="col-sm-offset-4 col-sm-4">

                                                <input type="submit" id="contact_submit" name="submit_contact" value="Reset Password" class="btn btn-primary">

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </form>
                    </div>

                </div>


            </div>
        </div>
    </div>


</div>
@endsection
