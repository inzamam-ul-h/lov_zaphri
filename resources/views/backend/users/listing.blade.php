@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
if ($user_type==1)
{
	$user='Coaches';
	$user_type_link='coaches';
}
else if($user_type==2){
	$user='Players';
	$user_type_link='players';
}
else if($user_type==3){
	$user='Clubs';
	$user_type_link='clubs';
}
else if($user_type==4){
	$user='Parents';
	$user_type_link='parents';
}
else {
	$user='Admins';
	$user_type_link='users';
}
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => $user,

	'show_links' => 1,
	'b1_title' => $user,
	'b1_route' => 'users.index',

	'show_buttons' => 1

];
if($records_exists == 1){
	$data['btn_filters'] = 1;
}
if(Auth::user()->user_type == 3 || (Auth::user()->can('users-add') || Auth::user()->can('all'))){
	$data['btn_add_route_type'] = 'users.create_user_by_type';
	$data['add_user_type'] = $user_type;
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
							<input type="hidden" id="s_user_type" value="<?php echo $user_type;?>">
							<div class="form-group">

								<div class="row">
									@include('data_filters.s_name')
									@include('data_filters.s_email')
									@include('data_filters.s_phone')
									@include('data_filters.s_status')
									@include('data_filters.s_created_at')
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
						<th>User Type</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Status</th>
						<th>Approved</th>
						<th>Created At</th>
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
			ajax:
			{
				url: "{!! route('users.datatables') !!}",
				data: function (d)
				{
					d.user_type = $('#s_user_type').val();
					d.name = $('#s_name').val();
                    d.email = $('#s_email').val();
                    d.phone = $('#s_phone').val();

					d.status = $('#s_status').val();

					d.created_at = $('#s_created_at').val();

					d.updated_at = $('#s_updated_at').val();
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
					data: 'user_type',
					name: 'user_type'
				},
				{
					data: 'name',
					name: 'name'
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
					data: 'status',
					name: 'status'
				},
				{
					data: 'admin_approved',
					name: 'admin_approved'
				},
                {
                    data: 'created_at',
                    name: 'created_at'
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
