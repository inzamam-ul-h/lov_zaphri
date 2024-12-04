@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,
	
	'show_title' => 1,
	'title' => 'Edit Category',
	
	'show_links' => 1,	
	'b1_title' => 'Categories',	
	'b1_route' => 'categories.index',
		
	'b2_title' => 'Edit',
	
	'show_buttons' => 1,
	'btn_back_route' => 'categories.index'	
];
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')  
@include('backend.layouts.portal.content_middle')

	<form  action="{{ route('categories.update',$Model_Data->id) }}" method="post">
		@csrf
		@method('put')
		<div class="content-group">

			<div class="row">
				<div class="col-sm-offset-2 col-sm-8">
					<h3 class="font-bold">Categories</h3>
					<hr />


					<div class="form-group row">

						<label class="col-sm-2 control-label">Name</label>

						<div class="col-sm-10">

							<div class="col-sm-12">

								<input type="text" class="form-control" name="name" placeholder="Category Name"
									size="75"  required="required" value="{{ $Model_Data->name }}">

							</div>

						</div>

					</div>


					<div class="form-group row">

						<label class="col-sm-2 control-label">Status</label>

						<div class="col-sm-10">

							<div class="col-sm-12">

								<select name="status" id="status" class="form-control" >
									<option value="1" selected="selected">Active</option>
									<option value="0" >Inactive</option>
								</select>

							</div>

						</div>

					</div>

					<div class="form-group row">

						<div class="col-sm-offset-10 col-sm-4">

							<input type="submit" class="btn btn-primary sub-btn" value="Update" name="submit_forms">

						</div>

					</div>

				</div>

			</div>

		</div>
	</form>	

@include('backend.layouts.portal.content_lower')					
@include('backend.layouts.portal.content_bottom')

@endsection
