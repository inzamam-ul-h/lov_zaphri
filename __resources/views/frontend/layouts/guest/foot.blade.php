@yield('css_after')

<style>
    #absoluteform {
        position: absolute;
        top: 240px;
        left: 150px;
        min-width: 30%;
        height: 500px;
        z-index: 2;
        padding: 20px;
    }
    .line_spacing{
        line-height:50px !important;
    }
    .form-column2 {
        background-color: rgb(255, 525, 255, 1);
        padding-bottom: 20px;
        padding: 20px;
    }

    @media (max-width: 767px) {
        #absoluteform {
            /*            display: none;*/

            position: relative;
            top: 0px;
            left: 0px;
            min-width: 100%;
            height: auto;
            z-index: 2;
            padding: 0 5px;
            clear: both
        }
        .panel-body h1{
            line-height:30px !important;
            font-size: 25px;
        }
    }

    /* Tabs */
    .tabs-container .panel-body {
        background: #fff;
        border: 1px solid #e7eaec;
        border-radius: 2px;
        padding: 20px;
        position: relative;
    }

    .tabs-container .nav-tabs>li.active>a,
    .tabs-container .nav-tabs>li.active>a:hover,
    .tabs-container .nav-tabs>li.active>a:focus {
        border-bottom: 1px solid #e7eaec;
        background-color: #fff;
    }

    .tabs-container .nav-tabs>li {
        float: left;
        margin-bottom: -1px;
        background: none;
        border: none;
        padding: 10px;
        font-weight: bold;
    }

    .tabs-container .tab-pane .panel-body {
        border-top: none;
    }

    .tabs-container .nav-tabs>li.active>a,
    .tabs-container .nav-tabs>li.active>a:hover,
    .tabs-container .nav-tabs>li.active>a:focus {
        border-bottom: 2px solid #000000;
        background-color: #fff;
    }

    .tabs-container .nav-tabs {
        border-bottom: 1px solid #e7eaec;
    }

    .tabs-container .tab-pane .panel-body {
        border-top: none;
    }

    .tabs-container .tabs-left .tab-pane .panel-body,
    .tabs-container .tabs-right .tab-pane .panel-body {
        border-top: 1px solid #e7eaec;
    }

    .tabs-container .nav-tabs>li a:hover {
        background: transparent;
        border-color: transparent;
    }

    .tabs-container .tabs-below>.nav-tabs,
    .tabs-container .tabs-right>.nav-tabs,
    .tabs-container .tabs-left>.nav-tabs {
        border-bottom: 0;
    }

    .tabs-container .tabs-left .panel-body {
        position: static;
    }

    .tabs-container .tabs-left>.nav-tabs,
    .tabs-container .tabs-right>.nav-tabs {
        width: 20%;
    }

    .tabs-container .tabs-left .panel-body {
        width: 80%;
        margin-left: 20%;
    }

    .tabs-container .tabs-right .panel-body {
        width: 80%;
        margin-right: 20%;
    }

    .tabs-container .tab-content>.tab-pane,
    .tabs-container .pill-content>.pill-pane {
        display: none;
    }

    .tabs-container .tab-content>.active,
    .tabs-container .pill-content>.active {
        display: block;
    }

    .tabs-container .tabs-below>.nav-tabs {
        border-top: 1px solid #e7eaec;
    }

    .tabs-container .tabs-below>.nav-tabs>li {
        margin-top: -1px;
        margin-bottom: 0;
    }

    .tabs-container .tabs-below>.nav-tabs>li>a {
        -webkit-border-radius: 0 0 4px 4px;
        -moz-border-radius: 0 0 4px 4px;
        border-radius: 0 0 4px 4px;
    }

    .tabs-container .tabs-below>.nav-tabs>li>a:hover,
    .tabs-container .tabs-below>.nav-tabs>li>a:focus {
        border-top-color: #e7eaec;
        border-bottom-color: transparent;
    }

    .tabs-container .tabs-left>.nav-tabs>li,
    .tabs-container .tabs-right>.nav-tabs>li {
        float: none;
    }

    .tabs-container .tabs-left>.nav-tabs>li>a,
    .tabs-container .tabs-right>.nav-tabs>li>a {
        min-width: 74px;
        margin-right: 0;
        margin-bottom: 3px;
    }

    .tabs-container .tabs-left>.nav-tabs {
        float: left;
        margin-right: 19px;
    }

    .tabs-container .tabs-left>.nav-tabs>li>a {
        margin-right: -1px;
        -webkit-border-radius: 4px 0 0 4px;
        -moz-border-radius: 4px 0 0 4px;
        border-radius: 4px 0 0 4px;
    }

    .tabs-container .tabs-left>.nav-tabs .active>a,
    .tabs-container .tabs-left>.nav-tabs .active>a:hover,
    .tabs-container .tabs-left>.nav-tabs .active>a:focus {
        border-color: #e7eaec transparent #e7eaec #e7eaec;
    }

    .tabs-container .tabs-right>.nav-tabs {
        float: right;
        margin-left: 19px;
    }

    .tabs-container .tabs-right>.nav-tabs>li>a {
        margin-left: -1px;
        -webkit-border-radius: 0 4px 4px 0;
        -moz-border-radius: 0 4px 4px 0;
        border-radius: 0 4px 4px 0;
    }

    .tabs-container .tabs-right>.nav-tabs .active>a,
    .tabs-container .tabs-right>.nav-tabs .active>a:hover,
    .tabs-container .tabs-right>.nav-tabs .active>a:focus {
        border-color: #e7eaec #e7eaec #e7eaec transparent;
        z-index: 1;
    }

    .tabs-container .nav-tabs>li {
        list-style: none;
    }

    .tabs-container .btn {
        background: #000;
        color: #FFF;
    }

    @media (max-width: 767px) {
        .tabs-container .nav-tabs>li {
            float: none !important;
        }

        .tabs-container .nav-tabs>li.active>a {
            border-bottom: 1px solid #e7eaec !important;
            margin: 0;
        }
    }



    .star-rating,
    .star-rating a:hover,
    .star-rating a:active,
    .star-rating .current-rating {
        background: url('<?php echo asset_url('images/star.gif');?>') left -1000px repeat-x;
    }

    .star-rating {
        position: relative;
        width: 125px;
        height: 24px;
        overflow: hidden;
        list-style: none;
        margin: 0;
        padding: 0;
        background-position: left top;
    }

    .star-rating li {
        display: inline;
    }

    .star-rating a,
    .star-rating .current-rating {
        position: absolute;
        top: 0;
        left: 0;
        text-indent: -1000em;
        height: 25px;
        line-height: 25px;
        outline: none;
        overflow: hidden;
        border: none;
    }

    .star-rating a:hover,
    .star-rating a:active {
        background-position: left bottom;
    }

    .star-rating a.one-star {
        width: 20%;
        z-index: 6;
    }

    .star-rating a.two-stars {
        width: 40%;
        z-index: 5;
    }

    .star-rating a.three-stars {
        width: 60%;
        z-index: 4;
    }

    .star-rating a.four-stars {
        width: 80%;
        z-index: 3;
    }

    .star-rating a.five-stars {
        width: 100%;
        z-index: 2;
    }

    .star-rating .current-rating {
        z-index: 1;
        background-position: left center;
    }

    /* CSS Document */

    .static-rating,
    .static-rating a:hover,
    .static-rating a:active,
    .static-rating .current-rating {
        background: url('<?php echo asset_url('images/starhalf.gif');?>') left -1000px repeat-x;
    }

    .static-rating {
        position: relative;
        width: 81px;
        height: 16px;
        overflow: hidden;
        list-style: none;
        margin: 0;
        padding: 0;
        background-position: left top;
    }

    .static-rating li {
        display: inline !important;
        padding: 0 !important;
    }

    .static-rating a,
    .static-rating .current-rating {
        border: none;
        height: 16px;
        left: 0;
        line-height: 16px;
        outline: none;
        overflow: hidden;
        position: absolute;
        text-indent: -1000em;
        top: 1px;
    }

    .static-rating a:hover,
    .static-rating a:active {
        background-position: left bottom;
    }

    .static-rating a.one-star {
        width: 10%;
        z-index: 6;
    }

    .static-rating a.two-stars {
        width: 30%;
        z-index: 5;
    }

    .static-rating a.three-stars {
        width: 50%;
        z-index: 4;
    }

    .static-rating a.four-stars {
        width: 70%;
        z-index: 3;
    }

    .static-rating a.five-stars {
        width: 100%;
        z-index: 2;
    }

    .static-rating .current-rating {
        z-index: 1;
        background-position: left bottom;
    }

    .sr_inline {
        display: inline-block !important;
    }

