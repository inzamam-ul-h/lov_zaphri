
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Create New video',

	'show_links' => 1,

	'b1_title' => 'Videos',
	'b1_route' => 'videos.index',

	'b2_title'=> 'New Video',

	'show_buttons' => 1,
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
                            <form name="settings_form" method="post" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="content-group">
                                    <div class="row">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            <h3 class="font-bold">Video Details</h3>
                                            <hr />
                                            <div class="form-group row">
                                                <div class="col-lg-6 col-md-6 col-sm-12">

                                                    <label class="col-sm-4 control-label">Title *</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" value="{{ old('title') }}"  maxlength="20" class="form-control validate" id="title" placeholder="Title" name="title" size="75" data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
                                                        @if ($errors->has('title'))
                                                            <span class="text-danger">{{ $errors->first('title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <label class="col-sm-4 control-label">Category *</label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control" id="category" name="category" required>
                                                            <option value="" selected disabled>Choose Category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                                                        <input class="html-duration-picker form-control" value="{{ old('duration') }}" id="duration" size="100" data-hide-seconds name="duration" required>
														@if ($errors->has('duration'))
															<span class="text-danger">{{ $errors->first('duration') }}</span>
														@endif
													</div>
												</div>
												<div class="col-sm-6">
													<label class="col-sm-4 control-label">Description *</label>
													<div class="col-sm-8">
														<textarea class="form-control" placeholder="Your description" value="" name="description" id="description" rows="3" required >{{ old('description') }}</textarea>
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
                                                    <label class="col-sm-4 control-label">Printable Images *
                                                        <small>(select multitple) </small>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="image[]" accept="image/*" multiple required>

                                                        @if ($errors->has('image'))
                                                        <span class="text-danger">{{ $errors->first('image') }}</span>
                                                    @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">Video *
                                                        <small> (Max Video Size 20MB) </small>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="video" accept="video/*" required>
                                                        @if ($errors->has('video'))
                                                            <span class="text-danger">{{ $errors->first('video') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

											<hr />
											<div class="form-group row">
												<div class="col-sm-10 col-xs-6">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')

@endsection
