
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Create New Event',

	'show_links' => 1,

	'b1_title' => 'Events',
	'b1_route' => 'events.index',

	'b2_title'=> 'New Event',

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
                            <form name="settings_form" method="post" action="{{ route('events.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="content-group">
                                    <div class="row">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <h3 class="font-bold">Basic Details</h3>
                                                </div>
                                            </div>
                                            <hr />
                                            <div class="form-group row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <label class="col-sm-3 control-label">Title *</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" value="" {{ old('title') }} maxlength="20" class="form-control validate" id="title" placeholder="Title" name="title" size="75" data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
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
														<select class="form-control"  id="age_group" name="age_group" required >
															<option value="" selected disabled>Choose Age Group</option>
															<ol>
																@foreach ($age_group as $age_groups)
																<option value="{{ $age_groups->id }}">
																	{{ $age_groups->title }}
																</option>
																@endforeach
															</ol>
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
                                                        <input type="date" class="form-control"  id="start_date_time" placeholder="start_date_time" {{ old('start_date_time')}} name="start_date_time" size="75" min="{{ date('Y-m-d') }}" required>
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
														<input type="text" class="form-control"  {{ old('meeting_link') }} id="meeting_link"placeholder="Meeting Link" name="meeting_link" size="75" required>
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
                                                        <textarea class="form-control" placeholder="Your description" name="description" id="description" rows="3" {{ old('description') }} required></textarea>
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
                                                        <input type="file" class="form-control" name="banner" accept="image/*" required >
                                                        @if ($errors->has('banner'))
                                                            <span class="text-danger">{{ $errors->first('banner') }}</span>
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
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">Images *
                                                        <small> (Select Multiple)</small>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="file" class="form-control" name="images[]" accept="image/*" multiple  required>

                                                        @if ($errors->has('image'))
                                                            <span class="text-danger">{{ $errors->first('image') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="col-sm-4 control-label">Documents *
                                                        <small> (Select Multiple)</small>
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="file"  class="form-control" id="documents" name="documents[]" size="75" multiple  required >

                                                        @if ($errors->has('document'))
                                                            <span class="text-danger">{{ $errors->first('document') }}</span>
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
