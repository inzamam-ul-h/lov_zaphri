<div class="table table-striped table-hover">

    <table class="table table-striped table-hover" id="myDataTable">
        <thead>
            <tr role ="row" class="heading">
                <th>S_no</th>
                <th>Date</th>
                <th>Time </th>
                <th> Type</th>
                @if ($AUTH_USER->user_type != 1)
                <th>Coach</th>
                @endif
                <th>Player</th>
                <th>Payment status</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

@include('backend.layouts.portal.content_lower')

@else

@include('backend.layouts.portal.no_records')

@endif

@include('backend.layouts.portal.content_bottom')


@endsection

@if($records_exists == 1)

@section('headerInclude')
@include('datatables.css')
@endsection

@section('footerInclude')
@include('datatables.js')
@endsection

@endif

@push('scripts')
<script>
<?php
$datatable = datatable_helpers();
?>

jQuery(document).ready(function(e)
{
$('.dropdown-toggle').dropdown();
<?php
if($records_exists == 1)

{
?>
var oTable = $('#myDataTable').DataTable(
{
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
        url: "{!! route('sessions.upcoming.datatable') !!}",

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
            data: 'end_date',
            name: 'end_date'
        },
        {
            data: 'type',
            name: 'type'
        }, @if ($AUTH_USER->user_type != 1)
        {
            data: 'coach',
            name: 'coach'
        },
        @endif	{
            data: 'player',
            name: 'player'
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

    oTable.draw();

    e.preventDefault();

});
<?php
}
?>

});

</script>
@endpush
