@extends('backend.layouts.portal')

@section('content')
    <?php
    $AUTH_USER = Auth::user();
    $data = [
        'show_breadcrumb' => 1,

        'show_title' => 1,
        'title' => 'Change Password',

        'show_links' => 1,
        'b1_title' => 'Users',
        'b1_route' => 'users.index',

        'b2_title' => 'Change Password',

        'show_buttons' => 1,
        'btn_back_route' => 'users.index',
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
                
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12">

									<div class="card">
										<div class="card-body">
											<form class="bg-light rounded px-4 py-2 mb-4 col-md-6" action="{{ route('users.updatePassword') }}" method="POST">
												@csrf
												<div class="my-2">
													<label for="currentPass">Current Password</label>
													<input type="text" class="form-control" name="current_password" id="currentPass">
												</div>

												<div class="my-2">
													<label for="newPass">New Password</label>
													<input type="text" class="form-control" name="new_password" id="newPass">
												</div>

												<div class="my-2">
													<label for="newConfPass">Confirm New Password</label>
													<input type="text" class="form-control" name="new_confirm_password" id="newConfPass">
												</div>

												<div class="mt-2 my-3">
													<input type="submit" value="Change Password" class="btn btn-primary">
													<?php echo cancel_button('users.index');?>
												</div>
											</form>
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
