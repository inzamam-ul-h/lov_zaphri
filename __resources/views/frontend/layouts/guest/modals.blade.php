<?php
if (!Auth::user()): ?>
    <div class="loginmodalbox modal fade" id="modalSignupEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content" >
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Sign Up With Email Now</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" method="POST" id="emailSignupForm" role="form" action="" name="registration" novalidate="novalidate">
                        @method('POST')
                        <p id="signup_msg_success" class="msg-white"></p>

                        <p id="signup_msg_error" class="msg-rd"></p>

                        <select name="user_type" id="user_type">
                            <option value="2">Player</option>
                            <option value="1">Coach</option>
                            <option value="3">Club</option>
                        </select>

                        <input type="email" name="email" id="email_2" placeholder="Please enter your Email address">

                        <input type="password" name="password" id="password_2" placeholder="password">

                        <input id="btn_signup" type="submit" value="Sign Up" class="ritekhela-bgcolor" name="signup">

                        <p>
                            <a href="#" id="modalSignup_5" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupPhone">
                                Signup with Phone Number
                            </a>
                            <br>
                            <a href="#" id="modalSignin_2" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginEmail">
                                Already have an account?
                            </a>
                            <br>
                            <a href="#" id="modalSignin_77" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalVerifyEmail">
                                Verify Email
                            </a>
                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalVerifyEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content" >
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Verify Email Now</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" id="emailVerifyForm" name="emailVerifyForm" novalidate="novalidate">
                        <p id="email_verification_msg_success" class="msg-white"></p>

                        <p id="email_verification_msg_error" class="msg-rd"></p>

                        <input type="text"  name="email_verification_email" id="email_verification_email">

                        <input type="number" name="email_verification_code" id="email_verification_code" placeholder="Please enter Code Here" minlength="4" maxlength="4">


                        <input id="btn_email_verify" type="submit" value="Verify" class="ritekhela-bgcolor" name="verify">

                        <p>
                            <a href="#" id="modalSignin_12" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSendCodeEmail">
                                Didn't Recieve code? Send Again.
                            </a>
                        </p>

                        <p>
                            <a href="#" id="modalSignin_11" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginEmail">
                                Already have an account?
                            </a>
                        </p>

                        <p>
                            <a href="#" id="modalSignup_22" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupEmail">
                                Do not have an account?
                            </a>
                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalSendCodeEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content" >
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Resend Code Again</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" id="email_resend_code" name="email_resend_code" novalidate="novalidate">
                        <p id="email_resend_code_msg_success" class="msg-white"></p>

                        <p id="email_resend_code_msg_error" class="msg-rd"></p>

                        <input type="text"  name="email_resend_code_email" id="email_resend_code_email">

                        <input id="btn_email_resend_code" type="submit" value="Resend Code" class="ritekhela-bgcolor" name="send_verification_code">

                        <p>

                            <a href="#" id="modalSignin_9" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalVerifyEmail">
                                Already have a verification code? Verify Now.
                            </a>

                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalForgotEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content">
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Forgot Password With Email</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" method="post" name="forgot_message" id="forgot-email" novalidate="novalidate">

                        <p id="forgot_msg_success" class="msg-white"></p>
                        <p id="forgot_msg_error" class="msg-rd"></p>

                        <input type="email" name="email" id="forget_email" placeholder="Please enter your Email address">

                        <input type="submit" value="Submit" class="ritekhela-bgcolor" name="signin">

                        <p>
                            <a href="#" id="modalSignin_18" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalForgotPhone">
                                Forgot Password With Phone No
                            </a>
                        </p>

                        <p>
                            <a href="#" id="modalSignin_1" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginEmail">
                                Remember Password?
                            </a>
                        </p>

                        <p>
                            <a href="#" id="modalSignup_2" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupEmail">
                                Do not have an account?
                            </a>
                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalLoginEmail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content">
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Login With Email</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" method="post" name="login" novalidate="novalidate">

                        <p id="login_msg_success" class="msg-white"></p>
                        <p id="login_msg_error" class="msg-rd"></p>

                        <input type="email" name="email" id="email" placeholder="Please enter your Email address">

                        <input type="password" name="password" id="password" placeholder="password">

                        <input type="submit" id="btn_login" value="Login Now" class="ritekhela-bgcolor" name="signin">
                        @csrf
                        <p>
                            <a href="#" id="modal_phone_1" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginPhone">
                                Login With Phone Number
                            </a>
                        </p>
                        <br>
                        <p>
                            <a href="#" id="modalforgot_1" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalForgotEmail">
                                Forget Password?
                            </a>
                        </p>
                        <br>
                        <p>
                            <a href="#" id="modalSignup_1" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupEmail">
                                Do not have an account?
                            </a>
                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalSignupPhone" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content" >
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Sign Up With Phone Number Now</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" name="phone_registration" novalidate="novalidate">
                        <p id="signup_phone_msg_success" class="msg-white"></p>

                        <p id="signup_phone_msg_error" class="msg-rd"></p>

                        <select name="user_type_phone" id="user_type_phone">
                            <option value="2">Player</option>
                            <option value="1">Coach</option>
                            <option value="3">Club</option>
                        </select>

                        <select name="phone_no_code" id="phone_no_code" class="form-control publicpart1" style="width: 50%">


                        </select>

                        <input type="number"  name="phone_no" id="phone_no" style="width: 50%">


                        <input type="password" name="password_phone" id="password_phone" placeholder="password">



                        <input id="signup_phone_no" type="submit" value="Sign Up" class="ritekhela-bgcolor" name="signup_phone_no">

                        <p>
                            <a href="#" id="modalSignup_6" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupEmail">
                                Signup with Email
                            </a>
                            <br>
                            <a href="#" id="modalSignin_17" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginPhone">
                                Already have an account?
                            </a>
                            <br>
                            <a href="#" id="modalSignin_8" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalVerifyPhone">
                                Verify Phone
                            </a>

                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalVerifyPhone" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content" >
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Verify Phone Number Now</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" name="phone_verification" novalidate="novalidate">
                        <p id="verification_msg_success" class="msg-white"></p>

                        <p id="verification_msg_error" class="msg-rd"></p>

                        <select name="verification_phone_no_code" id="verification_phone_no_code" class="form-control publicpart1" style="width: 40%">
                        </select>

                        <input type="number"  name="verification_phone_no" id="verification_phone_no" style="width: 60%">

                        <input type="number" name="verification_code" id="verification_code" placeholder="Please enter Code Here" minlength="4" maxlength="4">


                        <input id="btn_verify" type="submit" value="Verify" class="ritekhela-bgcolor" name="verify">

                        <p>

                            <a href="#" id="modalSignin_16" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSendCode">
                                Didn't Recieve code? Send Again.
                            </a>
                            <br>
                            <a href="#" id="modalSignin_10" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginPhone">
                                Already have an account?
                            </a>

                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalSendCodePhone" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content" >
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Resend Code Again</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" name="send_code_again" novalidate="novalidate">
                        <p id="send_code_msg_success" class="msg-white"></p>

                        <p id="send_code_msg_error" class="msg-rd"></p>

                        <select name="send_code_phone_no_code" id="send_code_phone_no_code" class="form-control publicpart1" style="width: 40%">
                        </select>

                        <input type="number"  name="send_code_phone_no" id="send_code_phone_no" style="width: 60%">

    <!--                        <input type="text" name="send_code_phone_no" id="send_code_phone_no" placeholder="Please enter your Phone Number">-->


                        <input id="send_verification_code" type="submit" value="Resend Code" class="ritekhela-bgcolor" name="send_verification_code">

                        <p>

                            <a href="#" id="modalSignin_15" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalVerifyPhone">
                                Already have a verification code? Verify Now.
                            </a>

                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalForgotPhone" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content">
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Forgot Password With Phone No</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" method="post" name="forgot_message_phone" novalidate="novalidate">

                        <p id="forgot_msg_phone_success" class="msg-white"></p>
                        <p id="forgot_msg_phone_error" class="msg-rd"></p>

                        <select name="forget_phone_code" id="forget_phone_code" class="form-control publicpart1" style="width: 40%">


                        </select>

                        <input type="number"  name="forget_phone" id="forget_phone" style="width: 60%">


                        <input type="submit" value="Submit" class="ritekhela-bgcolor" name="forgot_phone">

                        <p>
                            <a href="#" id="modalSignin_19" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalForgotEmail">
                                Forgot Password With Email
                            </a>
                        </p>

                        <p>
                            <a href="#" id="modalSignin_20" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginPhone">
                                Remember Password?
                            </a>
                        </p>

                        <p>
                            <a href="#" id="modalSignup_21" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupPhone">
                                Do not have an account?
                            </a>
                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="loginmodalbox modal fade" id="modalLoginPhone" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
            <div class="modal-content">
                <div class="modal-body ritekhela-bgcolor-two">

                    <h5 class="modal-title">Login With Phone Number</h5>

                    <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </a>

                    <form class="loginmodalbox-search" role="form" action="" method="post" name="login_phone" novalidate="novalidate">

                        <p id="phone_no_login_msg_success" class="msg-white"></p>
                        <p id="phone_no_login_msg_error" class="msg-rd"></p>

                        <select name="login_phone_no_code" id="login_phone_no_code" class="form-control publicpart1" style="width: 40%">


                        </select>

                        <input type="number"  name="login_phone_no" id="login_phone_no" style="width: 60%">

                        <input type="password" name="login_password" id="login_password" placeholder="password">

                        <input type="submit" value="Login Now" class="ritekhela-bgcolor" name="signin_phone_no">
                        <p>
                            <a href="#" id="modal_phone_2" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalLoginEmail">
                                Login With Email
                            </a>
                        </p>
                        <br>
                        <p>
                            <a href="#" id="modalforgot_2" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalForgotPhone">
                                Forget Password?
                            </a>
                        </p>
                        <br>
                        <p>
                            <a href="#" id="modalSignup_14" class="loginmodalbox-forget" data-toggle="modal" data-target="#modalSignupPhone">
                                Do not have an account?
                            </a>
                        </p>

                    </form>

                </div>
            </div>
        </div>
    </div>

