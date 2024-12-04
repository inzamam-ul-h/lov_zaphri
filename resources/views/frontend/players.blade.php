@extends('frontend.layouts.guest')

@section('content')

<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>Player</h1>

                <ul class="ritekhela-breadcrumb">

                    <li><a href="{{url('/home')}}">Home</a></li>

                    <li>Player</li>

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

                                <img src="{{ asset_url('images/player-registration.jpg') }}" alt="" style="width: 100%;">



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
                                        You can schedule individual sessions or group sessions and decide your price! Reminders will be available for both you and the athletes, once the sessions are about to start
                                    </p>
                                </div>

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/booking.png') }}" alt="" style="width: 100%;">



                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/payments.png') }}" alt="" style="width: 100%;">

                            </li>

                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Payment is easy as 1,2,3</a></h2>

                                    <span>Payment</span>

                                    <p>
                                        We leverage most of the traditional online payment methods such as credit cards, debit cards, Paypal and more
                                    </p>

                                </div>

                            </li>



                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Rate and review the coaches</a></h2>

                                    <span>Review</span>

                                    <p>
                                        Your feedback matters! We strive to always find the best coaches for you at the	best price.
                                    </p>

                                </div>

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/review.png') }}" alt="" style="width: 100%;">

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/deliver.png') }}" alt="" style="width: 100%;">

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



                        </ul>

                    </div>



                </div>



            </div>

        </div>

    </div>

</div>

@endsection