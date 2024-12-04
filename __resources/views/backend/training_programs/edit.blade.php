@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => ' Edit Training Program: '.$Model_Data->title,

	'show_links' => 1,

	'b1_title' => 'Training Programs',
	'b1_route' => 'training-programs.index',

	'b2_title' =>'Program Details',

	'show_buttons' => 1,
	'btn_back_route' => 'training-programs.index',
];
$uploadsPath = asset(upload_url('trainings/'.$Model_Data->id));
$pdfPath = asset(upload_url('trainings/'.$Model_Data->id.'/'.$Model_Data->pdf_file));
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="wrapper wrapper-content">
  	<form  name="settings_form" action="{{ route('training-programs.update',$Model_Data->id) }}" enctype="multipart/form-data" method="post">
    @method('put')
    @csrf
		<div class="row animated fadeInRight">
			<div class="row mt-10">
				<div class="col-sm-offset-1 col-lg-10 form-group row">
					<div class="col-lg-12 mt-10">
						<h3 class="font-bold">Basic :</h3>
						<hr>
					</div>
					<div class="col-lg-12 form-group row">
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="row" style="display: flex;">
								<label class="col-sm-3 control-label">Title *:</label>
								<input type="text"  maxlength="20" class=" col-sm-9 form-control validate"  value="{{ $Model_Data->title }}" id="program_title" placeholder="Program Title" value="" name="program_title"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="row" style="display: flex;">
								<label class="col-sm-3 control-label">Download Pdf : </label>
                            	<a href="{{$pdfPath}}" style = "color: blue;"> {{$Model_Data->pdf_file}}</a>
							</div>
						</div>
					</div>
                    <div class="col-lg-12 mt-10">
                        <hr>
                        <h3 class="font-bold">Variants :</h3>
                        <hr>
                    </div>
					<div class="col-lg-12 mt-10 form-group row" id="variation_data">
                        <?php $i= 0 ; ?>
						@foreach ($programDetails as $values )
						<?php $val_id = $values->id;
                        $i++;?>
						<input type="hidden" name="program_details_id[]" value="{{ $values->id }}" />
						<div class="col-sm-12 variations variationrow">
							<div class="form-group row">
								<div class="col-sm-6">
									<span class="addon_count"><strong>Variant</strong></span>
								</div>
								<div class="col-sm-4 text-right"> &nbsp; </div>
								<div class="col-sm-2 text-right formbtngroup">
									@if($i > 1)
                                    {!! Form::button('X', ['class' => 'btn btn-secondary btn-rem-var']) !!}
										@endif
									{!! Form::button('+', ['class' => 'btn btn-primary add_variations']) !!}
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-8 col-md-8 col-sm-12">
									<div class="form-group row">
										<label class="col-sm-4 control-label">Title *:</label>
										<div class="col-sm-8">Title:</label>
										 	<input type="text"  maxlength="20" class=" col-sm-9 form-control validate" value="{{$values->title}}" placeholder="Variant Title"  name="variant_title[]"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-4 control-label">Start Date Time *</label>
										<div class="col-sm-8">
											<input type="date" class="form-control" value="{{$values->start_date_time}}"  placeholder="start_date_time" name="start_date_time[]" size="75" min="{{ date('Y-m-d') }}" required>
											@if ($errors->has('duration'))
												<span class="text-danger">{{ $errors->first('duration') }}</span>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-4 control-label">Duration *</label>
										<div class="col-sm-8">
											<input type="number" class="form-control" value="{{$values->duration}}" placeholder="Duration" name="duration[]" size="75" required>
											@if ($errors->has('duration'))
												<span class="text-danger">{{ $errors->first('duration') }}</span>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-4 control-label">Description *</label>
										<div class="col-sm-8">
											<textarea class="form-control" placeholder="Your description" name="description[]"  rows="3" required > {{ $values->description}}</textarea>
											@if ($errors->has('description'))
												<span class="text-danger">{{ $errors->first('description') }}</span>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-4 control-label">Image *</label>
										<div class="col-sm-8">
											<input type="file" class="form-control" name="images_{{$val_id}}" size="75" >
											@if ($errors->has('images'))
												<span class="text-danger">{{ $errors->first('images') }}</span>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-4 control-label">Document *</label>
										<div class="col-sm-8">
											<input type="file" class="form-control" name="documents_{{$val_id}}" size="75">
											@if ($errors->has('documents'))
												<span class="text-danger">{{ $errors->first('documents') }}</span>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-4 control-label">Video *</label>
										<div class="col-sm-8">
											<input type="file" class="form-control" name="videos_{{$val_id}}" accept="video/*"  size="25">
											@if ($errors->has('videos'))
												<span class="text-danger">{{ $errors->first('videos') }}</span>
											@endif
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12">
									<?php
									$attachments = $values->documents;
									if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
										$file_path = $uploadsPath . "/" . $attachments;
										?>
										<div class="form-group row" id="training_program_document_div_<?php echo $i;?>">
											<label class="mt-1 col-sm-3 control-label">Uploaded Document:</label>
											<div class="col-lg-8 col-md-6 col-sm-12">
												<a class="btn btn-info" href="{{ $file_path }}" target="_blank">
												{{$values->original_documents_name}}
												</a><br>
                                                @if (strpos($file_path, 'uploads/defaults') === false)
                                                	<?php
													$del_link = "training-programs/".$values->id."/documents";
													$del_file_name = trim($values->documents);
													?>
                                                    <a class="btn btn-danger delete-confirm"
													   data-file_name="{{ $del_file_name }}"
													   data-url="{{ url('manage/file-delete/'.$del_link) }}"
													   data-parent="training_program_document_div_<?php echo $i;?>"
													   title="Delete File">
                                                        <i class="fa fa-trash fa-lg"></i> Delete
                                                    </a>
                                                @endif

											</div>
										</div>
										<?php
									}
									?>
									<?php
									$attachments = $values->images;
									if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
										$file_path = $uploadsPath . "/" . $attachments;
										?>
										<div class="form-group row" id="training_program_image_div_<?php echo $i;?>">
											<label class="col-sm-3 control-label">Uploaded Image:</label>
											<div class="col-lg-8 col-md-6 col-sm-12">
												<img src="{{ $file_path }}" style="width:100%">
                                                @if (strpos($file_path, 'uploads/defaults') === false)
                                                	<?php
													$del_link = "training-programs/".$values->id."/image";
													$del_file_name = trim($values->images);
													?>
                                                    <a class="btn btn-danger delete-confirm"
													   data-file_name="{{ $del_file_name }}"
													   data-url="{{ url('manage/file-delete/'.$del_link) }}"
													   data-parent="training_program_image_div_<?php echo $i;?>"
													   title="Delete File">
                                                        <i class="fa fa-trash fa-lg"></i> Delete
                                                    </a>
                                                @endif
											</div>
										</div>
										<?php
									}
									?>
									<?php
									$attachments = $values->videos;
									if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
										$file_path = $uploadsPath . "/" . $attachments;
										?>
										<div class="form-group row" id="training_program_video_div_<?php echo $i;?>">
											<label class="mt-1 col-sm-3 control-label">Uploaded Video:</label>
											<div class="col-lg-8 col-md-6 col-sm-12">
												<video controls>
													<source id="video_src" class="img-fluid view-img" src="{{ $file_path }}" type="video/mp4" width="15" height ="15">
													Your browser does not support the video tag.
												</video>
                                                @if (strpos($file_path, 'uploads/defaults') === false)
                                                	<?php
													$del_link = "training-programs/".$values->id."/video";
													$del_file_name = trim($values->videos);
													?>
													<a class="btn btn-danger delete-confirm"
													   data-file_name="{{ $del_file_name }}"
													   data-url="{{ url('manage/file-delete/'.$del_link) }}"
													   data-parent="training_program_video_div_<?php echo $i;?>"
													   title="Delete File">
														<i class="fa fa-trash fa-lg"></i> Delete
													</a>
												@endif
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</div>
						@endforeach
					</div>
					<hr />
					<div class="form-group row">
						<div class=" col-sm-offset-0 col-sm-10 col-xs-6">
							<label class="control-label">* indicates that fields are mandatory.</label>
						</div>
						<div class="col-sm-2 col-xs-6">
							<button type="submit" class="btn btn-primary sub-btn">Update</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