<?php else:
    $user = Auth::user(); ?>

<?php endif; ?>

<div class="loginmodalbox modal fade" id="ritekhelamodalsearch" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
        <div class="modal-content">
            <div class="modal-body ritekhela-bgcolor-two">
                <h5 class="modal-title">Search Coach Availabilities</h5>
                <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </a>
                <form class="loginmodalbox-search" action="{{ route('session_search') }}" method="get">

                    <div class="row">
                        <div class="col-lg-6">
                            <label class="search-modal-label">Start Date</label>
                            <input type="date" name="start" min="<?php echo date("Y-m-d"); ?>" >
                        </div>
                        <div class="col-lg-6">
                            <label class="search-modal-label" >End Date</label>
                            <input type="date" name="end" min="<?php echo date("Y-m-d"); ?>" >
                        </div>
                    </div>

                    <input type="submit" value="Search Now" class="ritekhela-bgcolor">
                </form>
            </div>
        </div>
    </div>
</div>

<div class="loginmodalbox modal fade" id="ritekhelamodalprofile" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document" id="modal-content">
        <div class="modal-content">
            <div class="modal-body ritekhela-bgcolor-two">
                <h5 class="modal-title">Search Coach Availabilities</h5>
                <a href="#" class="close ritekhela-bgcolor-two" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </a>
                <form class="loginmodalbox-search" action="<?php //echo $SITE_URL; ?>/search" method="get">

                    <div class="row">
                        <div class="col-lg-6">
                            <label class="search-modal-label">Start Date</label>
                            <input type="date" name="start" min="<?php echo date("Y-m-d"); ?>" >
                        </div>
                        <div class="col-lg-6">
                            <label class="search-modal-label" >End Date</label>
                            <input type="date" name="end" min="<?php echo date("Y-m-d"); ?>" >
                        </div>
                    </div>

                    <input type="submit" value="Search Now" class="ritekhela-bgcolor">
                </form>
            </div>
        </div>
    </div>
</div>
