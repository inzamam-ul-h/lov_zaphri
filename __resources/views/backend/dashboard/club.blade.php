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
                <h5 >All Associations</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $total_associations }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Players Associated</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $total_players }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Coaches Associated</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $total_coaches }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>All Videos</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $total_videos }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Videos For players</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $playerVideosCount }}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="ibox ">
            <div class="ibox-title">
                <h5>Videos For Coaches</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $coachVideosCount}}</h1>
                <small>Total</small>
            </div>
        </div>
    </div>
</div>


@include('backend.layouts.portal.content_middle')
<div class="table table-striped table-hover">
    <table id="myDataTable" class="table" style="width:100%">
        <caption class="table-caption">All Requests</caption>
        <thead>
            <tr role ="row" class="heading table-heading">
                <th>S_no</th>
                <th>User</th>
                <th>Type</th>
                <th>Email</th>
                <th>Phone No.</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@include('backend.layouts.portal.content_lower')

@include('backend.layouts.portal.content_bottom')
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
<script>
<?php
$datatable = datatable_helpers();
?>

    jQuery(document).ready(function(e)
            {
            $('.dropdown-toggle').dropdown();
            var oTable = $('#myDataTable').DataTable(
            {
            "lengthChange": false,
                    "paging": false,
                    pageLength:  <?= $datatable['pageLength']; ?>,
                    lengthMenu:  <?= $datatable['lengthMenu']; ?>,
                    processing:  <?= $datatable['processing']; ?>,
                    serverSide:  <?= $datatable['serverSide']; ?>,
                    stateSave:   <?= $datatable['stateSave']; ?>,
                    searching:   <?= $datatable['searching']; ?>,
                    Filter:      <?= $datatable['Filter']; ?>,
                    dom :       ' <?= $datatable['dom']; ?>',
                    autoWidth:   <?= $datatable['autoWidth']; ?>,
                    buttons:
            [
                    @if ($datatable['buttons_excel'])
            {
            extend: 'excel',
                    exportOptions: {
                    columns: ':visible'
                    }
            },
                    @endif

                    @if ($datatable['buttons_pdf'])
            {
            extend: 'pdf',
                    exportOptions: {
                    columns: ':visible'
                    }
            },
                    @endif

                    @if ($datatable['buttons_print'])
            {
            extend: 'print',
                    exportOptions: {
                    columns: ':visible'
                    }
            },
                    @endif

                    @if ($datatable['buttons_colvis'])
                    'colvis'
                    @endif
            ],
                    columnDefs:
            [
            {
            targets: - 1,
                    visible: true
            }
            ],
                    ajax:
            {
            url: "{!! route('dashboard.club_all_requests_databale') !!}",
            },
                    columns:
            [
            {
            data: 'sr_no',
                    name: 'sr_no',
                    render: function (data, type, row, meta)
                    {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
            },
            {
            data: 'name',
                    name: 'name'
            },
            {
            data: 'user_type',
                    name: 'user_type'
            },
            {
            data: 'email',
                    name: 'email'
            },
            {
            data: 'phone',
                    name: 'phone'
            },
            {
            data: 'action',
                    name: 'action'
            },
            ],
            });
            });

</script>

@endpush
