
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

	<title>{{ Site_Settings($Settings, 'site_title') }}</title>

	<meta name="description" content="{{ Site_Settings($Settings, 'meta_description') }}">

	<meta name="keywords" content="{{ Site_Settings($Settings, 'meta_keywords') }}">

	<meta name="author" content="{{ Site_Settings($Settings, 'site_title') }}">

	<meta name="author" content="Logic-Valley">

	<meta name="robots" content="noindex, nofollow">

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="shortcut icon" type="image/x-icon" href="{{ asset_url('images/favicon.png') }}" />

	@yield('css_before')

    <link href="{{ portal_managed_url('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ portal_managed_url('font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/iCheck/custom.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/slick/slick.css') }}" rel="stylesheet">
    <link href="{{ portal_managed_url('css/plugins/slick/slick-theme.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/chosen/bootstrap-chosen.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/cropper/cropper.min.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/switchery/switchery.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/nouslider/jquery.nouislider.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/ionRangeSlider/ion.rangeSlider.css') }}" rel="stylesheet">
    <link href="{{ portal_managed_url('css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/clockpicker/clockpicker.css')}}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/select2/select2.min.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/plugins/dualListbox/bootstrap-duallistbox.min.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('css/animate.css') }}" rel="stylesheet">
    <link href="{{ portal_managed_url('css/style.css') }}" rel="stylesheet">

    <link href="{{ portal_managed_url('lightbox/css/lightbox.css') }}" rel="stylesheet" />

