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

	'b2_title'=> 'Edit Profile',

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

                            <div id="#">


                                <form method="post" enctype="multipart/form-data"

                                action="{{ route('users.update',$Model_Data->id) }}">

                                    @csrf
                                    @method('put')
                                    <div class="content-group">
                                        <div class="row">
                                            <div class="col-sm-offset-1 col-sm-10">
                                                <div class="form-group row">
                                                    <div class="col-sm-7">
                                                        <h3 class="font-bold">Edit Profile</h3>
                                                    </div>
                                                    <div class="col-sm-5">
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="form-group row">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="col-sm-4 control-label">Name *</label>

                                                        <div class="col-sm-8">
                                                            <input type="text" value="{{ old('name', $Model_Data->name) }}" maxlength="20"
                                                                class="form-control validate" id="Name"
                                                                placeholder="Name" name="name" size="75"
                                                                data-parsley-minlength="2"
                                                                data-parsley-pattern="/^[a-z ,.'-]+$/i"
                                                                data-parsley-required>
                                                            @if ($errors->has('name'))
                                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="col-sm-4 control-label">Email *</label>
                                                        <div class="col-sm-8">
                                                            <input type="email" value="{{ old('email', $Model_Data->email) }}"
                                                                class="form-control " id="email" placeholder="Email"
                                                                name="email" size="75" >
                                                            @if ($errors->has('email'))
                                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="col-sm-4 control-label">REG NO. *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" value="{{ old('reg_no', $Model_Data->id) }}"
                                                                class="form-control " id="reg_no"
                                                                placeholder="Registration Number" name="reg_no"
                                                                size="75">
                                                            @if ($errors->has('reg_no'))
                                                                <span class="text-danger">{{ $errors->first('reg_no') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="col-sm-4 control-label">Contact Person *</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" value="{{ old('phone', $Model_Data->phone) }}"
                                                                class="form-control " id="contact_person"
                                                                placeholder="Contact Person" name="phone" size="75">
                                                            @if ($errors->has('phone'))
                                                                <span class="text-danger">{{ $errors->first('phone') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label class="col-sm-4 control-label">Address</label>
                                                        <div class="col-sm-8">
                                                            <textarea class="form-control" placeholder="Your Address" name="address" id="address"
                                                                rows="3">{{ old('address', $Model_Data->address) }}</textarea>
                                                            @if ($errors->has('address'))
                                                                <span class="text-danger">{{ $errors->first('address') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-group row">
                                                    @if(Auth ::user()->user_type == )
													<div class="col-sm-6">
														<label class="col-sm-4 control-label">password</label>
														<div class="col-sm-8">
															<input type="password" class="form-control" value="" placeholder="Password" name="password"><input>
															@if ($errors->has('password'))
																<span class="text-danger">{{ $errors->first('password') }}</span>
															@endif
														</div>
													</div>
													<div class="col-sm-6">
														<label class="col-sm-4" for="exampleInputName">photo</label>
														<label class="col-sm-8 btn btn-info">
															<input type="file" name="photo">
															<span>change photo</span>
														</label>
													</div>
                                                </div>
                                                <div class="form-group row">
													<div class="col-md-6">
														<label class="col-sm-4" for="exampleInputName">photo</label>
														<div class="col-sm-8">
															<img src="{{ user_profile_image_path($Model_Data->id) }}" alt="here the image view">
														</div>
													</div>
                                                </div>
											</div>
										</div>

										<div class="form-group row">
											<div class="col-sm-10 col-xs-6">
											</div>
											<div class="col-sm-2 col-xs-6">

                                                    <button type="submit"  class="btn btn-primary sub-btn">Update</button>


											</div>
										</div>

									</div>
                        		</form>

                            </div>

                            <hr />

                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <div class="form-group row">
                                            <div class="col-lg-8 col-md-8 col-sm-12">
                                                <label class="control-label">* indicates that fields are mandatory.</label>
                                            </div>

                                        </div>
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
