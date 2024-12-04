@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => ' View Training Program: '.$Model_Data->title,

    'show_links' => 1,

	'b1_title' => 'Training Programs',
	'b1_route' => 'training-programs.index',

	'b2_title' =>'Program Details',

	'show_buttons' => 1,
	'btn_back_route' => 'training-programs.index',

];
if(Auth::user()->can('training-programs-edit') || Auth::user()->can('all')){
	$data['btn_edit_route'] = 'training-programs.edit';
	$data['edit_record_id'] = $Model_Data->id;
}

$uploadsPath = asset(upload_url('trainings/'.$Model_Data->id));
$pdfPath = asset(upload_url('trainings/'.$Model_Data->id.'/'.$Model_Data->pdf_file));
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

	<div class="wrapper wrapper-content">
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
								<label class="col-sm-3 control-label">Title : </label>
								{{$Model_Data->title}}
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
						<?php $i = 0; ?>
						@foreach ($programDetails as $values )
			  				<?php  $i++ ?>
							<div class="col-sm-12 variations">
								<div class="form-group row">
									<div class="col-lg-4 col-md-4 col-sm-12">
										<div class="form-group row">
											<label class="col-sm-4 control-label">Variant <?= $i?></label>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 control-label">Title:</label>
											<div class="col-sm-8">
												 {{$values->title}}
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 control-label">Duration *</label>
											<div class="col-sm-8">
												{{$values->duration}}
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 control-label">Start Date Time *</label>
											<div class="col-sm-8">
												{{$values->start_date_time}}
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 control-label">Description *</label>
											<div class="col-sm-8">
												{{$values->description}}
											</div>
										</div>
										<?php
										$attachments = $values->documents;
										if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
											$attachments = $uploadsPath . "/" . $attachments;
											?>
											<div class="form-group row">
												<label class="col-sm-4 control-label">Uploaded Document:</label>
												<div class="col-sm-8">
													<a class="btn btn-info" href="{{ $attachments }}" target="_blank">
													{{$values->original_documents_name}}
													</a>
												</div>
											</div>
											<?php
										}
										?>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-12">
										<?php
										$attachments = $values->images;
										if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
											$attachments = $uploadsPath . "/" . $attachments;
											?>
											<div class="form-group row">
												<div class="col-lg-12 col-md-12 col-sm-12">
													<label class="control-label">Uploaded Image:</label><br>
													<img src="{{ $attachments }}" style="width:100%">
												</div>
											</div>
											<?php
										}
										?>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-12">
										<?php
										$attachments = $values->videos;
										if ($attachments != "" && $attachments != 'default_image' && !empty($attachments)) {
											$attachments = $uploadsPath . "/" . $attachments;
											?>
											<label class="control-label">Uploaded Video:</label><br>
											<video controls>
												<source id="video_src" class="img-fluid view-img" src="{{ $attachments }}" type="video/mp4" width="15" height ="15">
												Your browser does not support the video tag.
											</video>
											<?php
										}
										?>
									</div>
								</div>
							</div>
				 		@endforeach
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
@endpush

