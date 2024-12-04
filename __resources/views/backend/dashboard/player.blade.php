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


<?php
$table_data = [
    'table_caption'  => "Today's Sessions",
    'table_id'    => "myDataTable1",
    'table_url' => route('dashboard.player_upc_sessions_datatable')
];
?>
@include('datatables.player_session_table', $table_data)

@include('backend.layouts.portal.content_bottom')


<div class="modal inmodal" id="upModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <form class="loginmodalbox-search" action="{{url('/search')}}" method="get" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Cancel</span>
                    </button>
                    <h4 class="modal-title">Search Coach Availabilities</h4>
                    <p id="log_message"></p>
                </div>
                <div class="modal-body" id="modal-body_2">
                    <div class="form-group row">


                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label">Start Date</label>
                                <input class="form-control" type="date" name="start" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>">
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">End Date</label>
                                <input class="form-control" type="date" name="end" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d",strtotime("+30 days")); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <input type="submit" value="Search Now" class="btn btn-primary">

                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('headerInclude')
@include('datatables.css')
@endsection

@section('footerInclude')
@include('datatables.js')
@endsection

@push('scripts')
<style>
    .table-caption {
        font-size: 2.2rem;
        font-weight: bold;
        color: #333;
        padding: 10px 0;
    }
    .table-heading {
        font-weight: bold;
        background-color: #f2f2f2;
    }
</style>

@endpush