</style>

<script src="{{ portal_managed_url('lightbox/js/lightbox.js') }}"></script>

<script src="{{ asset_url('script/jquery.js') }}"></script>
<script src="{{ asset_url('script/popper.min.js') }}"></script>

<script src="{{ asset_url('script/bootstrap.min.js') }}"></script>
<script src="{{ asset_url('script/slick.slider.min.js') }}"></script>
<script src="{{ asset_url('script/fancybox.min.js') }}"></script>
<script src="{{ asset_url('script/isotope.min.js') }}"></script>
<script src="{{ asset_url('script/smartmenus.min.js') }}"></script>
<script src="{{ asset_url('script/progressbar.js') }}"></script>
<script src="{{ asset_url('script/jquery.countdown.min.js') }}"></script>
<script src="{{ asset_url('script/functions.js') }}"></script>

<script src="{{ asset_url('js/bootstrap.min.js') }}"></script>
<script src="{{ asset_url('js/jquery.validate.min.js') }}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/css/intlTelInput.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.min.js"></script>

<?php
$user = Auth::user();
if(!Auth::user())
{
    ?>
<script type="text/javascript">
        function ajax_csrf_token() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });
        }
        $(document).ready(function(e) {

            $("#modalforgot_1").click(function(e) {
                $('#modalLoginEmail').modal('hide');
            });

            $("#modalSignup_1").click(function(e) {
                $('#modalLoginEmail').modal('hide');
            });

            $("#modal_phone_1").click(function(e) {
                $('#modalLoginEmail').modal('hide');
            });
			
			
            $("#modalSignup_22").click(function(e) {
                $('#modalVerifyEmail').modal('hide');
            });
			
            $("#modalSignin_11").click(function(e) {
                $('#modalVerifyEmail').modal('hide');
            });
			
            $("#modalSignin_12").click(function(e) {
                $('#modalVerifyEmail').modal('hide');
            });
			
			
			
            $("#modalSignin_9").click(function(e) {
                $('#modalSendCodeEmail').modal('hide');
            });
			

            $("#modalSignin_1").click(function(e) {
                $('#modalForgotEmail').modal('hide');
            });

            $("#modalSignup_2").click(function(e) {
                $('#modalForgotEmail').modal('hide');
            });
			
            $("#modalSignin_18").click(function(e) {
                $('#modalForgotEmail').modal('hide');
            });
			

            $("#modalSignin_2").click(function(e) {
                $('#modalSignupEmail').modal('hide');
            });

            $("#modalSignup_5").click(function(e) {
                $('#modalSignupEmail').modal('hide');
            });
			
            $("#modalSignin_77").click(function(e) {
                $('#modalSignupEmail').modal('hide');
            });
			
            $("#modalSignin_7").click(function(e) {
                $('#modalSignupEmail').modal('hide');
            });
			
			
			
			
            $("#modalSignup_6").click(function(e) {
                $('#modalSignupPhone').modal('hide');
            });
			
            $("#modalSignin_8").click(function(e) {
                $('#modalSignupPhone').modal('hide');
                $('#verification_phone_no').val( $('#phone_no').val() );
                $('#verification_phone_no_code').val( $('#phone_no_code').val() );
            });
			
            $("#modalSignin_17").click(function(e) {
                $('#modalSignupPhone').modal('hide');
            });
			
			
            $("#modalSignup_9").click(function(e) {
                $('#modalVerifyPhone').modal('hide');
            });
			
            $("#modalSignin_10").click(function(e) {
                $('#modalVerifyPhone').modal('hide');
            });
			
            $("#modalSignup_11").click(function(e) {
                $('#modalVerifyPhone').modal('hide');
            });
			
            $("#modalSignin_16").click(function(e) {
                $('#modalVerifyPhone').modal('hide');
                $('#send_code_phone_no_code').val( $('#verification_phone_no_code').val() );
                $('#send_code_phone_no').val( $('#verification_phone_no').val() );
            });
			
			
            $("#modal_phone_2").click(function(e) {
                $('#modalLoginPhone').modal('hide');
            });
			
            $("#modalforgot_2").click(function(e) {
                $('#modalLoginPhone').modal('hide');
            });
			
            $("#modalSignup_14").click(function(e) {
                $('#modalLoginPhone').modal('hide');
            });
			
			
            $("#modalSignin_19").click(function(e) {
                $('#modalForgotPhone').modal('hide');
            });
			
            $("#modalSignin_20").click(function(e) {
                $('#modalForgotPhone').modal('hide');
            });
			
            $("#modalSignup_21").click(function(e) {
                $('#modalForgotPhone').modal('hide');
            });
			
			
            $("#modalSignin_15").click(function(e) {
                $('#modalSendCodePhone').modal('hide');
                $('#verification_phone_no_code').val( $('#send_code_phone_no_code').val() );
                $('#verification_phone_no').val( $('#send_code_phone_no').val() );
            });


            $(function() {
                $("form[name='registration']").validate({
                    rules: {
                        //firstname: "required",
                        //lastname: "required",
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 6
                        }
                    },

                    messages: {
                        //firstname: "Please enter your firstname",
                        //lastname: "Please enter your lastname",
                        email: "Please enter a valid email address",
                        password: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 6 characters long"
                        }
                    },

                    submitHandler: function(form) {
                        signup_user();
                    }
                });
            });
			
            $(function() {
                $("form[name='emailVerifyForm']").validate({
                    rules: {
                        email_verification_email: {
                            required: true,
                            email: true
                        },
                        email_verification_code: {
                            required: true,
                            minlength: 4,
                            maxlength: 4
                        }
                    },

                    messages: {
                        email_verification_email: "Please enter a valid email address",
                        email_verification_code: {
                            required: "Please provide verification code",
                            minlength: "verification code must be 4 characters long",
                            minlength: "verification code must be 4 characters long"
                        }
                    },

                    submitHandler: function(form) {
                        email_verification();
                    }
                });
            });
			
            $(function() {
                $("form[name='email_resend_code']").validate({
                    rules: {
                        email_resend_code_email: {
                            required: true,
                            email: true
                        }
                    },

                    messages: {
                        email_resend_code_email: "Please enter a valid email address",
                    },

                    submitHandler: function(form) {
                        email_resend_code();
                    }
                });
            });

            $(function() {
                $("form[name='forgot_message']").validate({
                    rules: {
                        forget_email: {
                            required: true,
                            email: true
                        }
                    },

                    messages: {
                        forget_email: "Please enter a valid email address"
                    },

                    submitHandler: function(form) {
                        forgot();
                    }
                });
            });

            $(function() {
                $("form[name='login']").validate({
                    rules: {
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                        }
                    },
                    messages: {
                        email: "Please enter a valid email address",

                        password: {
                            required: "Please enter password",
                        }
                    },
                    submitHandler: function(form) {
                        login_user();
                    }
                });
            });
        });

        $.ajax({
            url : "{{ url('/country/numbers.json') }}",
            type:'GET',
            dataType: 'json',
            success: function(response) {
                $("#phone_no_code").attr('disabled', false);
                $("#login_phone_no_code").attr('disabled', false);
                $.each(response,function(key, value)
                {
                    $("#phone_no_code").append('<option value=' + value.tel + '>' + value.name + ' ( '+value.tel+' )</option>');
                    $("#forget_phone_code").append('<option value=' + value.tel + '>' + value.name + ' ( '+value.tel+' )</option>');
                    $("#login_phone_no_code").append('<option value=' + value.tel + '>' + value.name + ' ( '+value.tel+' )</option>');
                    $("#verification_phone_no_code").append('<option value=' + value.tel + '>' + value.name + ' ( '+value.tel+' )</option>');
                    $("#send_code_phone_no_code").append('<option value=' + value.tel + '>' + value.name + ' ( '+value.tel+' )</option>');
                });
             }
        });

	// Emails

        function signup_user() {
            $('#btn_signup').attr('disabled', true);
            $('#signup_msg_error').html('');
            $('#signup_msg_error').hide();
            $('#signup_msg_success').html('Please wait! we are processing your request.');
            $('#signup_msg_success').show();

            var email = $('#email_2').val();
            var password = $('#password_2').val();
            var user_type = $('#user_type').val();

            {
                ajax_csrf_token();

                $.ajax({
                    url: "{{ route('register.email') }}",
                    data: "email=" + email + "&password=" + password + "&user_type=" + user_type,
                    type: "POST",
                    success: function(response) {
                        status = response.status;
                        if (status == 'true'|| status == true) {
                            $('#email_2').hide();
                            $('#password_2').hide();
                            $('#user_type').hide();
                            $('#btn_signup').attr('disabled', true);
                            $('#signup_msg_error').hide();
                            $('#email_verification_email').val(email);
                            $('#email_resend_code_email').val(email);
							

                            $('#signup_msg_success').html(response.messages);
                            $('#signup_msg_success').show();
							$('#email_verification_msg_success').html(response.messages);
                            $('#email_verification_msg_success').show();
                            $('#modalSignin_77').trigger('click');
                        } else {
                            $('#signup_msg_error').html(response.messages);
                            $('#signup_msg_error').show();
                            $('#signup_msg_success').hide();
                            $('#btn_signup').attr('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401 || xhr.status ==419) {
                            location.reload();
                        }

                        var errors = xhr.responseJSON.message;

                        $('#signup_msg_success').hide();
                        $('#signup_msg_error').show();
                        $('#signup_msg_error').html(errors);
                        $('#btn_signup').attr('disabled', false);
                    }
                });
            }
        }

        function email_verification() {
            $('#email_verification_msg_error').html('');
            $('#email_verification_msg_error').hide();
            $('#email_verification_msg_success').html('Please wait! we are processing your request.');
            $('#email_verification_msg_success').show();

            var email = $('#email_verification_email').val();

            var verification_code = $('#email_verification_code').val();
            {
                ajax_csrf_token();
                $.ajax({
                    url: "{{ route('verifyCode.email') }}",
                    data: "email=" + email + "&verification_code=" + verification_code,
                    type: "POST",
                    success: function(response) {
                        status = response.status;
                        if (status == true || status == 'true' ) {
                            $('#email_verification_msg_success').html(response.messages);
                            $('#email_verification_msg_success').show();
                            $('#email_verification_msg_error').hide();
                            $('#modalSignin_11').trigger('click');
                        } else {
                            $('#email_verification_msg_error').html(response.messages);
                            $('#email_verification_msg_error').show();
                            $('#email_verification_msg_success').hide();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401 || xhr.status ==419) {
                            location.reload();
                        }

                        var errors = xhr.responseJSON.message;

                        $('#email_verification_msg_success').hide();
                        $('#email_verification_msg_error').show();
                        $('#email_verification_msg_error').html(errors);
                    }
                });
            }
        }

        function email_resend_code() {
            $('#email_resend_code_msg_error').html('');
            $('#email_resend_code_msg_error').hide();
            $('#email_resend_code_msg_success').html('Please wait! we are processing your request.');
            $('#email_resend_code_msg_success').show();

            var email_resend_code_email = $('#email_resend_code_email').val();
            {
                ajax_csrf_token();
                $.ajax({
                    url: "{{ route('resendCode.email') }}",
                    data: "email=" + email_resend_code_email,
                    type: "POST",
                    success: function(response) {
                        status = response.status;
                        if (status == true || status == 'true' ) {
                            $('#email_resend_code_msg_success').html(response.messages);
                            $('#email_resend_code_msg_success').show();
                            $('#email_resend_code_msg_error').hide();
                            $('#btn_email_resend_code').hide();
                            $('#email_resend_code_email').hide();
                            $('#modalSignin_9').text("Verify Now");
                            $('#modalSignin_9').css('color', '#19aa8d');
                        } else {
                            $('#email_resend_code_msg_error').html(response.messages);
                            $('#email_resend_code_msg_error').show();
                            $('#email_resend_code_msg_success').hide();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status == 401 || xhr.status ==419) {
                            location.reload();
                        }

                        var errors = xhr.responseJSON.message;

                        $('#email_resend_code_msg_success').hide();
                        $('#email_resend_code_msg_error').show();
                        $('#email_resend_code_msg_error').html(errors);
                    }
                });
            }
        }

        function forgot() {

            $('#forgot_msg_error').html('');
            $('#forgot_msg_error').hide();
            $('#forgot_msg_success').html('Please wait! we are processing your request.');
            $('#forgot_msg_success').show();

            var forget_email = $('#forget_email').val();

            {
                $('#ritekhela-loader').show();
                ajax_csrf_token();

                $.ajax({
                    url: "{{ route('password.email') }}",
                    data: "email=" + forget_email,
                    type: "POST",
                    success: function(response) {
                        status = response.status;
                        if (status == true || status == 'true' ) {
                            $('#forgot_msg_success').html(response.messages);
                            $('#forget_email').val("");
                            $('#forgot_msg_success').show();
                            $('#forgot_msg_error').hide();
                            setTimeout(function() {
                                $("#forgot_msg_success").hide();
                            }, 5000);
                        } else {
                            $('#forgot_msg_error').html(response.messages);
                            $('#forgot_msg_error').show();
                            $('#forgot_msg_success').hide();
                        }

                    },
                    error: function(xhr) {
                        if (xhr.status == 401 || xhr.status ==419) {
                            location.reload();
                        }

                        var errors = xhr.responseJSON.message;

                        $('#forgot_msg_success').hide();
                        $('#forgot_msg_error').show();
                        $('#forgot_msg_error').html(errors);
                    },
                    complete:function(response){
                        $('#ritekhela-loader').hide();
                    }
                });
            }
        }

        function login_user() {
            $('#btn_login').attr('disabled', true);
            $('#login_msg_error').html('');
            $('#login_msg_error').hide();
            $('#login_msg_success').html('Please wait! we are processing your request.');
            $('#login_msg_success').show();

            var email = $('#email').val();
            var password = $('#password').val();
            var remember_me = 0;//$('input[name="loginkeeping"]:checked').val();

            {
                ajax_csrf_token();

                $.ajax({
                    url: "{{ url('/login-email') }}",
                    data: "email=" + email + "&password=" + password + "&remember_me=" + remember_me,
                    type: "POST",
                    success: function(response) {
                        status = response.status;
                        if (status == 'true'|| status == true) {
                            $('#login_msg_success').html(response.messages);
                            $('#login_msg_success').show();
                            $('#login_msg_error').hide();                            
                            $('#btn_login').attr('disabled', true);
                            setTimeout(function(){ location.href='{{ url('/manage/dashboard')}}'; }, 500);
                        } else {
                            $('#login_msg_error').html(response.messages);
                            $('#login_msg_error').show();
                            $('#login_msg_success').hide();
                            $('#btn_login').attr('disabled', false);
			}
                    },
                    error: function(xhr) {
                        if (xhr.status == 401 || xhr.status ==419) {
                            location.reload();
                        }

                        var errors = xhr.responseJSON.message;

                        $('#login_msg_success').hide();
                        $('#login_msg_error').show();
                        $('#login_msg_error').html(errors);
                        $('#btn_login').attr('disabled', false);
                    }
                });
            }
        }



    </script>
    <script>
    		// Phones
    		
            $(document).ready(function(e) {
    			
                $(function() {
                    $("form[name='phone_registration']").validate({
                        rules: {
                            phone_no: {
                                required: true,
                                minlength:7,
                                maxlength:15
                            },
                            password_phone: {
                                required: true,
                                minlength: 6
                            }
                        },

                        messages: {
                            phone_no: "Please enter a valid Phone Number",
                            password_phone: {
                                required: "Please provide a password",
                                minlength: "Your password must be at least 6 characters long"
                            }
                        },

                        submitHandler: function(form) {
                            signup_user_phone();
                        }
                    });
                });
                
                $(function() {
                    $("form[name='phone_verification']").validate({
                        rules: {
                            verification_phone_no: {
                                required: true,
                                minlength:7,
                                maxlength:15
                            },
                            verification_code: {
                                required: true,
                            }
                        },

                        messages: {
                            verification_phone_no: {
                                required: "Please provide a Phone Number",
                                minlength: "Please Enter Correct Phone No."
                            },
                            verification_code: "Verfication Code required"
                        },

                        submitHandler: function(form) {
                            phone_no_verification();
                        }
                    });
                });
    			
    		$(function() {
                    $("form[name='send_code_again']").validate({
                        rules: {
                            send_code_phone_no: {
                                required: true,
                                minlength:7,
                                maxlength:15
                            }
                        },

                        messages: {
                            send_code_phone_no: {
                                required: "Please provide a Phone Number",
                                minlength: "Please Enter Correct Phone No."
                            }
                        },

                        submitHandler: function(form) {
                            send_code_again();
                        }
                    });
                });
    			
    		$(function() {
                    $("form[name='forgot_message_phone']").validate({
                        rules: {
                            forget_phone: {
                                required: true,
                                minlength:7,
                                maxlength:15
                            }
                        },

                        messages: {
                            forget_phone: "Please enter a valid Phone Number"
                        },

                        submitHandler: function(form) {
                            forgot_phone();
                        }
                    });
                });
                
    		$(function() {
                    $("form[name='login_phone']").validate({
                        rules: {
                            login_phone_no: {
                                required: true,
                                minlength:7,
                                maxlength:15
                            },
                            login_password: {
                                required: true,
                            }
                        },
                        messages: {
                            login_phone_no: "Please enter a valid phone number",

                            password: {
                                required: "Please enter password",
                            }
                        },
                        submitHandler: function(form) {
                            login_user_phone_no();
                        }
                    });
                });
    			            
            });

            function signup_user_phone() {
                $('#signup_phone_msg_error').html('');
                $('#signup_phone_msg_error').hide();
                $('#signup_phone_msg_success').html('Please wait! we are processing your request.');
                $('#signup_phone_msg_success').show();

                var phone_no = $('#phone_no').val();
                var phone_no_prefix = $('#phone_no_code').val();
                var password = $('#password_phone').val();
                var user_type = $('#user_type_phone').val();
                {
                    ajax_csrf_token();

                    $.ajax({
                        url: "{{ route('register.phone') }}",
                        data: "phone=" + phone_no+ "&phone_no_prefix=" + phone_no_prefix  + "&password=" + password + "&user_type=" + user_type,
                        type: "POST",
                        success: function(response) {
                            status = response.status;
                            if (status == 'true'|| status == true) {
                                $('#phone_no').hide();
                                $('#password_phone').hide();
                                $('#user_type_phone').hide();
                                $('#signup_phone_no').hide();
                                $('#phone_no_code').hide();
                                $('#signup_phone_msg_error').hide();
                                $('#modalSignin_8').css('color', '#19aa8d');

                                $('#verification_phone_no').val(phone_no);

                                $('#signup_phone_msg_success').html(response.messages);
                                $('#signup_phone_msg_success').show();
                            } else {
                                $('#signup_phone_msg_success').hide();
                                $('#signup_phone_msg_error').html(response.messages);
                                $('#signup_phone_msg_error').show();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status == 401 || xhr.status ==419) {
                                location.reload();
                            }

                            var errors = xhr.responseJSON.message;

                            $('#signup_phone_msg_success').hide();
                            $('#signup_phone_msg_error').show();
                            $('#signup_phone_msg_error').html(errors);
                        }
                    });
                }
            }

            function phone_no_verification() {
                $('#verification_msg_error').html('');
                $('#verification_msg_error').hide();
                $('#verification_msg_success').html('Please wait! we are processing your request.');
                $('#verification_msg_success').show();

                var phone_no = $('#verification_phone_no').val();
                var phone_no_prefix = $('#verification_phone_no_code').val();
                var verification_code = $('#verification_code').val();
                {
                    ajax_csrf_token();
                    $.ajax({
                        url: "{{ route('verifyCode.phone') }}",
                        data: "phone=" + phone_no+ "&phone_no_prefix=" + phone_no_prefix + "&verification_code=" + verification_code,
                        type: "POST",
                        success: function(response) {
                            status = response.status;
                            if (status == true || status == 'true' ) {
                                $('#verification_msg_success').html(response.messages);
                                $('#verification_msg_success').show();
                                $('#verification_msg_error').hide();
                            } else {
                                $('#verification_msg_error').html(response.messages);
                                $('#verification_msg_error').show();
                                $('#verification_msg_success').hide();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status == 401 || xhr.status ==419) {
                                location.reload();
                            }

                            var errors = xhr.responseJSON.message;

                            $('#verification_msg_success').hide();
                            $('#verification_msg_error').show();
                            $('#verification_msg_error').html(errors);
                        }
                    });
                }
            }

            function send_code_again() {
                $('#send_code_msg_error').html('');
                $('#send_code_msg_error').hide();
                $('#send_code_msg_success').html('Please wait! we are processing your request.');
                $('#send_code_msg_success').show();

                var phone_no = $('#send_code_phone_no').val();
                var phone_no_prefix = $('#send_code_phone_no_code').val();
                {
                    ajax_csrf_token();
                    $.ajax({
                        url: "{{ route('resendCode.phone') }}",
                        data: "phone=" + phone_no + "&phone_no_prefix=" + phone_no_prefix,
                        type: "POST",
                        success: function (response) {
                            status = response.status;
                            if (status == true || status == 'true') {
                                $('#send_code_msg_success').html(response.messages);
                                $('#send_code_msg_success').show();
                                $('#send_code_msg_error').hide();
                                $('#send_code_phone_no').hide();
                                $('#send_verification_code').hide();
                                $('#send_code_phone_no_code').hide();
                                $('#modalSignin_15').text("Verify Now");
                                $('#modalSignin_15').css('color', '#19aa8d');
                            }
                            else {
                                $('#send_code_msg_error').html(response.messages);
                                $('#send_code_msg_error').show();
                                $('#send_code_msg_success').hide();
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status == 401 || xhr.status == 419) {
                                location.reload();
                            }

                            var errors = xhr.responseJSON.message;

                            $('#send_code_msg_success').hide();
                            $('#send_code_msg_error').show();
                            $('#send_code_msg_error').html(errors);
                        }
                    });
                }
            }

            function forgot_phone() {
                $('#forgot_msg_phone_error').html('');
                $('#forgot_msg_phone_error').hide();
                $('#forgot_msg_phone_success').html('Please wait! we are processing your request.');
                $('#forgot_msg_phone_success').show();

                var phone_no = $('#forget_phone').val();
                var phone_no_prefix = $('#forget_phone_code').val();
                {
                    ajax_csrf_token();

                    $.ajax({
                        url: "{{ route('password.phone') }}",
                        data: "phone=" + phone_no + "&phone_no_prefix=" + phone_no_prefix,
                        type: "POST",
                        success: function (response) {
                            status = response.status;
                            if (status == true || status == 'true') {
                                $('#forgot_msg_phone_success').html(response.messages);
                                $('#forgot_msg_phone_success').show();
                                $('#forgot_msg_phone_error').hide();
                            }
                            else {
                                $('#forgot_msg_phone_error').html(response.messages);
                                $('#forgot_msg_phone_error').show();
                                $('#forgot_msg_phone_success').hide();
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status == 401 || xhr.status == 419) {
                                location.reload();
                            }

                            var errors = xhr.responseJSON.message;

                            $('#forgot_msg_phone_success').hide();
                            $('#forgot_msg_phone_error').show();
                            $('#forgot_msg_phone_error').html(errors);
                        }
                    });
                }
            }

            function login_user_phone_no() {
                $('#phone_no_login_msg_error').html('');
                $('#phone_no_login_msg_error').hide();
                $('#phone_no_login_msg_success').html('Please wait! we are processing your request.');
                $('#phone_no_login_msg_success').show();

                var phone_no = $('#login_phone_no').val();
                var phone_no_prefix = $('#login_phone_no_code').val();
                phone_no = (phone_no_prefix + "" + phone_no);
                var password = $('#login_password').val();
                {
                    ajax_csrf_token();

                    $.ajax({
                        url: "{{ url('/login-phone') }}",
                        data: "phone=" + phone_no + "&password=" + password,
                        type: "POST",
                        success: function (response) {
                            status = response.status;
                            if (status == 'true' || status == true) {
                                $('#phone_no_login_msg_success').html(response.messages);
                                $('#phone_no_login_msg_success').show();
                                $('#phone_no_login_msg_error').hide();
                                        setTimeout(function () {
                                            location.href = '{{ url(' / manage / dashboard')}}';
                                        }, 500);
                            }
                            else {
                                $('#phone_no_login_msg_error').html(response.messages);
                                $('#phone_no_login_msg_error').show();
                                $('#phone_no_login_msg_success').hide();
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status == 401 || xhr.status == 419) {
                                location.reload();
                            }

                            var errors = xhr.responseJSON.message;

                            $('#phone_no_login_msg_success').hide();
                            $('#phone_no_login_msg_error').show();
                            $('#phone_no_login_msg_error').html(errors);
                        }
                    });
                }
            }

    </script>
    <?php
}
?>
@yield('js_after')

@stack('scripts')

