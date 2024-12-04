@extends('backend.layouts.portal')

@section('content')
    <?php
    $AUTH_USER = Auth::user();

    $data = [
        'show_breadcrumb' => 1,

        'show_title' => 1,
        'title' => 'Create New Role',

        'show_links' => 1,

        'b1_title' => 'Roles',
        'b1_route' => 'roles.index',

        'b2_title' =>'Create New',

        'show_buttons' => 1,
        'btn_back_route' => 'roles.index',
    ];

    ?>

    @include('backend.layouts.portal.breadcrumb', $data)

    @include('backend.layouts.portal.content_top')

		@include('backend.layouts.portal.content_middle')
		<div class="row mt-10">
			<div class="col-lg-12 row mt-10">
				<div class="col-lg-12 mt-10">
					<h3 class="font-bold">Create New Role</h3>
					<hr>
                      {!! Form::open(['route' => 'roles.store','files' => true]) !!}

                         @include('backend.roles.fields')

                      {!! Form::close() !!} 
				</div>
			</div>
		</div>
		@include('backend.layouts.portal.content_lower')

	@include('backend.layouts.portal.content_bottom')

@endsection