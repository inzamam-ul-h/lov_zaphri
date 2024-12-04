@extends('frontend.layouts.guest')
@php
$SITE_URL = env('APP_URL');
$AUTH_USER = Auth::user();
@endphp
@section('content')
<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">
                <h1>Profile</h1>
                <ul class="ritekhela-breadcrumb">
                    <li><a href="<?php echo $SITE_URL; ?>">Home</a></li>
                    <li>Profile</li>
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

                                    <div class="col-md-12">
                                        <div class="ritekhela-fixture ritekhela-modren-fixture">

                                            <div class="ritekhela-main-section ritekhela-fixture-list-full">
                                                <div class="container">
                                                    <div class="row">

                                                        <!--// Full Section //-->
                                                        <div class="col-md-12">
                                                            <?php
                                                            if ($User == null) {
                                                                ?>
                                                                <h3 class="text-center">No data to display</h3>
                                                                <?php
                                                            }
                                                            else {


                                                                $email = $User->email;
                                                                $phone_no = $User->phone;
                                                                $email_verified = $User->email_verified;
                                                                $phone_no_verified = $User->phone_no_verified;

                                                                $image = $User->coachpic;
                                                                $image_path = '/uploads/images/';
                                                                if ($image == null) {
                                                                    $image = "dummy-profile.png";
                                                                    $image_path = '/assets/images/';
                                                                }
                                                                $zip_code = $User->zip_code;
                                                                $gender = $User->gender;
                                                                $about_me = $User->about_me;
                                                                ?>
                                                                <div class="ritekhela-player-thumb-wrap row">
                                                                    <div class="col-md-4"
                                                                         style="padding-left: 0px;padding-right: 0px; ">

                                                                        <figure
                                                                            style="height: 100%;width: 100%;object-fit: cover; margin-bottom: 0px;margin-left: 0px;">
                                                                            {{-- <img src="<?php // echo $SITE_URL;  ?><?php // echo $image_path . $image;  ?>" --}}
                                                                            <img src="{{ asset_url('images/dummy-profile.png') }}"
                                                                                 alt=""
                                                                                 style="height: 100%;width: 100%;object-fit: cover;">
                                                                        </figure>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <div class="ritekhela-player-thumb-text">
                                                                            <br>
                                                                            <h3><span><?php echo get_user_name($User->id); ?></span></h3>
                                                                            <ul class="ritekhela-player-info">
                                                                                <li>
                                                                                    <h5>Type:</h5>
                                                                                    <span>
    <?php
    $user_type = $User->user_type;
    if ($user_type == 1) {
        echo 'Coach';
    }
    elseif ($user_type == 2) {
        echo 'Player';
    }
    elseif ($user_type == 3) {
        echo 'Club';
    }
    elseif ($user_type == 4) {
        echo 'Parent';
    }
    ?>
                                                                                    </span>
                                                                                </li>
    <?php
    if ($user_type == 1 || $user_type == 2) {
        ?>
                                                                                    <li>
                                                                                        <h5>Zip Code:</h5>
                                                                                        <span><?php echo $zip_code; ?></span>
                                                                                    </li>
        <?php
    }
    elseif ($user_type == 3) {
        ?>
                                                                                    <li>
                                                                                        <h5>Reg No:</h5>
                                                                                        <span><?php echo get_user_profile_data('reg_no', $User->id); ?></span>
                                                                                    </li>
        <?php
    }
    ?>
                                                                                <?php
                                                                                if ($email_verified == 1) {
                                                                                    ?>
                                                                                    <li>
                                                                                        <h5>Email:</h5>
                                                                                        <span><?php echo $email; ?></span>
                                                                                    </li>
        <?php
    }
    ?>
                                                                                <?php
                                                                                if ($phone_no_verified == 1) {
                                                                                    ?>
                                                                                    <li>
                                                                                        <h5>Phone No:</h5>
                                                                                        <span><?php echo $phone_no; ?></span>
                                                                                    </li>
        <?php
    }
    ?>
                                                                                <?php
                                                                                if ($user_type == 1 || $user_type == 2) {
                                                                                    ?>
                                                                                    <li>
                                                                                        <h5>Gender:</h5>
                                                                                        <span><?php echo $gender; ?></span>
                                                                                    </li>
                                                                                    <li>
                                                                                        <h5>About:</h5>
                                                                                        <span><?php echo $about_me; ?></span>
                                                                                    </li>
        <?php
    }
    elseif ($user_type == 3) {
        ?>
                                                                                    <li>
                                                                                        <h5>Address:</h5>
                                                                                        <span><?php echo get_user_profile_data('address', $User->id); ?></span>
                                                                                    </li>
        <?php
    }
    ?>


                                                                            </ul>
                                                                        </div>
                                                                    </div>


                                                                </div>
    <?php
}
?>
                                                        </div>
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

                </div>



            </div>

        </div>

    </div>


</div>
@endsection
