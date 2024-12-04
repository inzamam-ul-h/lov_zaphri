@extends('backend.layouts.portal')
@section('content')
<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Edit Team Details: '.$Model_Data->name,

	'show_links' => 1,

	'b1_title' => 'Teams',
	'b1_route' => 'teams.index',

	'b2_title' => 'Team Details',

	'show_buttons' => 2,
	'btn_back_route' => 'teams.index'
];
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="wrapper wrapper-content">
  	<form  name="settings_form" action="{{ route('teams.update',$Model_Data->id) }}" enctype="multipart/form-data" method="post">
    @csrf
    @method('put')
	<input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        <div class="row animated fadeInRight">
            <div class="row mt-10">
                <div class="col-sm-offset-1 col-lg-10 form-group row">
                    <div class="col-lg-12">
                        <h3 class="font-bold">Basic :</h3>
                        <hr>
                    </div>
                    <div class="col-lg-12">
						<div class="form-group row">
                    		<div class="col-lg-9 col-md-8 col-sm-12 form-group row">
								<div class="col-lg-12 col-md-12 col-sm-12 form-group row">
									<label class="col-sm-3 control-label">Team Name *</label>
									<div class="col-sm-9">
										<input type="text"  maxlength="20" class="form-control validate" id="name" placeholder="Team Name" value="{{ $Model_Data->name }}" name="name" size="75" data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required required>
										@if ($errors->has('name'))
											<span class="text-danger">{{ $errors->first('name') }}</span>
										@endif
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 form-group row">
									<label class="col-sm-3 control-label">Age Group *</label>
									<div class="col-sm-9">
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
								<div class="col-lg-12 col-md-12 col-sm-12 form-group row">
									<label class="col-sm-3 control-label">Logo *</label>
									<div class="col-sm-9">
                                        <input type="file" class="form-control" value="{{ $Model_Data->logo }}" name="logo" accept="image/*">
										@if ($errors->has('image'))
											<span class="text-danger">{{ $errors->first('image') }}</span>
										@endif
									</div>
								</div>
							</div>
                    		<div class="col-lg-3 col-md-4 col-sm-12 form-group row" id="team_logo_div">
								<label class="control-label">Logo</label><br>
                                <?php $file_path = asset(upload_url( 'teams/'.$Model_Data->id.'/'.$Model_Data->logo) ); ?>
                                <img src="{{$file_path}}" style="width:100%">
                                @if (strpos($file_path, 'uploads/defaults') === false)
									<?php
									$del_link = "teams/".$Model_Data->id."/image";
									$del_file_name = trim($Model_Data->logo);
									?>
									<a class="btn btn-danger delete-confirm"
									   data-file_name="{{ $del_file_name }}"
									   data-url ="{{ url('manage/file-delete/'.$del_link) }}"
									   data-parent="team_logo_div"
									   title="Delete File">
										<i class="fa fa-trash fa-lg"></i> Delete
									</a>
								@endif
                            </div>
						</div>
                    </div>
                    <div class="col-lg-12">
						<div class="form-group row">
							<label class="col-sm-2 control-label">Description *</label>
							<div class="col-sm-10">
								<textarea class="form-control" placeholder="Your description" value="{{$Model_Data->description}}" name="description" id="description" rows="3" required>{{$Model_Data->description}}</textarea>
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
								<select class="form-control form-select" value="{{ isset($Model_Data->coach_name) ? $Model_Data->coach_name : '' }}" id="coach_id" name="coach_id">
									<option value=""{{ !isset($Model_Data->coach_id) ? ' selected' : '' }}>Choose Coach</option>
									@foreach ($coaches as $coach)
									<option value="{{ $coach->coach_id }}"{{ $Model_Data->coach_id == $coach->coach_id ? ' selected' : '' }}>
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
									<option value="" {{ !isset($Model_Data->ast_coach_id) ? ' selected' : '' }}>
										Choose Assistant Coach
									</option>
									@foreach ($ast_coaches as $coach)
									<option value="{{ $coach->ast_coach_id }}" {{ $Model_Data->ast_coach_id == $coach->ast_coach_id ? ' selected' : '' }}>
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
								<select class="form-control form-select" id="player_id" name="player_id[]" multiple required>
									<option value="">Choose Players *</option>
									@foreach ($players as $player)
										<option value="{{ $player->player_id }}"
											@if (isset($player_ids) && in_array($player->player_id,  $player_ids))
												selected
											@endif
										>
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
							<button type="submit"  class="btn btn-primary sub-btn">Update</button>
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

