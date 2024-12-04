@php
$SITE_URL = env('APP_URL');
$AUTH_USER = Auth::user();
@endphp

@extends('frontend.layouts.guest')

@section('content')
<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>Events</h1>

                <ul class="ritekhela-breadcrumb">

                    <li><a href="{{ url('/home') }}">Home</a></li>

                    <li>Events</li>



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



                        <div class="ritekhela-main-section ritekhela-fixture-list-full">

                            <div class="container">

                                <div class="row">



                                    <!--// Full Section //-->
                                    @if ($event == null)
                                    <div class="col-lg-12" style="text-align: center">

                                        <h2>No Event Found</h2>

                                    </div>
                                    @else
                                    @php

                                    $event_user_id = $event['user_id'];

                                    $event_id = $event['id'];
                                    $title = $event['title'];

                                    $description = $event['description'];

                                    $banner = $event['banner'];

                                    $age_group = $event['age_group'];

                                    $start_date_time = $event['start_date_time'];

                                    $video = $event['video'];

                                    $inquiry_status = $event['inquiry_status'];

                                    $attachments = $event['documents'];

                                    if ($attachments != '') {
                                    $attachments = explode(',', $attachments);
                                    }

                                    @endphp
                                    <input type="text" id="event_id" value="<?php echo $event->id; ?>"
                                           style="display: none">

                                    <div id="light">

                                        <a class="boxclose" id="boxclose" onclick="lightbox_close();"></a>

                                        <video id="VisaChipCardVideo" width="600" controls>

                                            <source src="{{ asset(upload_url( 'events/'.$event->id.'/'.$event->video) )}}"
                                                    type="video/mp4">

                                            <!--Browser does not support <video> tag -->

                                        </video>

                                    </div>

                                    <div class="col-md-8">

                                        <!--// Fixture Detail List //-->

                                        <figure class="ritekhela-fixture-detail">

                                            <img src="{{ asset(upload_url( 'events/'.$event->id.'/'.$event->banner) )}}"
                                                 alt="Banner" >

                                            <ul class="ritekhela-blog-options">

                                                <li><a href="#"><i class="far fa-user"></i> By
                                                        <?php echo get_user_name($event_user_id); ?>
                                                    </a></li>

                                                <li><i class="far fa-calendar-alt"></i>
                                                    <?php echo date('m/d/Y', $start_date_time); ?>
                                                </li>

                                            </ul>

                                        </figure>

                                        @if ($start_date_time > time())
                                        <div>
                                            @if (isset($AUTH_USER) && ($AUTH_USER->user_type == '1' || $AUTH_USER->user_type == '2'))
                                            <a id="not_interested" onclick="not_interested()" class="btn"
                                               style="margin-left: 10px;background-color: red; float: right;<?php
                                               if ($eventIntrest == null) {
                                                   echo 'display:none';
                                               }
                                               ?>"><span
                                                    style="color: white"><i class="far fa-heart"></i>
                                                    Not Interested</span></a>

                                            {{-- @dd($eventIntrest) --}}

                                            <a id="interested" onclick="interested()" class="btn"
                                               style="margin-left: 10px;background-color: #3e454c; float: right;<?php
                                               if ($eventIntrest != null) {
                                                   echo 'display:none';
                                               }
                                               ?>"><span
                                                    style="color: white"><i class="far fa-heart"></i>
                                                    Interested</span></a>
                                            @endif
                                        </div>
                                        @if ($inquiry_status == 1)
                                        <div>

                                            <a class="btn" href="#" data-toggle="modal"
                                               data-target="#ritekhelamodalinquiry"
                                               style="background-color: #3e454c; color: white; float: right;">
                                                Inquire Now</a>

                                        </div>
                                        @endif
                                        @else
                                        @endif

                                        <div class="ritekhela-editor-detail">

                                            <h2>
                                                <?php echo $title; ?>
                                            </h2>

                                            <p>
<?php echo $description; ?>
                                            </p>

                                            <br>

                                        </div>

                                    </div>

                                    <aside class="col-md-4">

                                        <div id="fade" onClick="lightbox_close();"></div>

                                        <div>

                                            <a href="#" onclick="lightbox_open();">

                                                <div class="col-xs-12">
                                                    <img class="ItemImage"
                                                         src="{{ asset(upload_url( 'events/'.$event->id.'/'.$event->banner) )}}"
                                                         alt="Video" style="filter: brightness(50%)" />

                                                    <img class="OverlayIcon"
                                                         src="{{ $SITE_URL }}/assets/frontend/images/2.png"
                                                         alt="" />

                                                </div>

                                            </a>

                                        </div>

                                        <br>
                                        @if ($attachments != '')
                                        <h3>Attachments</h3>
                                        @foreach ($attachments as $attachment)
                                        <a href="{{ $attachment}}"
                                           target="_blank">

                                            <i class="far fa-file"></i>
