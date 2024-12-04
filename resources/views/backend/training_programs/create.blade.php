@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Create Training Program',

	'show_links' => 1,

	'b1_title' => 'Training Programs',
	'b1_route' => 'training-programs.index',

	'b2_title' =>'New Program',

	'show_buttons' => 1,
	'btn_back_route' => 'training-programs.index',
];
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="wrapper wrapper-content">
  	<form  name="settings_form" action="{{ route('training-programs.store') }}" enctype="multipart/form-data" method="post">
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
                            <input type="text"  maxlength="20" class=" col-sm-9 form-control validate" id="program_title" placeholder="Program Title" value="" name="program_title"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
                           </div>
                        </div>
                    </div>
					<div class="col-lg-12 mt-10">
						<hr>
						<h3 class="font-bold">Variants :</h3>
						<hr>
					</div>
					<div class="col-lg-12 mt-10 form-group row" id="variation_data">
						<div class="col-sm-12 variations">
							<div class="form-group row">
								<div class="col-sm-6">
									<span class="addon_count"><strong>Variant</strong></span>
								</div>
								<div class="col-sm-4 text-right"> &nbsp; </div>
								<div class="col-sm-2 text-right">
               						{!! Form::button('+', ['class' => 'btn btn-primary add_variations']) !!}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 control-label">Title *:</label>
								<div class="col-sm-10">
									<input type="text"  maxlength="20" class="form-control validate" placeholder="Variant Title" value="" name="variant_title[]"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
								</div>
							</div>
                            <div class="form-group row">
								<div class="col-lg-6 col-md-6 col-sm-12 row">
									<label class="col-sm-4 control-label">Start Date Time *</label>
									<div class="col-sm-8">
										<input type="date" class="form-control" placeholder="start_date_time" name="start_date_time[]" size="75" min="{{ date('Y-m-d') }}" required>
										@if ($errors->has('duration'))
											<span class="text-danger">{{ $errors->first('duration') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-12 row">
									<label class="col-sm-4 control-label">Duration *</label>
									<div class="col-sm-8">
										<input type="number"  class="form-control" name="duration[]" size="75" required>
										@if ($errors->has('duration'))
											<span class="text-danger">{{ $errors->first('duration') }}</span>
										@endif
									</div>
								</div>
							</div>
                            <div class="form-group row">
								<label class="col-sm-2 control-label">Description *</label>
								<div class="col-sm-10">
									<textarea class="form-control" placeholder="Your description" name="description[]" rows="3" required ></textarea>
									@if ($errors->has('description'))
										<span class="text-danger">{{ $errors->first('description') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4 col-md-4 col-sm-12 row">
									<label class="col-sm-4 control-label">Image *</label>
									<div class="col-sm-8">
										<input type="file" class="form-control" name="images[]" size="75" required>
										@if ($errors->has('images'))
											<span class="text-danger">{{ $errors->first('images') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12 row">
									<label class="col-sm-4 control-label">Document *</label>
									<div class="col-sm-8">
										<input type="file" class="form-control" name="documents[]" size="75" required>
										@if ($errors->has('documents'))
											<span class="text-danger">{{ $errors->first('documents') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12 row">
									<label class="col-sm-4 control-label">Video *
                                        <small> Max Video Size 20MB </small>
                                    </label>
									<div class="col-sm-8">
										<input type="file" class="form-control" name="videos[]"  size="75" required>
										@if ($errors->has('videos'))
											<span class="text-danger">{{ $errors->first('videos') }}</span>
										@endif
									</div>
								</div>
							 </div>
						</div>
					 </div>
					<hr />
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

<div class="clone hide" style="display: none;">
    <div class="col-sm-12 variations variationrow">

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
			<label class="col-sm-2 control-label">Title *:</label>
			<div class="col-sm-10">
				<input type="text"  maxlength="20" class="form-control validate" placeholder="Variant Title" value="" name="variant_title[]"  data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6 col-md-6 col-sm-12 row">
				<label class="col-sm-4 control-label">Start Date Time *</label>
				<div class="col-sm-8">
					<input type="date" class="form-control" placeholder="start_date_time" name="start_date_time[]" size="75" min="{{ date('Y-m-d') }}" required>
					@if ($errors->has('duration'))
						<span class="text-danger">{{ $errors->first('duration') }}</span>
					@endif
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 row">
				<label class="col-sm-4 control-label">Duration *</label>
				<div class="col-sm-8">
					<input type="number"  class="form-control" name="duration[]" size="75" required>
					@if ($errors->has('duration'))
						<span class="text-danger">{{ $errors->first('duration') }}</span>
					@endif
				</div>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-sm-2 control-label">Description *</label>
			<div class="col-sm-10">
				<textarea class="form-control" placeholder="Your description" name="description[]" rows="3" required></textarea>
				@if ($errors->has('description'))
					<span class="text-danger">{{ $errors->first('description') }}</span>
				@endif
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-4 col-md-4 col-sm-12 row">
				<label class="col-sm-4 control-label">Image *</label>
				<div class="col-sm-8">
					<input type="file" class="form-control" name="images[]" size="75" required>
					@if ($errors->has('images'))
						<span class="text-danger">{{ $errors->first('images') }}</span>
					@endif
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 row">
				<label class="col-sm-4 control-label">Document *</label>
				<div class="col-sm-8">
					<input type="file" class="form-control" name="documents[]" size="75" required>
					@if ($errors->has('documents'))
						<span class="text-danger">{{ $errors->first('documents') }}</span>
					@endif
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-12 row">
				<label class="col-sm-4 control-label">Video *</label>
				<div class="col-sm-8">
					<input type="file" class="form-control" name="videos[]"  size="75" required>
					@if ($errors->has('videos'))
						<span class="text-danger">{{ $errors->first('videos') }}</span>
					@endif
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
