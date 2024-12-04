<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"><meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>{{$PageTitle}} {{($PageTitle !="")? "|":""}} {{ Site_Settings($Settings, 'site_title') }}</title>
<meta name="description" content="{{($PageDescription !="")? $PageDescription : Site_Settings($Settings, 'meta_description')}}">
<meta name="keywords" content="{{($PageKeywords !="")? $PageKeywords : Site_Settings($Settings, 'meta_keywords')}}">
<meta name="author" content="Logic Valley">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset_url('images/favicon.png') }}" />

@yield('css_before')

<link rel="stylesheet" href="{{ asset_url('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset_url('css/fontawesome-all.css') }}">
<link rel="stylesheet" href="{{ asset_url('css/slick-slider.css') }}">
<link rel="stylesheet" href="{{ asset_url('css/fancybox.css') }}">
<link rel="stylesheet" href="{{ asset_url('css/smartmenus.css') }}">
<link rel="stylesheet" href="{{ asset_url('style.css') }}">
<link rel="stylesheet" href="{{ asset_url('css/color.css') }}">
<link rel="stylesheet" href="{{ asset_url('css/responsive.css') }}">




<style>

    .Item.ItemLeft, .Item.ItemMiddle, .Item.ItemRight
    {
        float:left;
        /*margin-right:15px;*/
    }
    .clear
    {
        clear:both;
    }


    img.ItemImage {
        width: 100%;
        height: 100%;
    }


    .OverlayIcon {
        position: absolute;
        top: 0px;
        left: 0px;
    }

    #fade {
        display: none;
        position: fixed;
        top: 0%;
        left: 0%;
        width: 100%;
        height: 100%;
        background-color: black;
        z-index: 1001;
        -moz-opacity: 0.8;
        opacity: .80;
        filter: alpha(opacity=80);
    }

    #light {
        display: none;
        position: absolute;
        /*top: 50%;*/
        left: 50%;
        max-width: 600px;
        /*max-height: 360px;*/
        margin-left: -300px;
        margin-top: -180px;
        /*border: 2px solid #FFF;*/
        background: #FFF;
        z-index: 1002;
        overflow: visible;
    }

    #boxclose {
        float: right;
        cursor: pointer;
        color: #fff;
        border: 1px solid #AEAEAE;
        border-radius: 3px;
        background: #222222;
        font-size: 31px;
        font-weight: bold;
        display: inline-block;
        line-height: 0px;
        padding: 11px 3px;
        position: absolute;
        right: 2px;
        top: 2px;
        z-index: 1002;
        opacity: 0.9;
    }

    .boxclose:before {
        content: "×";
    }

    #fade:hover ~ #boxclose {
        display:none;
    }





    .modal-body p {
        display: block;
        width: 100%;
        line-height: 30px;
        clear: both;
    }

    .msg-white {
        color: white;
    }

    .msg-rd {
        color: #ed5565;
    }

    @media (max-width: 420px) {

        #modalSignup,
        #modalLogin,
        #modalForgot,
        #ritekhelamodalsearch {
            z-index: 111111;
            width: 300px;
            margin: 0 auto;
        }

        #modal-content {
            width: 97%;
        }

        .modal-dialog {
            position: relative;
            margin-left: 0.3rem;
            margin-right: 0.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            pointer-events: none;
        }

    }

    @media (max-width: 767px) {


        #light, #VisaChipCardVideo{
            max-width: 360px;
        }
        #light{
            left: 85%;
        }

        .sm-hide {
            display: none !important;
        }

        .sm-left {
            left: 20px;
            width: 50%;
        }

        .sm-right {
            right: 20px;
            width: 50%;
        }

    }

</style>

<style>
    #page-wrapper {

        margin: 0 !important;

    }

    #book_message {

        color: #ed5565;

        font-weight: bold;

    }

    .cal_slot_red {

        background-color: red;

        color: white;

    }

    .cal_slot_red,
    .fc-event-title {

        color: white;

    }

    .cal_slot_green {

        background-color: green;

        color: white;

    }

    .cal_slot_green,
    .fc-event-title {

        color: white;

    }

    .cal_slot_yellow {

        background-color: yellow;

        color: white;

    }

    .cal_slot_yellow,
    .fc-event-title {

        color: white;

    }

    .fc-past {
        background-color: #CCCCCC;
    }

    .fc-future {
        background-color: #FFF;
    }

    .cust_cli {
        cursor: pointer;
    }

    #calendardata,
    #slide_div {
        clear: both;
        display: block;
        widows: 100%;
        overflow-x: scroll;
    }

    .hide {
        display: none;
    }


    @media (max-width: 420px) {
        .col-xs-2 {
            width: 16%;
        }

        .col-xs-3 {
            width: 25%;
        }

        .col-xs-4 {
            width: 33%;
        }

        .col-xs-5 {
            width: 42%;
        }

        .col-xs-6 {
            width: 50%;
        }

        .col-xs-7 {
            width: 59%;
        }

        .col-xs-8 {
            width: 67%;
        }

        .col-xs-9 {
            width: 75%;
        }

        .col-xs-10 {
            width: 85%;
        }

        .col-xs-12 {
            width: 100%;
        }

    }

</style>

<?php /* ?><script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
<script>
window.OneSignal = window.OneSignal || [];
OneSignal.push(function () {
    OneSignal.init({
        appId: "6b5ca065-0699-4568-8729-8d66d6c85819",
    });
});
</script><?php */ ?>