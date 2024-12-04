@extends('frontend.layouts.guest')

@php
$SITE_URL = env('APP_URL');
@endphp

@section('content')
<div id="absoluteform" style="margin-top:20px">

    <div class="form-column2 col-lg-9">
        <div class="tabs-container">

            <ul class="nav nav-tabs">

                <li id="tab_1_li" class="tesrt active"><a data-toggle="tab" href="#tab-1" id="tab_1_link">Player</a></li>

                <li id="tab_2_li" class="tesrt "><a data-toggle="tab" href="#tab-2" id="tab_2_link">Coach</a></li>

                <li id="tab_3_li" class="tesrt"><a data-toggle="tab" href="#tab-3" id="tab_3_link">Club</a></li>

            </ul>

            <div class="tab-content">

                <div id="tab-2" class="tab-pane ">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="line_spacing">Personalized Coaching For Better Possibilities</h1>

                                <div class="clearfix"></div>
                                <?php
                                if (!Auth::user()) {
                                    ?>
                                    <a href="#" data-toggle="modal" data-target="#modalSignup"
                                       class="btn btn-primary">Sign up Today</a>
                                       <?php
                                   }
                                   else {
                                       ?>
                                    <a href="<?php //echo $MANAGE_URL;
                                       ?>/home" class="btn btn-primary">My Zaphri</a>
                                       <?php
                                   }
                                   ?>

                            </div>
                        </div>
                    </div>
                </div>


                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="line_spacing">Virtual Coaching For Tomorrow's Champions</h1>
                                <div class="clearfix"></div>

<?php
if (!Auth::user()) {
    ?>
                                    <a href="#" data-toggle="modal" data-target="#modalSignup"
                                       class="btn btn-primary">Sign up Today</a>
                                    <?php
                                }
                                else {
                                    ?>
                                    <a href="<?php //echo $MANAGE_URL;
                                    ?>/home" class="btn btn-primary">My Zaphri</a>
                                       <?php
                                   }
                                   ?>

                            </div>
                        </div>
                    </div>
                </div>



                <div id="tab-3" class="tab-pane">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="line_spacing">We Succeed Because We Do It Together</h1>
                                <div class="clearfix"></div>

<?php
if (!Auth::user()) {
    ?>
                                    <a href="#" data-toggle="modal" data-target="#modalSignup"
                                       class="btn btn-primary">Sign up Today</a>
                                    <?php
                                }
                                else {
                                    ?>
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">My Zaphri</a>
                                       <?php
                                   }
                                   ?>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="ritekhela-banner-one">

    <div class="ritekhela-banner-one-layer">
        <span class="ritekhela-banner-transparent"></span>
        <img src="{{ asset_url('extra-images/banner-2.jpg') }}" alt="">
    </div>
    <div class="ritekhela-banner-one-layer">
        <span class="ritekhela-banner-transparent"></span>
        <img src="{{ asset_url('extra-images/banner-1.jpg') }}" alt="">
    </div>

</div>


<div class="ritekhela-main-content">

    <div class="ritekhela-main-section ritekhela-fixture-slider-full">

        <div class="container">

            <div class="row">

                <div class="col-md-8">

                    <div class="ritekhela-fancy-title-two">

                        <h2>About Us</h2>

                    </div>

                    <div class="authore-wrap">

                        <figure>

                            <a href="<?php echo $SITE_URL; ?>">

                                <img alt="about" src="{{ asset_url('extra-images/about-match-1.jpg') }}">

                            </a>

                        </figure>

                        <div class="ritekhela-authore-info">

                            <h2><a href="#">Connecting more players, Creating more champions</a></h2>

                            <p><?php echo get_contact_details_data('about_zaphry'); ?></p>

                        </div>

                    </div>



                    <div class="ritekhela-fancy-title-two">

                        <h2>Popular Coaches</h2>

                    </div>



                    <div class="ritekhela-team ritekhela-team-view1">

<?php
$counter = 0;

