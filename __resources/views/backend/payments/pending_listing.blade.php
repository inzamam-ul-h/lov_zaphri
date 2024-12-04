@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Payments',

	'show_links' => 1,
	'b1_title' => 'Payments',
	'b1_route' => 'payments.index',

	 'show_buttons' => 1,
	'btn_dashboad_route' => 'payments.create'

];
if($records_exists == 1){
	$data['btn_filters'] = 1;
}
if(Auth::user()->can('payments-add') || Auth::user()->can('all')){
	$data['btn_add_route'] = 'payments.create';
}
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
{{-- {{ dd($exists) }} --}}
	@if($records_exists == 1)

		<div class="row collapse" id="datatable_filters">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<form method="post" role="form" id="data-search-form">
							<div class="form-group">

								<div class="row">
									@include('data_filters.s_name')
									@include('data_filters.s_email')
									@include('data_filters.s_phone')
									@include('data_filters.s_phone')
									@include('data_filters.s_created_at')
									@include('data_filters.s_status')
								</div>

								@include('data_filters.action_buttons')

							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		@include('backend.layouts.portal.content_middle')

		<div class="table table-striped table-hover">

			<table class="table table-striped table-hover" id="myDataTableHistory">

        <thead>
            <tr role ="row" class="heading table-heading">
                <th >S_no</th>
                <th>DateTime</th>
                <th>Type </th>
                <th> Player</th>

                <th>Payment Date</th>
                <th>Transction ID</th>
                <th>Payment Status</th>
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
    var oTable = $('#myDataTableHistory').DataTable(
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
        processing: true,
        serverSide: true,
        ajax: {

        url: "{!! route('payments.pending.datatable') !!}",

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
            data: 'datetime',
            name: 'datetime'
        },
        {
            data: 'type',
            name: 'type'
        },
         {
            data: 'player',
            name: 'player'
        },

        {
            data: 'payment_date',
            name: 'payment_date'
        },	{
            data: 'transaction_id',
            name: 'transaction_id'
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
