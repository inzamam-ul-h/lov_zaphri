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
	'title' => $Model_Data->name,

	'show_links' => 1,

	'b1_title' => $user,
	'b1_url' => $user_type_link,

	'b2_title'=> 'View Profile',

	'show_buttons' => 1,
	'btn_back_url' => $user_type_link
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

                        <div id="data_display" class="row">
                            <div class="col-sm-offset-1 col-sm-10">
                                <div class="col-lg-12" id="field-space">
                                    <div id="basic">
                                        <div class="form-group row">
                                            <div class="col-sm-8">
                                                <h3 class="font-bold">Profile</h3>
                                            </div>
                                        </div>
                                        <hr />
                                    </div>

                                        {{-- @dd($user->photo) --}}
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <img src="{{ user_profile_image_path($Model_Data->id) }}" height="150" width="150">
                                        <h2 class="m-t-xs m-b-none">
                                            <b>{{ $Model_Data->name }}</b>
                                        </h2>
                                        <br>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                                <input type="hidden" name="model_id" value="{{ $Model_Data->id }}">
                                            </div>
                                        </div>

                                    </div>
                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <strong class="c-white">Full Name :  </strong>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                {{ $Model_Data->name }}
                                            </div>
                                        </div>
                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <strong class="c-white">Email : </strong>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												{{ $Model_Data->email }}
                                            </div>
                                        </div>
                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <strong class="c-white">Phone No: </strong>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												{{ $Model_Data->phone }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        {{-- <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <strong class="c-white">User_Role.: </strong>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												<p>
													@if($Model_Data->user_type == 0)
														Admin
													@elseif($Model_Data->user_type == 1)
														Coach
													@elseif($Model_Data->user_type == 2)
														Player
													@elseif($Model_Data->user_type == 3)
														Coach
													@elseif($Model_Data->user_type == 4)
														parent
													@endif
												</p>
                                            </div>
                                        </div> --}}

                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <strong class="c-white">Reg No. : </strong>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												{{ $Model_Data->id }}
                                            </div>
                                        </div>
                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <strong class="c-white">Contact Person : </strong>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												{{ $Model_Data->phone }}
                                            </div>
                                        </div>
                                        <div class="row mt-10">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
												<strong class="c-white">Address: </strong>
											</div>
											<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                {{ $Model_Data->address }}
											</div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                                <div id="tableExample3_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <div class="col-lg-12">
                                            <div id="basic">
                                                <div class="form-group row">
                                                    <div class="col-sm-8">
                                                        <h3 class="font-bold">Professional Profile</h3>
                                                    </div>
                                                </div>
                                                <hr />
                                            </div>
                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                <div class="row mt-10">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <strong class="c-white">skill: </strong>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    </div>
                                                </div>
                                                <div class="row mt-10">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                        <strong class="c-white">Experice: </strong>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                    </div>
                                                </div>
                                            </div>

                                    <div class="form-group row">
                                        <div class="col-sm-10 col-xs-6">
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="row">
												{{-- @if((Auth::user()->can('users-status') || Auth::user()->can('all')) && $user->admin_approved == 0)
                                                <a href="{{ route('users.approve', $user->id) }}"  class="btn btn-success sub-btn " id="approve_btn">Approve</a>
												@endif --}}
                                                <a href="{{ route('users.edit', $Model_Data->id) }}"  class="btn btn-primary sub-btn " id="edit_btn">Edit</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-60">
                <div class="col-sm-12">
                    &nbsp;
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
