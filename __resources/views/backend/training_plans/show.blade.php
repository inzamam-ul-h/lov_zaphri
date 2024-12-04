
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'View Training Plan: '.$Model_Data->plan_name,

    'show_links' => 1,

	'b1_title' => 'Training Plans',
	'b1_route' => 'training-plans.index',

	'b2_title' =>'Plan Details',

	'show_buttons' => 1,
	
	'btn_back_route' => 'training-plans.index',
];
if(Auth::user()->can('training-plans-edit') || Auth::user()->can('all')){
	$data['btn_edit_route'] = 'training-plans.edit';
	$data['edit_record_id'] = $Model_Data->id;
}
$pdfPath = asset(upload_url('plans/'.$Model_Data->id.'/'.$Model_Data->pdf_file));
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
                            	{{$Model_Data->plan_name}}
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
                        <h3 class="font-bold">Videos :</h3>
                        <hr>
                    </div>
					<div class="col-lg-12 form-group row" id="variation_data">
						@foreach ($planDetails as $videos )
							<?php
							$video_data = [			
								'parent_classes' => 'col-lg-3 col-md-4 col-sm-12',						
								'video' => $videos	
							];
							?>
							@include('backend.common.quick_video_view', $video_data)
						@endforeach
					</div>
                </div>
            </div>
        </div>
	</div>

@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')

@endsection

