@extends('backend.layouts.portal')

@section('content')
<?php
$AUTH_USER = Auth::user();
//dd($AUTH_USER);
$user_type = $Model_Data->user_type;
$user_type_link = 'manage/users/';
if ($user_type == 1) {
    $user = 'Coaches';
    $user_type_link .= 'coaches';
}
else if ($user_type == 2) {
    $user = 'Players';
    $user_type_link .= 'players';
}
else if ($user_type == 3) {
    $user = 'Clubs';
    $user_type_link .= 'clubs';
}
else if ($user_type == 4) {
    $user = 'Parents';
    $user_type_link .= 'parents';
}
else {
    $user = 'Admins';
    $user_type_link .= 'admins';
}
$user_type_link .= '/' . $user_type;

$data = [
    'show_breadcrumb' => 1,
    'show_title' => 1,
    'title'      => 'View Profile: ' . $Model_Data->name,
    'show_links' => 1,
    'b2_title' => 'View Profile',
];
/* if(($Model_Data->id == Auth::user()->id || Auth::user()->can('users-edit') || Auth::user()->can('all'))
  || ($Model_Data->id != Auth::user()->id && Auth::user()->user_type != 3)){} */

if ($Model_Data->id == Auth::user()->id || Auth::user()->can('all') || (Auth::user()->can('users-edit') && Auth::user()->user_type != 3)) {
    $data['show_buttons'] = 1;
    $data['btn_edit_route'] = 'users.edit';
    $data['edit_record_id'] = $Model_Data->id;

}
if (Auth::user()->can('users-listing') || Auth::user()->can('all')) {
    $data['b1_url'] = $user_type_link;
    $data['b1_title'] = $user;
}
//dd($data);
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
                        <div class="content-group">
                            <div class="row">
                                <div class="col-sm-offset-1 col-sm-10">
                                    <div class="form-group row">
                                        <div class="col-sm-7">
                                            <h3 class="font-bold">Basic  Profile</h3>
                                        </div>
                                    </div>
                                    <hr />
                                    @if($user_type == 1)
                                    @include('backend.users.show.coach_show')
                                    @elseif($user_type == 2)
                                    @include('backend.users.show.player_show')
                                    @elseif($user_type == 3)
                                    @include('backend.users.show.club_show')
                                    @elseif($user_type == 4)
                                    @include('backend.users.show.parent_show')
                                    @else
                                    @include('backend.users.show.admin_show')
                                    @endif
                                </div>
                            </div>
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
