
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Create New Team',

	'show_links' => 1,

	'b1_title' => 'Teams',
	'b1_route' => 'teams.index',

	'b2_title'=> 'New Team',

	'show_buttons' => 1,
	'btn_back_route' => 'teams.index'
];
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="wrapper wrapper-content">
  	<form  name="settings_form" action="{{ route('teams.store') }}" enctype="multipart/form-data" method="post">
        @csrf
		<input type="hidden" name="user_id" value="{{ $AUTH_USER->id }}" />
		<input type="hidden" name="user_type" value="{{ $AUTH_USER->user_type }}" />
        <div class="row animated fadeInRight">
            <div class="row mt-10">
                <div class="col-sm-offset-1 col-lg-10 form-group row">
                    <div class="col-lg-12">
                        <h3 class="font-bold">Basic :</h3>
                        <hr>
                    </div>
                    <div class="col-lg-12">
						<div class="form-group row">
							<div class="col-lg-6 col-md-6 col-sm-6 row">
								<label class="col-sm-4 control-label">Team Name *</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Team Name" size="75"  required="required">
									 @if ($errors->has('name'))
										<span class="text-danger">{{ $errors->first('name') }}</span>
									 @endif
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6 col-md-6 col-sm-6 row">
								<label class="col-sm-4 control-label">Age Group *</label>
								<div class="col-sm-8">
									<select class="form-control" id="age_group" name="age_group" required >
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
							<div class="col-lg-6 col-md-6 col-sm-6 row">
								<label class="col-sm-4 control-label">Logo *</label>
								<div class="col-sm-8">
									<input type="file" class="form-control" name="logo" id="logo" placeholder="" size="75" value="" required="required">
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 control-label">Description *</label>
							<div class="col-sm-10">
								<textarea class="form-control" placeholder="Your description" name="description" id="description" rows="3" required>{{ old('description') }}</textarea>
								@if ($errors->has('description'))
									<span class="text-danger">{{ $errors->first('description') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="col-lg-12 mt-10">
						<hr>
						<h3 class="font-bold">Coaches:</h3>
						<hr>
					</div>
					<div class="col-lg-12 mt-10 form-group row">
						<div class="col-lg-6 col-md-6 col-sm-6 row">
							<label class="col-sm-2 control-label">Coach </label>
							<div class="col-sm-10">
								<select class="form-control form-select" id="coach_id" name="coach_id">
									<option value="">Choose  Coach</option>
										@foreach ($coaches as $coach)
										<option value="{{ $coach->coach_id }}">
											{{ $coach->coach_name }}
										</option>
										@endforeach
								</select>
								@if ($errors->has('coach'))
									<span class="text-danger">{{ $errors->first('coach') }}</span>
								@endif
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 row">
							<label class="col-sm-2 control-label">Assistant Coach </label>
							<div class="col-sm-10">
								<select class="form-control form-select" id="ast_coach_id" name="ast_coach_id">
									<option value="">Choose  Assistant Coach</option>
										@foreach ($ast_coaches as $coach)
										<option value="{{ $coach->ast_coach_id }}">
											{{ $coach->ast_coach_name }}
										</option>
										@endforeach
								</select>
								@if ($errors->has('coach'))
									<span class="text-danger">{{ $errors->first('coach') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="col-lg-12 mt-10">
						<hr>
						<h3 class="font-bold">Players:</h3>
						<hr>
					</div>
					<div class="col-lg-12 mt-10 form-group row">
						<div class="col-lg-12 col-md-12 col-sm-12 row">
							<label class="col-sm-2 control-label">Players* </label>
							<div class="col-sm-10">
								<select class="form-control form-select"  id="player_id" name="player_id[]" multiple required>
									<option value="">Choose Players</option>
									@foreach ($players as $player)
										<option value="{{ $player->player_id }}">
											{{ $player->player_name }}
										</option>
									 @endforeach
								</select>
								@if ($errors->has('player'))
									<span class="text-danger">{{ $errors->first('player') }}</span>
								@endif
							</div>
						</div>
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