<?php echo ' ' . $attachment; ?>

                                        </a>

                                        <br>
                                        @endforeach
                                        @endif

                                        <br>
                                        @include('frontend.layouts.guest.newsletter')
                                    </aside>
                                    @endif

                                    <!--// Full Section //-->

                                </div>

                            </div>

                        </div>



                    </div>

                </div>



            </div>

        </div>

    </div>


</div>

<div class="loginmodalbox modal fade" id="ritekhelamodalinquiry" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
        <div class="modal-content">
            <div class="modal-body ritekhela-bgcolor-two">
                <h5 class="modal-title">Submit Your Inquiry</h5>
                <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </a>
                {{-- <form class="loginmodalbox-search" action="{{ route('eventsInquery') }}" --}}

                <form class="loginmodalbox-search" action="{{route('eventsInquery',$event_id) }}" method="POST">
                    @csrf
                    <div class="col-xs-12">
                        <label class="search-modal-label">Inquiry Text</label>
                        <textarea name="inquiry" id="inquiry" rows="8" style="height: 100px"></textarea>
                    </div>

                    <input type="submit" name="submit_inquiry" value="Inquire Now" class="ritekhela-bgcolor">
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
    window.document.onkeydown = function (e) {

        if (!e) {

            e = event;

        }

        if (e.keyCode == 27) {

            lightbox_close();

        }

    }



    function lightbox_open() {

        var lightBoxVideo = document.getElementById("VisaChipCardVideo");

        window.scrollTo(0, 0);

        document.getElementById('light').style.display = 'block';

        document.getElementById('fade').style.display = 'block';

        lightBoxVideo.play();

    }



    function lightbox_close() {

        var lightBoxVideo = document.getElementById("VisaChipCardVideo");

        document.getElementById('light').style.display = 'none';

        document.getElementById('fade').style.display = 'none';

        lightBoxVideo.pause();

    }



    function interested() {

        document.getElementById("interested").style.display = "none";

        var event_id = document.getElementById("event_id").value;



        var type = 'event_interested';

        {

            $.ajax({

                url: "{{ url('/events/intrested/') }}" + '/' + event_id,

                // data: "event_id=" + event_id,

                type: "GET",

                success: function (response) {

                    console.log(response)

                    data = (response);

                    status = data.status;

                    if (status == 'success') {

                        document.getElementById("not_interested").style.display = "block";

                    }
                    else if (status == 'error') {

                        document.getElementById("interested").style.display = "block";

                    }

                }

            });

        }



    }



    function not_interested() {

        document.getElementById("not_interested").style.display = "none";





        var event_id = document.getElementById("event_id").value;



        var type = 'event_not_interested';



        {

            $.ajax({

                url: "{{ url('/events/not-intrested/') }}" + '/' + event_id,

                type: "GET",

                success: function (response) {

                    console.log(response)

                    data = (response);

                    status = data.status;

                    if (status == 'success') {

                        document.getElementById("interested").style.display = "block";

                    }
                    else if (status == 'error') {

                        document.getElementById("not_interested").style.display = "block";

                    }

                }

            });

        }



    }

    $(function () {

        $("form[name='newsletter_form']").validate({

            rules: {

                subscribe_email: {

                    required: true,

                    email: true

                },

                subscribe_name: {

                    required: true,

                },

                subscribe_contact: {

                    required: true,

                }

            },

            messages: {

                subscribe_email: "Please enter a valid Email address",

                subscribe_name: {

                    required: "Please enter your Name",

                },

                subscribe_contact: {

                    required: "Please enter your Contact",

                }

            },

            submitHandler: function (form) {

                newsletter_submit();

            }

        });

    });



    function newsletter_submit() {

        var subscribe_name = $('#subscribe_name').val();

        var subscribe_email = $('#subscribe_email').val();

        var subscribe_contact = $('#subscribe_contact').val();

        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({

                url: "{{ url('subscribe-news') }}",

                data: "type=subscription&subscribe_name=" + subscribe_name + "&subscribe_email=" +
                        subscribe_email + "&subscribe_contact=" + subscribe_contact,

                type: "POST",

                success: function (response) {

                    var status = response.status;
                    console.log(response);

                    if (status == 'success') {

                        $('#subscribe_msg_success').html("Your have subscribe successfully");

                        $('#subscribe_msg_success').show();

                        $('#subscribe_msg_error').hide();

                    }
                    else if (status == 'error') {

                        $('#subscribe_msg_error').html("Your are not subscribed due to some error");

                        $('#subscribe_msg_error').show();

                        $('#subscribe_msg_success').hide();

                    }
                    else if (status == 'exist') {
                        console.log('Subscribed');
                        $('#subscribe_msg_error').html("This email have already subscribed");

                        $('#subscribe_msg_error').show();

                        $('#subscribe_msg_success').hide();

                    }

                    $('#subscribe_name').val("");

                    $('#subscribe_email').val("");

                    $('#subscribe_contact').val("");



                }

            });

        }

    }
</script>
@endpush