<div class="clone hide" style="display: none;">
    <div class="col-sm-12 variations variationrow">
        <input type="hidden" name="program_details_id[]" value="0" />

        <div class="form-group row">
            <div class="col-sm-6">
            	<span class="addon_count"><strong>Variant</strong></span>
            </div>
            <div class="col-sm-4 text-right"> &nbsp; </div>
            <div class="col-sm-2 text-right formbtngroup">
                {!! Form::button('X', ['class' => 'btn btn-secondary btn-rem-var']) !!}
                {!! Form::button('+', ['class' => 'btn btn-primary add_variations']) !!}
            </div>
		</div>

		<div class="form-group row">
			<div class="col-lg-8 col-md-8 col-sm-12">
				<div class="form-group row">
					<label class="col-sm-4 control-label">Title *:</label>
					<div class="col-sm-8">
						<input type="text"  maxlength="20" class="form-control validate" placeholder="Variant Title" value="" name="variant_title[]"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-4 control-label">Start Date Time *</label>
					<div class="col-sm-8">
						<input type="date" class="form-control" placeholder="start_date_time" name="start_date_time[]" size="75" min="{{ date('Y-m-d') }}">
						@if ($errors->has('duration'))
							<span class="text-danger">{{ $errors->first('duration') }}</span>
						@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-4 control-label">Duration *</label>
					<div class="col-sm-8">
						<input type="number"  class="form-control" name="duration[]" size="75">
						@if ($errors->has('duration'))
							<span class="text-danger">{{ $errors->first('duration') }}</span>
						@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-4 control-label">Description *</label>
					<div class="col-sm-8">
						<textarea class="form-control" placeholder="Your description" name="description[]" rows="3" ></textarea>
						@if ($errors->has('description'))
							<span class="text-danger">{{ $errors->first('description') }}</span>
						@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-4 control-label">Image *</label>
					<div class="col-sm-8">
						<input type="file" class="form-control" name="images[]" size="75">
						@if ($errors->has('images'))
							<span class="text-danger">{{ $errors->first('images') }}</span>
						@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-4 control-label">Document *</label>
					<div class="col-sm-8">
						<input type="file" class="form-control" name="documents[]" size="75">
						@if ($errors->has('documents'))
							<span class="text-danger">{{ $errors->first('documents') }}</span>
						@endif
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-4 control-label">Video *</label>
					<div class="col-sm-8">
						<input type="file" class="form-control" name="videos[]"  size="75">
						@if ($errors->has('videos'))
							<span class="text-danger">{{ $errors->first('videos') }}</span>
						@endif
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


@push('scripts')
<style>
.variations{
	border-bottom:1px solid #999;
	margin:10px;
}
</style>
<script >

$(document).ready(function() {
	call_addrem_addons();
});

function call_addrem_addons()
{
	$(".add_variations").off();
	$(".add_variations").click(function()
	{
		var html = $(".clone").html();
		$("#variation_data").append(html);
		call_addrem_addons();
	});
    $("body").on("click",".btn-rem-var",function(){
		$(this).parents(".formbtngroup").parents(".row").parents(".variationrow").remove();
		addon_count();
	});
	addon_count();
}


function addon_count()
{
	jQuery('.addon_count').each(function(index, element) {
        var value = index;
		value++;
		var addon_count = '<strong>Variant '+value+'</strong>';
		$(this).html(addon_count);
    });
}
</script>
@endpush
