@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$user_id = $AUTH_USER->id;
$Site_Title = Site_Settings($Settings, 'site_title');
$data = [
    'show_breadcrumb' => 1,
    'show_title'      => 1,
    'title'           => dashboard_greetings($AUTH_USER),
    'show_subtitle'   => 1,
    'subtitle'        => "Welcome back to <strong>" . $Site_Title . "!</strong>",    
    'show_dashboard_buttons' => 1,
];
?>
@include('backend.layouts.portal.breadcrumb', $data)
@include('backend.layouts.portal.content_top')

<div class="row sm-hide m-3">
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5 >All Sessions</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $sessions_total }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Expired</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $sessions_expired }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Canceled</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $sessions_canceled }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Delivered</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $sessions_delivered }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Upcoming</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $sessions_upcoming }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Today</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $sessions_today ?? 0 }}</h1>

                <small>Total</small>
            </div>
        </div>
    </div>
</div>

@include('backend.layouts.portal.content_bottom')
@endsection

