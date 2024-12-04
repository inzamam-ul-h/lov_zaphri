
@extends('backend.layouts.portal')

@section('content')

<?php
$b2_title_str = '';
$AUTH_USER = Auth::user();
$user_type_link = 'manage/users/';
if ($user_type==1)
{
	$module = 'Coaches';
	$b2_title_str = 'Coach';
	$user_type_link .= 'coaches';
}
else if($user_type==2){
	$module = 'Players';
	$b2_title_str = 'Player';
	$user_type_link .= 'players';
}
else if($user_type==3){
	$module = 'Clubs';
	$b2_title_str = 'Club';
	$user_type_link .= 'clubs';
}
else if($user_type==4){
	$module = 'Parents';
	$b2_title_str = 'Parent';
	$user_type_link .= 'parents';
}
else {
	$module = 'Admins';
	$b2_title_str = 'Admin';
	$user_type_link .= 'admins';
}
$user_type_link .= '/'.$user_type;
$b2_title_str = 'Create New ' .$b2_title_str;

$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => $module.': '.$b2_title_str,

	'show_links' => 1,

	'b1_title' => $module,
	'b1_url' => $user_type_link,

	'b2_title'=> $b2_title_str,

	'show_buttons' => 1,
	'btn_back_url' => $user_type_link
];
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')
    <form name="settings_form" method="post" action="{{ route('users.store') }}" enctype="multipart/form-data">
		@csrf

        <input type="hidden" name="user_id" value="{{ $AUTH_USER->id }}" />
        <input type="hidden" name="user_type" value="{{ $user_type }}" />
        <div class="content-group">
            <div class="row">
                <div class="col-sm-offset-2 col-sm-8">
                    <h3 class="font-bold"><?php echo $b2_title_str ?> Account</h3>
                    <hr />
                    @if($user_type==1)
                    @include('backend.users.create.coach_create')
                 @elseif($user_type==2)
                 @include('backend.users.create.player_create')
                 @elseif($user_type==3)
                 @include('backend.users.create.club_create')
                 @elseif ($user_type==4)
                 @include('backend.users.create.parent_create')
                 @else
                 @include('backend.users.create.admin_create')
                 @endif




                    <div class="form-group row">

                        <div class="col-sm-offset-4 col-sm-4">

                            <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_forms">

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </form>

	@include('backend.layouts.portal.content_lower')
	@include('backend.layouts.portal.content_bottom')

@endsection
