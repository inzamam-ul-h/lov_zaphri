@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Edit Video Details: '.$Model_Data->title,

	'show_links' => 1,

	'b1_title' => 'Videos',
	'b1_route' => 'videos.index',

	'b2_title' => 'Video Details',

	'show_buttons' => 2,
	'btn_back_route' => 'videos.index'
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
                            <form name="settings_form" method="post" action="{{ route('videos.update',$Model_Data->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="content-group">
                                    <div class="row">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <h3 class="font-bold">Video Details</h3>
                                                </div>
                                            </div>
											<hr />
											<div class="form-group row">
												<div class="col-lg-6 col-md-6 col-sm-12">
													<label class="col-sm-4 control-label">Title *</label>
													<div class="col-sm-8">
														<input type="text"  maxlength="20" class="form-control validate" id="title" placeholder="Title" value="{{ $Model_Data->title }}" name="title" size="75" data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
														@if ($errors->has('title'))
															<span class="text-danger">{{ $errors->first('title') }}</span>
														@endif
													</div>
												</div>
												<div class="col-lg-6 col-md-6 col-sm-12">
													<label class="col-sm-4 control-label">Category *</label>
													<div class="col-sm-8">
														<select class="form-control" id="category" name="category" required>
															<option  value=""{{ !isset($Model_Data->category) ? ' selected' : '' }} >Choose Category</option>
															@foreach ($categories as $category)
																<option value="{{ $category->id }}" {{ $Model_Data->category == $category->id ? ' selected' : '' }}>{{ $category->name }}</option>
															@endforeach
														</select>
														@if ($errors->has('category'))
															<span class="text-danger">{{ $errors->first('category') }}</span>
														@endif
													</div>
												</div>
											</div>
											<div class="form-group row">
												<div class="col-lg-6 col-md-6 col-sm-12">
													<label class="col-sm-4 control-label">Duration *</label>
													<div class="col-sm-8">
                                                        <input class="html-duration-picker form-control" value="{{ $Model_Data->duration }}" id="duration" size="100" data-hide-seconds name="duration" pattern="^([0-5]?[0-9]:[0-5][0-9])?$" required>
														@if ($errors->has('duration'))
															<span class="text-danger">{{ $errors->first('duration') }}</span>
														@endif
													</div>
												</div>
												<div class="col-sm-6">
													<label class="col-sm-4 control-label">Description *</label>
													<div class="col-sm-8">
														<textarea class="form-control" placeholder="Your description" value="{{$Model_Data->description}}" name="description" id="description" rows="3" required >{{$Model_Data->description}}</textarea>
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
													<label class="col-sm-4 control-label">Image *</label>
													<div class="col-sm-8">
														<input type="file" class="form-control" value="{{ $Model_Data->image }}" name="image[]" accept="image/*" multiple>
														@if ($errors->has('image'))
															<span class="text-danger">{{ $errors->first('image') }}</span>
														@endif
													</div>
												</div>
												<div class="col-md-6">
													<label class="col-sm-4 control-label">Video *</label>
													<div class="col-sm-8">
														<input type="file" class="form-control"  name="video" accept="video/*">
														@if ($errors->has('video'))
															<span class="text-danger">{{ $errors->first('video') }}</span>
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
											<div class="form-group row">
												<div class="col-lg-6 col-md-6 col-sm-12">
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
																	$xy=0;
																	foreach ($images as $image) {
																		if((empty($image) || $image == 'default_image') && $count > 1){

																		}else{
																			$xy++;
																			$event_images = $defaultImage;
																			if(!empty($image) && $image != 'default_image'){
																				$file_path = $uploadsPath . "/" . $image;
																				?>
																				<div class="col-lg-3 col-md-4 col-sm-6" id="video_image_div_<?php echo $xy;?>">
                                                                                    <?php
																					$del_link = "videos/".$Model_Data->id."/image";
																					$del_file_name = $image;
																					?>
                                                                                        <a class="btn btn-danger delete-image"
																						   data-file_name="{{ $del_file_name }}"
																						   data-url="{{ url('manage/file-delete/'.$del_link) }}"
																						   data-parent="video_image_div_<?php echo $xy;?>"
																						   title="Delete File">
                                                                                            <i class="fa fa-trash fa-lg"></i> Delete
                                                                                        </a>
																					<img src="{{$file_path }}" style="height :80px;  width:100%">
                                                                                    @if (strpos($file_path, 'uploads/defaults') === false)

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
												<div class="col-lg-6 col-md-6 col-sm-12" id="video_video_div">
													<div class="form-group row">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<label class="control-label">Video </label><br>
                                                            <?php
                                                            $del_link = "videos/".$Model_Data->id."/video";
                                                            $del_file_name = trim($Model_Data->video);
                                                            ?>
                                                            <a class="btn btn-danger delete-image"
                                                               data-file_name="{{ $del_file_name }}"
                                                               data-url="{{ url('manage/file-delete/'.$del_link) }}"
                                                               data-parent="video_video_div"
                                                               title="Delete File">
                                                                <i class="fa fa-trash fa-lg"></i> Delete
                                                            </a>
                                                           	<?php $file_path = asset(upload_url( 'videos/'.$Model_Data->id.'/'.$Model_Data->video) ) ?>
															<video controls>
																<source id="video_src" class="img-fluid view-img" src="{{ $file_path}}" width="10" height="10" type="video/mp4">
																Your browser does not support the video tag.
															</video>
                                                            @if (strpos($file_path, 'uploads/defaults') === false)

                                                            @endif
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


