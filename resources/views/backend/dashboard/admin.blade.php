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

@include('backend.layouts.portal.content_middle')
<div class="table table-striped table-hover">
    <table id="myDataTable1" class="table" style="width:100%">
        <caption class="table-caption">Clubs: Pending Approval</caption>
        <thead>
            <tr role ="row" class="heading table-heading">
                <th>S_no</th>

                <th>User</th>
                <th>User Type</th>
                <th>Email</th>
                <th>Phone No.</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<?php /* ?>{{-- <div class="table table-striped table-hover">
<table id="myDataTable1" class="table" style="width:100%">
        <caption class="table-caption">Upcomming Session</caption>
    <the        ad>
                    <tr role ="row" class="heading table-heading">
                    <th>S_no</th>
                        <th>Start Time</th>
                        <th>Session Date</th>
                        <th>Type</th>
                        <th>Coach</th>
                        <th>Player</th>
                        <th>Payment status</th>
                        <th>Price</th>
    </tr>
    </            thead>
    <tbody>
           </tb        ody>
</t        able>
    </d    iv> --}}<?php */ ?>

@include('backend.layouts.portal.content_lower')

<?php /* ?>@include('backend.layouts.portal.content_middle')
    <div class="table table-striped table-hover">
    <table id="myDataTable" class="table" style="width:100%">
        <caption class="table-caption">All Session</caption>
        <thead>
            <tr role ="row" class="heading table-heading">
                <th>S_no</th>
                <th>Start Time</th>
                <th>Session Date</th>
                <th>Type</th>
                <th>Coach</th>
                <th>Player</th>
                <th>Payment status</th>
                <th>Price</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    </div> 
    @include('backend.layouts.portal.content_lower')<?php */ ?>


    @include('backend.layouts.portal.content_bottom')
    @endsection

    {{-- @if($records_exists == 1  $records_exists2 == 1) --}}

    @section('headerInclude')
    @include('datatables.css')
    @endsection

    @section('footerInclude')
    @include('datatables.js')
    @endsection

    {{-- @endif --}}

    @push('scripts')
<style>
        .table-caption {
            font-size: 2.2rem;
            font-weight: bold;
            /* text-align: center; */
            color: #333; /* Change the color as needed */
            padding: 10px 0;
        }

        /* Table Heading Styles */
        .table-heading {
            font-weight: bold;
            background-color: #f2f2f2; /* Change the background color as needed */
            /* Add any other styles you want for the table heading cells */
        }
    </style>
    <script>
<?php
$datatable = datatable_helpers();
?>

        jQuery(document).ready(function(e)
        {
        $('.dropdown-toggle').dropdown();
<?php
// if($records_exists == 1)
{
    ?>
            var oTable = $('#myDataTable1').DataTable(
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

            url: "{!! route('dashboard.club_approve_datatable') !!}",
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
    <?php
}
?>
<?php
/* // if($records_exists == 1)

  // {
  ?>
  var oTable2 = $('#myDataTable').DataTable(
  {		      "lengthChange": false,
  pageLength:  <?=$datatable['pageLength'];?>,
  lengthMenu:  <?=$datatable['lengthMenu'];?>,
  processing:  <?=$datatable['processing'];?>,
  serverSide:  <?=$datatable['serverSide'];?>,
  stateSave:   <?=$datatable['stateSave'];?>,
  searching:   <?=$datatable['searching'];?>,
  Filter:      <?=$datatable['Filter'];?>,
  dom :       ' <?=$datatable['dom'];?>',
  autoWidth:   <?=$datatable['autoWidth'];?>,
  buttons:
  [
  @if($datatable['buttons_excel'])
  {
  extend: 'excel',
  exportOptions: {
  columns: ':visible'
  }
  },
  @endif

  @if($datatable['buttons_pdf'])
  {
  extend: 'pdf',
  exportOptions: {
  columns: ':visible'
  }
  },
  @endif

  @if($datatable['buttons_print'])
  {
  extend: 'print',
  exportOptions: {
  columns: ':visible'
  }
  },
  @endif

  @if($datatable['buttons_colvis'])
  'colvis'
  @endif
  ],
  columnDefs:
  [
  {
  targets: -1,
  visible: true
  }
  ],
  // processing: true,
  // serverSide: true,
  ajax:
  {
  //
  url: "{!! route('dashboard.admin_all_sessions_datatable') !!}",

  // data: function (d)
  // {
  // 	d.name = $('#s_name').val();

  // 	d.status = $('#s_status').val();

  // 	d.created_at = $('#s_created_at').val();

  // 	d.updated_at = $('#s_updated_at').val();
  // }
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
  data: 'start_date',
  name: 'start_date'
  },
  {
  data: 'session_date',
  name: 'session_date'
  },
  {
  data: 'type',
  name: 'type'
  },
  {
  data: 'coach_name',
  name: 'coach_name'
  },	{
  data: 'player_name',
  name: 'player_name'
  },

  {
  data: 'payment_status',
  name: 'payment_status'
  },		{
  data: 'price',
  name: 'price'
  },
  ],
  });

  $('#data-search-form').on('submit', function (e) {

  oTable2.draw();

  e.preventDefault();

  });
  <?php
  // } */
?>

        });

    </script>

    @endpush
