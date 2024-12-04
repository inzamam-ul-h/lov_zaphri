
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'View Video Details: '.$Model_Data->title,

	'show_links' => 1,

	'b1_title' => 'Videos',
	'b1_route' => 'videos.index',

	'b2_title' => 'Video Details',

	'show_buttons' => 1,

    'btn_back_route'=>'videos.index',
];
if(Auth::user()->can('videos-edit') || Auth::user()->can('all')){
	$data['btn_edit_route'] = 'videos.edit';
	$data['edit_record_id'] = $Model_Data->id;
}
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

	<div class="wrapper wrapper-content">
        <div class="row animated fadeInRight">
            <div class="row mt-10">
                <div class="col-sm-offset-1 col-sm-10 row mt-10">
                    <div class="col-lg-12 mt-10">
                        <h3 class="font-bold">Video Details:</h3>
                        <hr>
                    </div>
					<div class="form-group row">
    					<div class="col-lg-6 col-md-6 col-sm-12">
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
								   <div style="display: flex;">
									<label class="col-sm-4 control-label">Title : </label>
									{{$Model_Data->title}}
								   </div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Category : </label>
									 {{ $Model_Data->category_name}}
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Duration : </label>
									 {{ $Model_Data->duration}}
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Recipients : </label> 

									@if($Model_Data->recipients == '1')
										Both
									@elseif($Model_Data->recipients == '2')
										Players
									@elseif($Model_Data->recipients == '3')
										Coach
									@endif
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label"> Description: </label>
									 {{$Model_Data->description}}
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Status : </label>
									 	@if($Model_Data->status == '0')
									 		Inactive
									 	@elseif($Model_Data->status == '1')
									 		Active
										@endif
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Created At : </label>
									 {{$Model_Data->created_at}}
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Created by : </label>
									 {{$Model_Data->user_name}}
									</div>
								 </div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12">
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<label class="control-label">Video </label><br>
									<video controls>
										<source id="video_src" class="img-fluid view-img" src="{{ asset(upload_url( 'videos/'.$Model_Data->id.'/'.$Model_Data->video) )}}" width="10" height="10" type="video/mp4">
										Your browser does not support the video tag.
									</video>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-lg-12 mt-10">
							<hr>
							<h3 class="font-bold">Uploads :</h3>
							<hr>
						</div>
					</div>
                	<div class="col-lg-12 mt-10">
						<div class="form-group row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="form-group row">
									<div class="col-lg-12 col-md-12 col-sm-12">
										<label class="control-label">Images </label><br>
										<div class="row">
											<?php
											$video_id = $Model_Data->id;
											$SITE_URL = env('APP_URL');
											$defaultImage = asset(upload_url('defaults/video.png'));
											$uploadsPath = asset(upload_url('videos/'.$video_id));
											$event_images = $defaultImage;
											if(!empty($Model_Data->image) && $Model_Data->image != 'default_image'){
												$Model_Data->image = trim(str_replace('default_image,', '', $Model_Data->image));
												$Model_Data->image = trim(str_replace(',default_image', '', $Model_Data->image));
												$Model_Data->image = trim(str_replace('default_image', '', $Model_Data->image));
												$images = $Model_Data->image;
												$arr = explode(",", $images);
												$event_images = $uploadsPath. "/" . $arr[0];
												$images = explode(",", $Model_Data->image);
												$count= count($images);
												foreach ($images as $image) {
													if((empty($image) || $image == 'default_image') && $count > 1){

													}else{
														$event_images = $defaultImage;
														if(!empty($image) && $image != 'default_image'){
															$event_images = $uploadsPath . "/" . $image;
															?>
															<div class="col-lg-3 col-md-4 col-sm-6">
																<img src="{{$event_images }}" style="width:100%">
															</div>
															<?php
														}

													}
												}
											}
											?>
										</div>
									</div>
								</div>
							</div>							
						</div>
                 	</div>
            	</div>
        	</div>
        </div>
	</div>
@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')

@endsection
