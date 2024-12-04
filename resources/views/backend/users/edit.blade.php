@extends('backend.layouts.portal')

@section('content')
<?php
$AUTH_USER = Auth::user();
$user_type = $Model_Data->user_type;
$user_type_link = 'manage/users/';
if ($user_type==1)
{
	$user = 'Coaches';
	$user_type_link .= 'coaches';
}
else if($user_type==2){
	$user = 'Players';
	$user_type_link .= 'players';
}
else if($user_type==3){
	$user = 'Clubs';
	$user_type_link .= 'clubs';
}
else if($user_type==4){
	$user = 'Parents';
	$user_type_link .= 'parents';
}
else {
	$user = 'Admins';
	$user_type_link .= 'admins';
}
$user_type_link .= '/'.$user_type;

$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Edit Profile: '.$Model_Data->name,

	'show_links' => 1,

	'b1_title' => $user,

	'b2_title'=> 'Edit Profile'
];
if(Auth::user()->can('users-listing') || Auth::user()->can('all')){
	$data['show_buttons'] = 1;
	$data['btn_back'] = 1;
	$data['btn_back_url'] = $user_type_link;

    $data['b1_title'] = $user;
    $data['b1_url'] = $user_type_link;
}
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


							<form method="post" enctype="multipart/form-data"

							action="{{ route('users.update',$Model_Data->id) }}">

								@csrf
								@method('put')

                            	<div id="#">
                                    <div class="content-group">
                                        <div class="row">
                                            <div class="col-sm-offset-1 col-sm-10">
                                                <div class="form-group row">
                                                    <div class="col-sm-7">

                                                        <h3 class="font-bold">Basic Profile</h3>
                                                    </div>
                                                    <div class="col-sm-5">
                                                    </div>
                                                </div>
                                                <hr />
												@if($user_type==1)
												   @include('backend.users.edit.coach_edit')
												@elseif($user_type==2)
													@include('backend.users.edit.player_edit')
												@elseif($user_type==3)
													@include('backend.users.edit.club_edit')
												@elseif ($user_type==4)
													@include('backend.users.edit.parent_edit')
												@else
													@include('backend.users.edit.admin_edit')
												@endif

												<hr />
												<div class="form-group row">
													<div class="col-sm-10 col-xs-6">
														<label class="control-label">* indicates that fields are mandatory.</label>
													</div>
													<div class="col-sm-2 col-xs-6">
														<button type="submit"  class="btn btn-primary sub-btn">Update</button>
													</div>
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

@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')
@endsection
