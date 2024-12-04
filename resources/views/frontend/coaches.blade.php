@extends('frontend.layouts.guest')

@section('content')

<div class="ritekhela-subheader">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Coach</h1>
                <ul class="ritekhela-breadcrumb">
                    <li><a href="{{url('/home')}}">Home</a></li>
                    <li>Coach</li>
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

                    <div class="ritekhela-fixture ritekhela-modren-fixture">

                        <ul class="row">

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/coach-registration.jpg') }}" alt="" style="width: 100%;">



                            </li>

                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Create an account</a></h2>

                                    <span>Register</span>

                                    <p>
                                        It is easy and intuitive. Update your profile and the system will take care of most of the rest, such as forwarding your request to be associated to your club, matching you with the right age group, finding athletes in your area, etc.
                                    </p>
                                </div>

                            </li>



                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Schedule your sessions</a></h2>

                                    <span>Sessions</span>

                                    <p>
                                        You can schedule individual sessions or group sessions and decide your price! Reminders will be available for both you and the coaches, once the sessions are about to start
                                    </p>

                                </div>

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/manage.png') }}" alt="" style="width: 100%;">



                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/payments.png') }}" alt="" style="width: 100%;">

                            </li>

                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Get paid and get feedback</a></h2>

                                    <span>Feedback</span>

                                    <p>
                                        Money will be automatically deposited	In your account, once the session is	successfully delivered. You will provide	feedback to the athlete and they will rate	You!
                                    </p>

                                </div>

                            </li>



                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Receive all the benefits from your club</a></h2>

                                    <span>Benefits</span>

                                    <p>
                                        You will be able to personalize your practice based on videos and printable documents from your club. Once you are approved, you will be automatically visible to the athletes in your club
                                    </p>
                                </div>

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/deliver.png') }}" alt="" style="width: 100%;">

                            </li>



                        </ul>

                    </div>



                </div>



            </div>

        </div>

    </div>



</div>

@endsection