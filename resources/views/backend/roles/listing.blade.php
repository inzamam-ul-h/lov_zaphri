@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Roles',

	'show_links' => 1,
	
	'b1_title' => 'Roles',
	'b1_route' => 'roles.index',
	
	'show_buttons' => 1
];
if($records_exists == 1){
	$data['btn_filters'] = 1;
}
if(Auth::user()->can('users-add') || Auth::user()->can('all')){
	$data['btn_add_route'] = 'roles.create';
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
			<div class="table-responsive">
				<table class="table table-striped table-hover"  id="myDataTable">
					<thead>
						<tr role="row" class="heading">
							<th>SN</th>
							<th>Title</th>
							<th>Role for</th>                                               
							<th>Created at</th>
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
                url: "{!! route('roles.datatable') !!}",
                data: function (d) 
				{  
                    d.title = $('#s_title').val();
					
					d.created_at = $('#s_created_at').val();
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
					data: 'display_to', 
					name: 'display_to'
				},

                {
                    data: 'updated_at', 
                    name: 'updated_at'
                },
								
				{
					data: 'action', 
					name: 'action'
				}
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