foreach ($coaches as $key => $data) {


    $coach_id = $data['id'];

    $coach_name = get_user_name($coach_id);

    $public_url = $data['public_url'];

    $public_url = $SITE_URL . '/' . $public_url;

    $about_me = $data["about_me"];

    $coachpic = $data["coachpic"];

    if ($counter == 0) {

        echo '<ul class="row">';
    }
    elseif ($counter % 3 == 0) {

        echo '</ul><ul class="row">';
    }
    ?>

                            <li class="col-md-4">



                                <figure>

                                    <a href="<?php echo $public_url; ?>" title="View Sessions by <?php echo $coach_name; ?>">

                            <?php
                            if (!empty($coachpic)) {
                                ?>

                                            <img src="<?php echo $SITE_URL; ?>/uploads/images/<?php echo $coachpic; ?>" alt="image"
                                                 style="height:316px">

                                <?php
                            }
                            else {
                                ?>

                                            <img src="{{ asset_url('images/dummy-profile.png') }}" alt="image"
                                                 style="height:316px">

        <?php
    }
    ?>

                                    </a>

                                </figure>

                                <div class="ritekhela-team-view1-text">



                                    <h2>

                                        <a href="<?php echo $public_url; ?>" title="View Sessions by <?php echo $coach_name; ?>">

    <?php echo $coach_name; ?>

                                        </a>

                                    </h2>

                                    <span><?php echo get_user_rating($coach_id); ?></span>



                                    <p><?php echo $about_me; ?></p>



                                    <a href="<?php echo $public_url; ?>" class="ritekhela-team-view1-btn">

                                        View Calendar <i class="fa fa-angle-right"></i>

                                    </a>

                                </div>



                            </li>

                                        <?php
                                        $counter++;
                                    }

                                    if ($counter > 0) {

                                        echo '</ul>';
                                    }
                                    ?>

                    </div>


                </div>



                <aside class="col-md-4">


                    @include('frontend.layouts.guest.newsletter')


                </aside>




            </div>

        </div>

    </div>

</div>
@endsection


@push('scripts')
<script type="text/javascript">
    $(document).ready(function (e) {



        $("#tab_1_link").click(function (e) {

            $('#tab_1_li').removeClass('active');

            $('#tab_2_li').removeClass('active');

            $('#tab_3_li').removeClass('active');



            $('#tab-1').removeClass('active');

            $('#tab-2').removeClass('active');

            $('#tab-3').removeClass('active');



            $('#tab_1_li').addClass('active');

            $('#tab-1').addClass('active');

        });



        $("#tab_2_link").click(function (e) {

            $('#tab_1_li').removeClass('active');

            $('#tab_2_li').removeClass('active');

            $('#tab_3_li').removeClass('active');



            $('#tab-1').removeClass('active');

            $('#tab-2').removeClass('active');

            $('#tab-3').removeClass('active');



            $('#tab_2_li').addClass('active');

            $('#tab-2').addClass('active');

        });



        $("#tab_3_link").click(function (e) {

            $('#tab_1_li').removeClass('active');

            $('#tab_2_li').removeClass('active');

            $('#tab-3_li').removeClass('active');



            $('#tab-1').removeClass('active');

            $('#tab-2').removeClass('active');

            $('#tab-3').removeClass('active');



            $('#tab_3_li').addClass('active');

            $('#tab-3').addClass('active');

        });







        $(function () {

            $("form[name='contact_request_form']").validate({

                rules: {

                    query_email: {

                        required: true,

                        email: true

                    },

                    query_name: {

                        required: true,

                    },

                    query_contact: {

                        required: true,

                    }

                },

                messages: {

                    query_email: "Please enter a valid Email address",

                    query_name: {

                        required: "Please enter your Name",

                    },

                    query_contact: {

                        required: "Please enter your Contact",

                    }

                },

                submitHandler: function (form) {

                    contact_request_submit();

                }

            });

        });

    });


    function contact_request_submit() {

        var query_name = $('#query_name').val();

        var query_email = $('#query_email').val();

        var query_contact = $('#query_contact').val();



        {

            $.ajax({

                url: "<?php //echo $SITE_URL;
                                    ?>/Ajax_common.php",

                data: "type=register_contact&query_name=" + query_name + "&query_email=" + query_email +
                        "&query_contact=" + query_contact,

                type: "POST",

                success: function (response) {

                    //                                console.log(response);

                    if (response == 'success') {

                        $('#query_msg_success').html("Your request is registered successfully");

                        $('#query_msg_success').show();

                        $('#query_msg_error').hide();
                        ;

                    }
                    else if (response == 'error') {

                        $('#query_msg_error').html("Your request is not registered due to some error");

                        $('#query_msg_error').show();

                        $('#query_msg_success').hide();

                    }

                    $('#query_name').val("");

                    $('#query_email').val("");

                    $('#query_contact').val("")

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
