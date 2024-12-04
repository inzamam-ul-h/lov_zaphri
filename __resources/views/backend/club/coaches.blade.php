@extends('backend.layouts.portal')

@section('content')
<?php
$AUTH_USER = Auth::user();

$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Club Coaches',

	'show_links' => 1,

	'b1_title' => 'Club Members',
	'b1_route' => 'dashboard',

	'b2_title' =>'Associated Coaches',

	'show_buttons' => 1
];
if(Auth::user()->user_type == 3 || (Auth::user()->can('users-add') || Auth::user()->can('all'))){
	$data['btn_add_route_type'] = 'users.create_user_by_type';
	$data['add_user_type'] = 1;
}

?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')

    @include('backend.layouts.portal.content_middle')

    <div class="row mt-10">
        <div class="col-lg-12 row mt-10">
            <div class="col-lg-12 mt-10">
                <h3 class="font-bold">Associated Coaches Profiles :</h3>
                <hr>

                <div class="form-group row">
                    @foreach($Model_Data as $user)
						<?php
						$user_data = [
							'parent_classes' => 'col-lg-6 col-md-6 col-sm-12',
							'user_profile' => $user,
							'profile_heading' => 'Coach',
                            'can_remove' => 0
						];
                        if(($AUTH_USER->user_type ==3 && $AUTH_USER->can('club-members-edit')) || $AUTH_USER->can('all')){
                                $user_data['can_remove'] = 1;
                                $user_data['remove_title'] = 'Remove associated coach?';
                                $user_data['remove_url'] = 'manage/clubs/requests/'.$user->id.'/remove';
                            }
						?>

						@include('backend.common.quick_horizontal_view', $user_data)
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    @include('backend.layouts.portal.content_lower')

@include('backend.layouts.portal.content_bottom')

@endsection
