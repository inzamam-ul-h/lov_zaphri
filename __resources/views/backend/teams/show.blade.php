@extends('backend.layouts.portal')

@section('content')
<?php
$AUTH_USER = Auth::user();

$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'View Team Details: '.$Model_Data->name,

	'show_links' => 1,

	'b1_title' => 'Teams',
	'b1_route' => 'teams.index',

	'b2_title' =>'Team Details',

	'show_buttons' => 1,
	'btn_back_route' => 'teams.index',

];
if(Auth::user()->can('teams-edit') || Auth::user()->can('all')){
	$data['btn_edit_route'] = 'teams.edit';
	$data['edit_record_id'] = $Model_Data->id;
}
?>
@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="wrapper wrapper-content">
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
									<label class="col-sm-3 control-label">Team Name:</label>
									<div class="col-sm-9">
										{{ $Model_Data->name }}
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 form-group row">
									<label class="col-sm-3 control-label">Age Group *</label>
									<div class="col-sm-9">
										{{ $Model_Data->age_group }}
									</div>
								</div>
								<div class="col-lg-12 col-md-12 col-sm-12 form-group row">
									<label class="col-sm-3 control-label">Description</label>
									<div class="col-sm-9">
										{{$Model_Data->description}}
									</div>
								</div>
							</div>
                    		<div class="col-lg-3 col-md-4 col-sm-12 form-group row">
								<label class="control-label">Logo</label><br>
								<img src="{{ asset(upload_url( 'teams/'.$Model_Data->id.'/'.$Model_Data->logo) )}}" style="width:100%">
							</div>
						</div>
                    </div>
                    <div class="col-lg-12 form-group row">
						<div class="col-lg-2 col-md-2 col-sm-6">
							<label>Status :</label><br>
							@if($Model_Data->status == '0')
								Inactive
							@elseif($Model_Data->status == '1')
								Active
							@endif
						</div>
						<div class="col-lg-2 col-md-2 col-sm-6">
							<label>Created by :</label><br>
							<span class="no-margins"><?php echo get_user_name($Model_Data->created_by);?></span>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-6">
							<label>Created at :</label><br>
							<span class="no-margins"><?php echo $Model_Data->created_at;?></span>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-6">
							<label>Updated by :</label><br>
							<span class="no-margins"><?php echo get_user_name($Model_Data->updated_by);?></span>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-6">
							<label>Updated at :</label><br>
							<span class="no-margins"><?php echo $Model_Data->updated_at;?></span>
						</div>
					</div>
					
					@if(!empty($ast_coach) || !empty($coach))
					<div class="col-lg-12 mt-10">
						<hr>
						<h3 class="font-bold">Coaches:</h3>
						<hr>
					</div>
					<div class="col-lg-12 mt-10 form-group row">
						<?php

						$user_data = [
							'parent_classes' => 'col-lg-6 col-md-6 col-sm-12',
							'user_profile' => $coach,
							'profile_heading' => 'Coach',
                            'can_remove' => 0
						];
                        if(($AUTH_USER->user_type ==3 && $AUTH_USER->can('team-members-edit')) || $AUTH_USER->can('all')){
                            $user_data['can_remove'] = 1;
							$user_data['remove_title'] = 'Remove team coach?';
							$user_data['remove_url'] = 'manage/teams/remove/coach/'.$Model_Data->id.'/'.$coach->id;
                        }
						?>
						@include('backend.common.quick_horizontal_view', $user_data)

						@if(!empty($ast_coach))
							<?php
							$user_data = [
								'parent_classes' => 'col-lg-6 col-md-6 col-sm-12',
								'user_profile' => $ast_coach,
								'profile_heading' => 'Assistant Coach',
                                'can_remove' => 0
							];
                            if(($AUTH_USER->user_type ==3 && $AUTH_USER->can('team-members-edit')) || $AUTH_USER->can('all')){
                                $user_data['can_remove'] = 1;
								$user_data['remove_title'] = 'Remove team assistant coach?';
								$user_data['remove_url'] = 'manage/teams/remove/coach/'.$Model_Data->id.'/'.$ast_coach->id;
                            }
							?>
							@include('backend.common.quick_horizontal_view', $user_data)
						@endif
					</div>
					@endif
					<div class="col-lg-12 mt-10">
						<hr>
						<h3 class="font-bold">Players:</h3>
						<hr>
					</div>
					<div class="col-lg-12 mt-10 form-group row">
						<div class="col-lg-12 col-md-12 col-sm-12 row">
							@foreach ( $players as $user )
								<?php
								$user_data = [
									'parent_classes' => 'col-lg-3 col-md-4 col-sm-12',
									'user_profile' => $user,
									'profile_heading' => 'Player',
                                    'can_remove' => 0
								];
                                if(( $AUTH_USER->can('team-members-edit')) || $AUTH_USER->can('all')){
                                    $user_data['can_remove'] = 1;
									$user_data['remove_title'] = 'Remove team player?';
									$user_data['remove_url'] = 'manage/teams/remove/player/'.$Model_Data->id.'/'.$user->id;
                                }
								?>
								@include('backend.common.quick_profile_view', $user_data)
							@endforeach
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

@push('scripts')

<style>
     .sub-btn{
        display: inline ;
    }
    .c-white  {
	}
    td.avldays_cell {
        width: 14.5%;
        height: 40px;
        border: 1px solid;
        text-align: center;
        text-transform: capitalize;
        background-color: beige;
        cursor: pointer;
    }
    td.avldays_active {
        background-color: mediumspringgreen;
    }
    .mt-60 {
        margin-top: 60px;
    }
    .publicpart1 {
        width: 35%;
        float: left;
        display: inline-block;
    }
    .publicpart2 {
        width: 65%;
        float: right;
        display: inline-block;
    }
    #public_url_message {
        padding: 10px;
    }
</style>
@endpush
