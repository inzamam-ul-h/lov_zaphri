@extends('frontend.layouts.guest')

@section('content')

<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>Club</h1>

                <ul class="ritekhela-breadcrumb">

                    <li><a href="{{url('/home')}}">Home</a></li>

                    <li>Club</li>


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

                                <img src="{{ asset_url('images/club-registration.jpg') }}" alt="" style="width: 100%;">


                            </li>

                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Start today!</a></h2>

                                    <span>Register</span>

                                    <p>
                                        After you sent a request, Zaphri will work with you to ensure authenticity of accounts, onboarding process, and Q/As. We want to ensure that your club has a remarkable experience and both your coaches and athletes are not matched with impersonators.
                                    </p>

                                </div>

                            </li>



                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Provide your coaches proper training</a></h2>

                                    <span>Training</span>

                                    <p>
                                        Often times coaches do not have enough time to align with your club’s philosophy and expected standard of training. Through a series of videos that you create, you will be able to “coach the coaches”!
                                    </p>

                                </div>

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/training.png') }}" alt="" style="width: 100%;">



                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/deliver.png') }}" alt="" style="width: 100%;">



                            </li>

                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">Training sessions</a></h2>

                                    <span>Sessions</span>

                                    <p>
                                        You can also provide training videos for all your coaches and athletes. All Coaches will have the ability to create their personalized and printable training session using our quick and simple application.
                                    </p>

                                </div>

                            </li>



                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">It pays for itself</a></h2>

                                    <span>Pay</span>

                                    <p>
                                        You will get a commission from each paid interaction from athletes in your club.
                                    </p>
                                    <br>

                                </div>

                            </li>

                            <li class="col-md-6">

                                <img src="{{ asset_url('images/payments.png') }}" alt="" style="width: 100%;">

                            </li>


                            <li class="col-md-6">

                                <img src="{{ asset_url('images/safe.png') }}" alt="" style="width: 100%;">



                            </li>

                            <li class="col-md-6">

                                <div class="ritekhela-team-view3-text">

                                    <h2><a href="#">It is transparent and safe</a></h2>

                                    <span>Transparent</span>

                                    <p>
                                        We will work only with coaches with background check complete and we will allow parents to monitor their children’s activity

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