
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
		{{-- @method('put') --}}
        <input type="hidden" name="user_id" value="{{ $AUTH_USER->id }}" />
        <input type="hidden" name="user_type" value="{{ $user_type }}" />
        <div class="content-group">
              {{-- <?php dd($user_type) ?> --}}
            <div class="row">
                <div class="col-sm-offset-2 col-sm-8">
                    <h3 class="font-bold">Create new <?= $user ?> Account</h3>
                    <hr />


                    {{-- <div class="form-group row">

                        <label class="col-sm-2 control-label">Type</label> --}}

                        {{-- <div class="col-sm-10">

                            <div class="col-sm-12">
                                <input type="hidden" value="{{ old('user_type', $user_type) }}"
                                class="form-control " id="user_type"
                                placeholder="User Type" name="user_type"
                                size="75" >

                            </div>

                        </div> --}}

                    {{-- </div> --}}


                    <div class="form-group row">

                        <label class="col-sm-2 control-label">First Name</label>

                        <div class="col-sm-10">

                            <div class="col-sm-12">

                                <input type="text" class="form-control" name="f_name" placeholder="First Name" size="75" value="{{ old('f_name') }}" required="required">

                            </div>

                        </div>

                    </div>

                    <div class="form-group row">

                        <label class="col-sm-2 control-label">Last Name</label>

                        <div class="col-sm-10">

                            <div class="col-sm-12">

                                <input type="text" class="form-control" name="l_name" placeholder="Last Name" size="75" value="{{ old('l_name') }}" required="required">

                            </div>

                        </div>

                    </div>

                    <div class="form-group row">

                        <label class="col-sm-2 control-label">Phone</label>

                        <div class="col-sm-10">

                            <div class="col-sm-12">

                                <input type="text" class="form-control" name="phone" placeholder="Phone Number" size="75" value="{{ old('phone') }}">

                            </div>

                        </div>

                    </div>


                   @if(auth()->user()->user_type == 0)

                    <div class="form-group row">

                        <label class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-10">

                            <div class="col-sm-12">

                                <input type="email" class="form-control" name="email" placeholder="Email" size="75" value= 9" required="required">

                            </div>

                        </div>

                    </div>
					<div class="form-group row">

						<label class="col-sm-2 control-label">profile photo</label>

						<div class="col-sm-10">

							<div class="col-sm-12">

								<input type="file" class="form-control" name="photo" placeholder="" size="75" value="" required="required">

							</div>

						</div>

					</div>

                    <div class="form-group row">

                        <label class="col-sm-2 control-label">Password</label>

                        <div class="col-sm-10">

                            <div class="col-sm-12">

                                <input type="password" class="form-control" name="password" placeholder="" size="75" value="" required="required">

                            </div>

                        </div>

                    </div>
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