<?php /* ?><script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
<script>
  window.OneSignal = window.OneSignal || [];
  OneSignal.push(function() {
    OneSignal.init({
      appId: "6b5ca065-0699-4568-8729-8d66d6c85819",
    });
  });
</script><?phpo */?>



    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
		.delete-confirm{
			display: block;
		}
        .delete-image{
            display: block;
            width: 30px;
            position: relative;
            bottom: -32px;
            right: 0;
            float: right;
        }
		.mt-1{
			margin-top: 10px;
		}
		.mt-2{
			margin-top: 20px;
		}
		.mt-3{
			margin-top: 30px;
		}
		.mt-4{
			margin-top: 40px;
		}
		.mt-5{
			margin-top: 50px;
		}

		.ml-1{
			margin-left: 10px;
		}
		.ml-2{
			margin-left: 20px;
		}
		.ml-3{
			margin-left: 30px;
		}
		.ml-4{
			margin-left: 40px;
		}
		.ml-5{
			margin-left: 50px;
		}

		.mr-1{
			margin-right: 10px;
		}
		.mr-2{
			margin-right: 20px;
		}
		.mr-3{
			margin-right: 30px;
		}
		.mr-4{
			margin-right: 40px;
		}
		.mr-5{
			margin-right: 50px;
		}

		.mb-1{
			margin-bottom: 10px;
		}
		.mb-2{
			margin-bottom: 20px;
		}
		.mb-3{
			margin-bottom: 30px;
		}
		.mb-4{
			margin-bottom: 40px;
		}
		.mb-5{
			margin-bottom: 50px;
		}


        .cards-wrapper {
            display: flex;
            justify-content: center;
        }
        .card img {
            max-width: 100%;
            max-height: 100%;
        }
        .card {
            margin: 0 0.5em;
            box-shadow: 2px 6px 8px 0 rgba(22, 22, 26, 0.18);
            border: none;
            border-radius: 0;
        }
        .carousel-inner {
            padding: 1em;
        }
        .carousel-control-prev,
        .carousel-control-next {
            background-color: #e1e1e1;
            width: 5vh;
            height: 5vh;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }
        @media (min-width: 768px) {
            .card img {
                height: 11em;
            }
        }

        .video_thumbnail{
            position: relative;
        }
        .side_image{
            width:100%;
            height:4.5rem;
        }
        .category{
            position: absolute;
            top: 12px;
            left: 85%;
            width: 100%;
        }
        .check_id{
            /*border: solid 1px blue;*/
            /*background: #B2DBFF;*/
            /*border: 10px;*/
            /*border-radius: 0px ;*/
            /*box-shadow: 0px 0px 2px 1px black;*/
            /*outline: 2px solid #f9c303;*/
            /*outline: 2px solid #f9c303;*/
            position: absolute;
            top: 8px;
            left: 4%;
            height: 17px;
            width: 17px;
        }


        .pagination {
            display: inline-block;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #f9c303;
            color: #000000;
        }

        .pagination a:hover:not(.active) {background-color: #ddd;}


        .sub_link{
            float: right;
            color: #676a6c;
            font-weight: bold;
        }

        .white-bg {
            background-color: #f9c303;
        }

        table {
            overflow-x: scroll;
            height: auto;
        }

        tr.row {
            line-height: 40px;
        }

        tr.row24 {
            line-height: normal;
        }

        .cal_slot_red,
        .cal_slot_red_2 {
            background-color: #f8ac59 !important;
            color: white;
            min-height: 30px;
            padding: 5px;
            cursor: default;
        }

        .cal_slot_green,
        .cal_slot_green_2 {
            background-color: #f9c303 !important;
            color: white;
            min-height: 30px;
            padding: 5px;
            cursor: default;
        }

        .cal_slot_blue,
        .cal_slot_blue_2 {
            background-color: blue !important;
            color: white;
            min-height: 30px;
            padding: 5px;
            cursor: default;
            display: none;
        }

        .cal_slot_yellow,
        .cal_slot_yellow_2 {
            background-color: #3F51B5 !important;
            color: Black;
            min-height: 30px;
            padding: 5px;
            cursor: default;
            /*display:none;*/
        }

        .cal_slot_selected {
            background-color: none !important;
            color: Black;
            min-height: 30px;
            padding: 5px;
            cursor: default;
            /*display:none;*/
        }

        .cal_slot_white {
            background-color: white !important;
        }

        .cal_slot_transparent {
            background-color: transparent !important;
        }

        .cal_slot_green_2 {
            border: 1px solid #f9c303;
        }

        .colr_green {
            color: #f9c303;
        }

        .cal_slot_red_2 {
            border: 1px solid #f8ac59;
        }

        .colrblue {
            background-color: #3369E7;
        }

        .colr_red {
            color: #f8ac59;
        }

        .cal_slot_green,
        .cal_slot_green_2,
        .cal_slot_red_2 {
            cursor: pointer;
        }

        .btn-reschedulebook,
        .btn-reschedulebook:hover {
            background-color: #3369E7;
        }

        .radioBtn .notActive {
            color: #3276b1;
            background-color: #fff;
        }

        .hide {
            display: none;
        }

        .mb-60 {
            margin-bottom: 60px;
        }

        .mb-50 {
            margin-bottom: 50px;
        }

        .mb-40 {
            margin-bottom: 40px;
        }

        .mb-30 {
            margin-bottom: 30px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        .mt-60 {
            margin-top: 60px;
        }

        .mt-50 {
            margin-top: 50px;
        }

        .mt-40 {
            margin-top: 40px;
        }

        .mt-30 {
            margin-top: 30px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-5 {
            margin-top: 5px;
        }

        #radioBtn .notActive {
            color: #3276b1;
            background-color: #fff;
        }

        td.avldays_cell {
            width: 14.5%;
            height: 40px;
            border: 1px solid;
            text-align: center;
            text-transform: capitalize;
            background-color: beige;
            cursor: pointer;
        }

        td.avldays_active {
            background-color: mediumspringgreen;
        }

        .cal_slot_red,
        .fc-event-title {
            color: white;
        }

        .cal_slot_green,
        .fc-event-title {
            color: white;
        }

        .cal_slot_blue,
        .fc-event-title {
            color: white;
        }

        .cal_slot_yellow,
        .fc-event-title {
            color: black;
        }

        .msg-gr {
            color: #f9c303;
        }

        .msg-rd {
            color: #ed5565;
        }

        .bold {
            font-size: 14px;
            font-weight: bold;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: bold;
        }

        .cust_cl_0 {
            color: #000 !important;
            background-color: #FFF !important;
            border: 1px solid #000 !important;
        }

        .cust_cl_0 .fc-event-title {
            color: #000 !important;
        }

        .cust_cl_0 a {
            color: #000 !important;
        }


        .cust_cl_1 {
            color: #FFF !important;
            background-color: #ee5353 !important;
        }

        .cust_cl_1 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_1 a {
            color: #FFF !important;
        }

        .cust_cl_2 {
            color: #FFF !important;
            background-color: #f778b4 !important;
        }

        .cust_cl_2 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_2 a {
            color: #FFF !important;
        }

        .cust_cl_3 {
            color: #FFF !important;
            background-color: #e27eff !important;
        }

        .cust_cl_3 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_3 a {
            color: #FFF !important;
        }

        .cust_cl_4 {
            color: #FFF !important;
            background-color: #8989fc !important;
        }

        .cust_cl_4 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_4 a {
            color: #FFF !important;
        }

        .cust_cl_5 {
            color: #FFF !important;
            background-color: #4a91e9 !important;
        }

        .cust_cl_5 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_5 a {
            color: #FFF !important;
        }

        .cust_cl_6 {
            color: #FFF !important;
            background-color: #0cc0d7 !important;
        }

        .cust_cl_6 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_6 a {
            color: #FFF !important;
        }

        .cust_cl_7 {
            color: #FFF !important;
            background-color: #34c76e !important;
        }

        .cust_cl_7 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_7 a {
            color: #FFF !important;
        }

        .cust_cl_8 {
            color: #FFF !important;
            background-color: #67c820 !important;
        }

        .cust_cl_8 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_8 a {
            color: #FFF !important;
        }

        .cust_cl_9 {
            color: #FFF !important;
            background-color: #dfc12d !important;
        }

        .cust_cl_9 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_9 a {
            color: #FFF !important;
        }

        .cust_cl_10 {
            color: #FFF !important;
            background-color: #f49a31 !important;
        }

        .cust_cl_10 .fc-event-title {
            color: #FFF !important;
        }

        .cust_cl_10 a {
            color: #FFF !important;
        }

        .cust_cl_booked {
            color: #FFF !important;
            background-color: #f9c303 !important;
        }

        .cust_cl_booked .fc-event-title {
            color: #000000 !important;
        }

        .cust_cl_booked a {
            color: #000000 !important;
        }


        .fc-button-month {
            display: none;
        }

        @media (max-width: 767px) {

            h1,
            .h1 {
                font-size: 22px;
            }

            h2,
            .h2 {
                font-size: 18px;
            }

            h3,
            .h3 {
                font-size: 16px;
            }

            h4,
            .h4 {
                font-size: 14px;
            }

            h5,
            .h5 {
                font-size: 11px;
            }

            h6,
            .h6 {
                font-size: 9px;
            }

            .sm-action .title-action {
                padding-top: 10px;
                text-align: left;
            }

            .examplerow {
                width: 100%;
                overflow-x: scroll;
            }

            .examplerow .col-lg-12 {
                overflow-x: scroll;
            }

            .examplerow .col-lg-12 #example {
                overflow-x: scroll;
            }

            .nav>li.sm-show {
                display: block !important;
            }

            .close {
                font-size: 40px;
            }

            .smcs {
                padding: 0 !important;
                margin-bottom: 40px !important;
            }

            .smcs-label {
                display: inline-block !important;
                width: 30% !important;
                float: left !important;
            }

            .smcs-control {
                display: inline-block !important;
                width: 35% !important;
                float: left !important;
            }

            .smcs-control-full {
                display: inline-block !important;
                width: 70% !important;
                float: left !important;
            }

            .sm-hide {
                display: none !important;
            }

            .sm-right {
                float: right;
            }

            .category{

                left: 70%;

            }

        }

        @media (min-width: 768px) {

            .nav>li.sm-show {
                display: none !important;
            }
        }

        .lebel-left {
            padding: 0 !important;
        }

        .current-rating {
            margin: 0px !important;
        }

    </style>
<link rel="stylesheet" href="{{ asset_url('select2//select2.min.css') }}">
<style>

.select2-container .select2-selection--single{
	height: 35px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
	line-height: 35px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow{
	height: 35px;
}
</style>
	@yield('css_after')
