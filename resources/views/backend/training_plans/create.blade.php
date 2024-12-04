
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => ' Create Training Plan',

	'show_links' => 1,

	'b1_title' => 'Training Plans',
	'b1_route' => 'training-plans.index',

	'b2_title' =>'New Training Plan',

	'show_buttons' => 1,
	'btn_back_route' => 'training-plans.index',
];
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="wrapper wrapper-content">
  	<form  name="settings_form" action="{{ route('training-plans.store') }}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="row animated fadeInRight">
            <div class="row mt-10">
                <div class="col-sm-offset-1 col-lg-10 form-group row">
                    <div class="col-lg-12">
                        <h3 class="font-bold">Basic :</h3>
                        <hr>
                    </div>
                    <div class="col-lg-12 form-group row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                           <div style="display: flex;">
                            <label class="col-sm-3 control-label">Title *:</label>
                            <input type="text"  maxlength="20" class=" col-sm-9 form-control validate" id="plan_name" placeholder="Plan Name" value="{{ old('plan_name') }}" name="plan_name"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
                           </div>
                        </div>
                    </div>
					<div class="col-lg-12 mt-10">
						<hr>
						<h3 class="font-bold">Videos *:</h3>
						<hr>
					</div>
					<div class="col-lg-12 mt-10 form-group row">
                        @foreach($videos as $video)
							<?php
							$checked = FALSE;
							$video_data = [
								'parent_classes' => 'col-lg-3 col-md-4 col-sm-12',
								'video' => $video,
								'checkbox' => 1,
								'checked' => $checked
							];
							?>
							@include('backend.common.quick_video_view', $video_data)
                        @endforeach
                    </div>
					<hr>
					<div class="form-group row">
						<div class=" col-sm-offset-0 col-sm-10 col-xs-6">
							<label class="control-label">* indicates that fields are mandatory.</label>
						</div>
						<div class="col-sm-2 col-xs-6">
							<button type="submit"  class="btn btn-primary sub-btn">Save</button>
						</div>
					</div>
                </div>
            </div>
        </div>
    </form>
</div>
@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')
@endsection

