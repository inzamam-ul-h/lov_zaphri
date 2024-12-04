
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'View Event: '.$Model_Data->title,

	'show_links' => 1,

	'b1_title' => 'Events',
	'b1_route' => 'events.index',

	'b2_title'=> 'Event Details',

	'show_buttons' => 1,
    'btn_back_route'=>'events.index',
];
if(Auth::user()->can('events-edit') || Auth::user()->can('all')){
	$data['btn_edit_route'] = 'events.edit';
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
                        <h3 class="font-bold">Event Details:</h3>
                        <hr>
                    </div>
					<div class="form-group row">
    					<div class="col-lg-8 col-md-8 col-sm-12">
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
									 <label class="col-sm-4 control-label">Age Group : </label>
									 {{$Model_Data->age_group}}
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;">
									 <label class="col-sm-4 control-label">Start Date : </label>
									 {{$Model_Data->start_date_time}}
									</div>
								 </div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div style="display: flex;" >
									 <label class="col-sm-4 control-label">Meeting link : </label>
									 <a href="{{$Model_Data->meeting_link}}" style = " color: #0e0d0d;" > {{$Model_Data->meeting_link}}</a>
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
						</div>
						<div class="col-lg-4 col-md-4 col-sm-12">
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<img src="{{ asset(upload_url( 'events/'.$Model_Data->id.'/'.$Model_Data->banner) )}}" alt="here the image view" style="width: 100%; height: 300px;">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<label class="control-label" for="photo">Banner</label>
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
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="form-group row">
									<div class="col-lg-12 col-md-12 col-sm-12">
										<label class="control-label">Documents </label><br>
										<div class="row">
											<?php
											$event_id = $Model_Data->id;
											$SITE_URL = env('APP_URL');
											$defaultImage = asset(upload_url('defaults/video.png'));
											$uploadsPath = asset(upload_url('events/'.$event_id));
											$event_images = $defaultImage;
											if(!empty($Model_Data->documents) && $Model_Data->documents != 'default_image'){
												$Model_Data->documents = trim(str_replace('default_image,', '', $Model_Data->documents));
												$Model_Data->documents = trim(str_replace(',default_image', '', $Model_Data->documents));
												$Model_Data->documents = trim(str_replace('default_image', '', $Model_Data->documents));
												$images = $Model_Data->documents;
												$arr = explode(",", $images);
												$event_images = $uploadsPath. "/" . $arr[0];
												$images = explode(",", $Model_Data->documents);
                                                $original_documents_name =  explode(",", $Model_Data->original_documents_name);
                                                $counter= count($original_documents_name);
												$count= count($images);
                                                $i = 0;
												foreach ($images as $image) {
                                                    if($i < $counter){
                                                            if((empty($image) || $image == 'default_image') && $count > 1){

                                                            }else{
                                                                $event_images = $defaultImage;
                                                                if(!empty($image) && $image != 'default_image'){
                                                                    $event_images = $uploadsPath . "/" . $image;
                                                                    ?>
                                                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                                                        <a class="btn btn-info" href="{{$event_images }}" target="_blank">
                                                                        {{$original_documents_name[$i] }}
                                                                        </a>
                                                                    </div>
                                                                    <?php
                                                                }

                                                            }
                                                            $i++;
                                                        }

                                                    }
											}
											?>
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-12 col-md-12 col-sm-12">
										<label class="control-label">Images </label><br>
										<div class="row">
											<?php
											$event_id = $Model_Data->id;
											$SITE_URL = env('APP_URL');
											$defaultImage = asset(upload_url('defaults/video.png'));
											$uploadsPath = asset(upload_url('events/'.$event_id));
											$event_images = $defaultImage;
											if(!empty($Model_Data->images) && $Model_Data->images != 'default_image'){
												$Model_Data->images = trim(str_replace('default_image,', '', $Model_Data->images));
												$Model_Data->images = trim(str_replace(',default_image', '', $Model_Data->images));
												$Model_Data->images = trim(str_replace('default_image', '', $Model_Data->images));
												$images = $Model_Data->images;
												$arr = explode(",", $images);
												$event_images = $uploadsPath. "/" . $arr[0];
												$images = explode(",", $Model_Data->images);
												$count= count($images);
												foreach ($images as $image) {
													if((empty($image) || $image == 'default_image') && $count > 1){

													}else{
														$event_images = $defaultImage;
														if(!empty($image) && $image != 'default_image'){
															$event_images = $uploadsPath . "/" . $image;
															?>
															<div class="col-lg-4 col-md-4 col-sm-6">
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
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="form-group row">
									<div class="col-lg-12 col-md-12 col-sm-12">
										<label class="control-label">Video </label><br>
										<video controls>
											<source id="video_src" class="img-fluid view-img" src="{{ asset(upload_url( 'events/'.$Model_Data->id.'/'.$Model_Data->video) )}}" width="10" height="10" type="video/mp4">
											Your browser does not support the video tag.
										</video>
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

