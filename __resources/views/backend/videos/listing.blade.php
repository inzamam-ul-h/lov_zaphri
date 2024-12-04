@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Videos',

	'show_links' => 1,
	'b1_title' => 'Videos',
	'b1_route' => 'videos.index',
	'show_buttons' => 1,
	// 'b2_title' => 'Edit',

];
if($records_exists == 1){
	$data['btn_filters'] = 1;
}
if(Auth::user()->can('videos-add') || Auth::user()->can('all')){
	$data['btn_add_route'] = 'videos.create';
}
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')

	@if($records_exists == 1)

		<div class="row collapse" id="datatable_filters">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body">
						<form method="post" role="form" id="data-search-form">
							<div class="form-group">

								<div class="row">
									@include('data_filters.s_title')
									@include('data_filters.s_category')
									@include('data_filters.s_user_type')
                                    @include('data_filters.s_name')
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

			<table class="table table-striped table-hover" id="myDataTable">
				<thead>
					<tr role ="row" class="heading">
						<th>S_no</th>
						<th>Title</th>
						<th>User </th>
						<th>User Type</th>
						<th>category</th>
						<th>Status</th>
						<th>created by</th>
						<th>Action</th>

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
				url: "{!! route('videos.datatable') !!}",

				data: function (d)
				{
					d.title = $('#s_title').val();

					d.status = $('#s_status').val();

					 d.name = $('#s_name').val();

                     d.category = $('#s_category').val();
					d.user_type = $('#s_user_type').val();
				}
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
					data: 'title',
					name: 'title'
				},
				 {
					data: 'user',
					name: 'user'
				},
				{
					data: 'user_type',
					name: 'user_type'
				},
				{
					data: 'category',
					name: 'category'
				},	{
					data: 'status',
					name: 'status'
				},
                {
					data: 'created_by',
					name: 'created_by'
				},

				{
					data: 'action',
					name: 'action'
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
