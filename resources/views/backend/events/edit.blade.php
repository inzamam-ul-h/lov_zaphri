
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Edit Event: '.$Model_Data->title,

	'show_links' => 1,

	'b1_title' => 'Events',
	'b1_route' => 'events.index',

	'b2_title'=> 'Event Details',

	'show_buttons' => 1,
	'btn_back_route' => 'events.index'
];
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <div id="#">
                            <form name="settings_form" method="post" action="{{ route('events.update',$Model_Data->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="content-group">
                                    <div class="row">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <h3 class="font-bold">Event Details</h3>
                                                </div>
                                            </div>
                                            <hr />
                                            <div class="form-group row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="col-sm-3 control-label">Title *</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" value="{{ $Model_Data->title }}"  maxlength="20" class="form-control validate" id="title" placeholder="Title" name="title" size="75" data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
                                                        @if ($errors->has('title'))
                                                            <span class="text-danger">{{ $errors->first('title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="col-sm-3 control-label">Age Group *</label>
													<div class="col-sm-4">
														<select class="form-control"  id="age_group" name="age_group" required>
															<option value=""{{ !isset($Model_Data->age_group) ? ' selected' : '' }}>Choose Age Group</option>
															@foreach ($age_group as $age_groups)
																<option value="{{ $age_groups->id }}"{{ $Model_Data->age_group == $age_groups->id ? ' selected' : '' }}>
																	{{ $age_groups->title }}
																</option>
															@endforeach
														</select>
														@if ($errors->has('age_group'))
															<span class="text-danger">{{ $errors->first('age_group') }}</span>
														@endif
													</div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="col-sm-3 control-label">Start Date Time *</label>
                                                    <div class="col-sm-4">
                                                        <input type="date" class="form-control" id="start_date_time" placeholder="start_date_time" value="{{ old('start_date_time', $Model_Data->start_date_time)}}" name="start_date_time" size="75" min="{{ date('Y-m-d') }}" required>
                                                        @if ($errors->has('duration'))
                                                            <span class="text-danger">{{ $errors->first('duration') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="col-sm-3 control-label">Meeting Link *</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" value="{{ $Model_Data->meeting_link }}" class="form-control " id="meeting_link" placeholder="Meeting Link" name="meeting_link" size="75"required>
                                                         @if ($errors->has('meeting_link'))
                                                            <span class="text-danger">{{ $errors->first('meeting_link') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="col-sm-3 control-label">Description *</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control" placeholder="Your description" name="description" id="description" rows="3" required >{{$Model_Data->description}}</textarea>
                                                        @if ($errors->has('description'))
                                                            <span class="text-danger">{{ $errors->first('description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                									<hr>
                                                    <h3 class="font-bold">Uploads</h3>
                									<hr>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">banner *</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="banner" accept="image/*" >
                                                        @if ($errors->has('banner'))
                                                            <span class="text-danger">{{ $errors->first('banner') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">Video *</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="video" accept="video/*">
                                                        @if ($errors->has('video'))
                                                            <span class="text-danger">{{ $errors->first('video') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">Images *</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="images[]" accept="image/*" multiple >
														<small> (Select Multiple)</small>
                                                        @if ($errors->has('image'))
                                                            <span class="text-danger">{{ $errors->first('image') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">Documents *</label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="documents[]"  multiple >
														<small> (Select Multiple)</small>
                                                        @if ($errors->has('document'))
                                                            <span class="text-danger">{{ $errors->first('document') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
												<div class="col-lg-12 mt-10">
													<hr>
													<h3 class="font-bold">Uploaded :</h3>
													<hr>
												</div>
											</div>
											<div class="col-lg-12 mt-10">
												<div class="form-group row">
													<div class="col-lg-6 col-md-6 col-sm-12">
														<div class="form-group row" id="event_banner_div">
															<div class="col-lg-12 col-md-12 col-sm-12">
																<label class="control-label">Banner</label><br>
																<div class="form-group row">
																	<div class="col-lg-12 col-md-12 col-sm-12">
                                                                        <?php
																		$file_path = asset(upload_url( 'events/'.$Model_Data->id.'/'.$Model_Data->banner) );
																		?>
																		<img src="{{ $file_path}}" alt="here the image view" style="width: 100%; height: 300px;">
                                                                        @if (strpos($file_path, 'uploads/defaults') === false)
																			<?php
																			$del_link = "events/".$Model_Data->id."/banner";
																			$del_file_name = trim($Model_Data->banner);
																			?>
																			<a class="btn btn-danger delete-confirm"
																			   data-file_name="{{ $del_file_name }}"
																			   data-url="{{ url('manage/file-delete/'.$del_link) }}"
																			   data-parent="event_banner_div"
																			   title="Delete File">
																				<i class="fa fa-trash fa-lg"></i> Delete
																			</a>
																		@endif
                                                                    </div>
																</div>
															</div>
														</div>
														<div class="form-group row">
															<div class="col-lg-12 col-md-12 col-sm-12">
																<label class="control-label">Documents</label><br>
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
																		$count= count($images);
                                                                        $original_documents_name =  explode(",", $Model_Data->original_documents_name);
                                                                        $counter= count($original_documents_name);
																		$xy=0;
                                                                        $i = 0;
																		foreach ($images as $image) {
                                                                            if($i < $counter){
																			if((empty($image) || $image == 'default_image') && $count > 1){

																			}else{
																				$xy++;
																				$event_images = $defaultImage;
																				if(!empty($image) && $image != 'default_image'){
																					$file_path = $uploadsPath . "/" . $image;
																					?>
																					<div class="col-lg-4 col-md-4 col-sm-6" id="event_documents_div_<?php echo $xy;?>">
																						<a class="btn btn-info" href="{{$file_path }}" target="_blank">
																						{{$original_documents_name[$i]}}
																						</a>
                                                                                        @if (strpos($file_path, 'uploads/defaults') === false)
																							<?php
																							$del_link = "events/".$Model_Data->id."/documents";
																							$del_file_name = trim($image);
																							?>
																							<a class="btn btn-danger delete-confirm"
													   										   data-file_name="{{ $del_file_name }}"
																							   data-url ="{{ url('manage/file-delete/'.$del_link) }}"
																							   data-parent="event_documents_div_<?php echo $xy;?>"
																							   title="Delete File">
																								<i class="fa fa-trash fa-lg"></i> Delete
																							</a>
																						@endif
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
																<label class="control-label">Images</label><br>
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
																		$xy=0;
																		foreach ($images as $image) {
																			if((empty($image) || $image == 'default_image') && $count > 1){

																			}else{
																				$xy++;
																				$event_images = $defaultImage;
																				if(!empty($image) && $image != 'default_image'){
																					$file_path = $uploadsPath . "/" . $image;
																					?>
																					<div class="col-lg-4 col-md-4 col-sm-6" id="event_images_div_<?php echo $xy;?>">
																						<img src="{{$file_path }}" style="width:100%">
                                                                                        @if (strpos($file_path, 'uploads/defaults') === false)
																							<?php
																							$del_link = "events/".$Model_Data->id."/images";
																							$del_file_name = trim($image);
																							?>
																							<a class="btn btn-danger delete-confirm"
																							   data-file_name="{{ $del_file_name }}"
																							   data-url="{{ url('manage/file-delete/'.$del_link) }}"
																							   data-parent="event_images_div_<?php echo $xy;?>"
																							   title="Delete File">
																								<i class="fa fa-trash fa-lg"></i> Delete
																							</a>
																						@endif
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
													<div class="col-lg-6 col-md-6 col-sm-12" id="event_video_div">
														<div class="form-group row">
															<div class="col-lg-12 col-md-12 col-sm-12">
																<label class="control-label">Video </label><br>
                                                                <?php $file_path = asset(upload_url( 'events/'.$Model_Data->id.'/'.$Model_Data->video) ) ?>
																<video controls>
                                                                    <source id="video_src" class="img-fluid view-img" src="{{ $file_path}}" width="10" height="10" type="video/mp4">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                                @if (strpos($file_path, 'uploads/defaults') === false)
                                                                    <?php
																	$del_link = "events/".$Model_Data->id."/video";
																	$del_file_name = trim($Model_Data->video);
																	?>
                                                                    <a class="btn btn-danger delete-confirm"
																	   data-file_name="{{ $del_file_name }}"
																	   data-url="{{ url('manage/file-delete/'.$del_link) }}"
																	   data-parent="event_video_div"
																	   title="Delete File">
                                                                        <i class="fa fa-trash fa-lg"></i> Delete
                                                                    </a>
                                                                @endif
															</div>
														</div>
													</div>
												</div>
											</div>

											<hr />
											<div class="form-group row">
												<div class="col-sm-10 col-xs-6">
													<label class="control-label">* indicates that fields are mandatory.</label>
												</div>
												<div class="col-sm-2 col-xs-6">
													<button type="submit"  class="btn btn-primary sub-btn">Update</button>
												</div>
											</div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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

