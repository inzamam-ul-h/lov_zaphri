@extends('frontend.layouts.guest')

@section('content')
<div class="ritekhela-subheader">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Contact Us</h1>
                <ul class="ritekhela-breadcrumb">
                    <li><a href="{{url('/home')}}">Home</a></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$Site_Phone = Site_Settings($Settings, 'phone');

$Site_Email = Site_Settings($Settings, 'email');

$Site_Address = Site_Settings($Settings, 'address');

$Site_Whatsapp = Site_Settings($Settings, 'whatsapp');
?>

<div class="ritekhela-main-content">

    <div class="ritekhela-main-section ritekhela-contact-map-full">
        <div class="container-fluid">
            <div class="row">

                <div class="ritekhela-contact-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13606.8196963411!2d74.3235584!3d31.504793599999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m3!3e6!4m0!4m0!5e0!3m2!1sen!2s!4v1564568940172!5m2!1sen!2s" height="450"></iframe>
                </div>

            </div>
        </div>
    </div>

    <div class="ritekhela-main-section ritekhela-fixture-list-full">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="ritekhela-fancy-title-two">
                        <h2>Contact Information</h2>
                    </div>
                    <div class="ritekhela-contact-list">
                        <ul class="row">
                            <li class="col-md-3">
                                <i class="fa fa-phone"></i>
                                <span><?php echo $Site_Phone; ?></span>

                            </li>
                            <li class="col-md-3">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo $Site_Email; ?></span>

                            </li>
                            <li class="col-md-3">
                                <i class="fa fa-map-marker-alt"></i>
                                <span><?php echo $Site_Address; ?></span>
                            </li>
                            <li class="col-md-3">
                                <i class="fa fa-fax"></i>
                                <span><?php echo $Site_Whatsapp; ?></span>

                            </li>
                        </ul>
                    </div>

                    <div class="ritekhela-fancy-title-two">
                        <h2>Contact Here</h2>
                    </div>

                    <div class="ritekhela-form">
                        @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif
                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif
                        @if ($message = Session::get('warning'))
                        <div class="alert alert-warning alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif
                        @if ($message = Session::get('info'))
                        <div class="alert alert-info alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            Check the following errors :(
                        </div>
                        @endif


                        <form method="post" action="{{ route('ContactPageSubmit') }}" role="form" name="contact_form" id="contact_form" novalidate>
                            @csrf
                            <p>
                                <input type="text" placeholder="Your Name" id="contact_name" name="contact_name">
                            </p>
                            <p>
                                <input type="text" placeholder="Email" id="contact_email" name="contact_email">
                            </p>
                            <p>
                                <input type="text" placeholder="Contact" id="contact_phone" name="contact_phone">
                            </p>
                            <p class="ritekhela-comment">
                                <textarea placeholder="Comment" id="contact_comment" name="contact_comment"></textarea>
                            </p>
                            <p class="ritekhela-submit" style="display:inline">
                                <input type="submit" id="contact_submit" name="submit_contact" value="Send Now" class="ritekhela-bgcolor">
                            </p>
                            <p id="contact_msg_success" class="msg-gr"></p>

                            <p id="contact_msg_error" class="msg-rd"></p>

                        </form>
                    </div>

                </div>


            </div>
        </div>
    </div>


</div>


@endsection