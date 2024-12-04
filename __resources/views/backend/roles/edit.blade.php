@extends('backend.layouts.portal')

@section('content')
    <?php
    $AUTH_USER = Auth::user();

    $data = [
        'show_breadcrumb' => 1,

        'show_title' => 1,
        'title' => 'Edit Role: '.$Model_Data->name,

        'show_links' => 1,

        'b1_title' => 'Roles',
        'b1_route' => 'roles.index',

        'b2_title' =>'Edit Role Details',

        'show_buttons' => 1,
        'btn_back_route' => 'roles.index',
    ];

    ?>

    @include('backend.layouts.portal.breadcrumb', $data)

    @include('backend.layouts.portal.content_top')

		@include('backend.layouts.portal.content_middle')
		<div class="row mt-10">
			<div class="col-lg-12 row mt-10">
				<div class="col-lg-12 mt-10">
					<h3 class="font-bold">Role Details</h3>
					<hr>
                        <?php /*?>{!! Form::model($Model_Data, ['route' => ['roles.update', $Model_Data->id], 'method' => 'patch']) !!}

                        @include('backend.roles.fields')

                        {!! Form::close() !!}<?php */?>

						<div class="form-group">
                            <div class="row">
                                <div class="col-sm-6 row">
                                    <div class="col-sm-4">
                                        {!! Form::label('name', 'Name:') !!}
                                    </div>
                                    <div class="col-sm-8">
                                        <p>{{ $Model_Data->name }}</p>
                                    </div>
                                </div>
                                <div class="col-sm-6 row">
                                    <div class="col-sm-4">
                                        {!! Form::label('display_to', 'Role For:') !!}
                                    </div>
                                    <div class="col-sm-8">
                                        <p>
                                            <?php
                                            if($Model_Data->display_to == 0){
                                                echo 'Admin Users Only';
                                            }
                                            else{
                                                echo 'Others';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row mt-2">
                                <div class="col-sm-6 row">
                                    <div class="col-sm-4">
                                        {!! Form::label('created_at', 'Created At:') !!}
                                    </div>
                                    <div class="col-sm-8">
                                        <p>{{ $Model_Data->created_at }}</p>
                                    </div>
                                </div>
                                <div class="col-sm-6 row">
                                    <div class="col-sm-4">
                                        {!! Form::label('updated_at', 'Updated At:') !!}
                                    </div>
                                    <div class="col-sm-8">
                                        <p>{{ $Model_Data->updated_at }}</p>
                                    </div>
                                </div>
                            </div>
                    	</div>
				</div>
			</div>
		</div>
		@include('backend.layouts.portal.content_lower')

		@include('backend.layouts.portal.content_middle')
		<div class="row mt-10">
			<div class="col-lg-12 row mt-10">
				<div class="col-lg-12 mt-10">
					<h3 class="font-bold">Role Permissions</h3>
					<hr>
                    <div class="card-body" style="display: none;">
                        @include('backend.roles.show_permissions')
                        <hr/>
                        <div class="row mt-2">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-primary" onclick="edit_form()">Edit Permissions</button>
                                <?php echo cancel_button('roles.index');?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" >
                        {!! Form::open(['route' => ['permissions_update',$Model_Data->id]]) !!}
                        	@include('backend.roles.edit_permissions')        
                            <hr/>
                            <div class="row mt-2">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                                    <!-- <button type="button" class="btn btn-secondary" onclick="display_form()">Cancel</button> -->
                                </div>
                            </div>
                        
                        {!! Form::close() !!}      
                        
                    </div>
				</div>
			</div>
		</div>
		@include('backend.layouts.portal.content_lower')

	@include('backend.layouts.portal.content_bottom')

@endsection

@push('scripts')    
    <script>
        $(document).ready(function(e) {
            $('.radioBtn a').on('click', function(){
                var sel = $(this).data('title');
                var tog = $(this).data('toggle');
                $('#'+tog).prop('value', sel);

                $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
                $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
            });
			
            $('.radioBtnAll a').on('click', function()
			{
                var sel = $(this).data('title');
                var tog = $(this).data('toggle');

                $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
                $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				
				if($('#id_list_'+tog))
				{
					$('#id_list_'+tog).prop('value', sel);					

					$('a[data-toggle="id_list_'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
					$('a[data-toggle="id_list_'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				}
				
				if($('#id_add_'+tog))
				{
					$('#id_add_'+tog).prop('value', sel);				

					$('a[data-toggle="id_add_'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
					$('a[data-toggle="id_add_'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				}
				
				if($('#id_edit_'+tog))
				{
					$('#id_edit_'+tog).prop('value', sel);				

					$('a[data-toggle="id_edit_'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
					$('a[data-toggle="id_edit_'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				}
				
				if($('#id_view_'+tog))
				{
					$('#id_view_'+tog).prop('value', sel);				

					$('a[data-toggle="id_view_'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
					$('a[data-toggle="id_view_'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				}
				
				if($('#id_status_'+tog))
				{
					$('#id_status_'+tog).prop('value', sel);				

					$('a[data-toggle="id_status_'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
					$('a[data-toggle="id_status_'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				}
				
				if($('#id_delete_'+tog))
				{
					$('#id_delete_'+tog).prop('value', sel);				

					$('a[data-toggle="id_delete_'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
					$('a[data-toggle="id_delete_'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
				}
            });
        });
    </script>
@endpush