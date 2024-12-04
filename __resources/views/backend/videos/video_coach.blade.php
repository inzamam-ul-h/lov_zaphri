
@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
	'show_breadcrumb' => 1,

	'show_title' => 1,
	'title' => 'Create New video',

	'show_links' => 1,
	'b1_title' => 'upload video',
	'b1_route' => 'videos.index',
	'b2_title'=> ' upload',
	// 'b2_route'=> '0',

	'show_buttons' => 1,
	'btn_back_route' => 'videos.index'
];
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="row">
                    <form name="video_form" method="post" enctype="multipart/form-data" action="{{ route('videos.store') }}">
                       @csrf
                        <div class="content-group">
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <div class="row">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <h3 class="font-bold">uplaod video</h3>
                                    <hr />


                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Title</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">

                                                <input type="text" value="" {{ old('title') }} maxlength="20" class="form-control validate" id="title" placeholder="Title" name="title" size="75" data-parsley-minlength="2" data-parsley-pattern="/^[a-z ,.'-]+$/i" data-parsley-required>
                                                @if ($errors->has('title'))
                                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Category</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">

                                                <select class="form-control" id="category" name="category">
                                                    <option value="" selected disabled>Choose Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('category'))
                                                    <span class="text-danger">{{ $errors->first('category') }}</span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>


                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Description</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">

                                                <textarea class="form-control" placeholder="Your description" name="description" id="description" rows="3" {{ old('description') }}></textarea>
                                                        @if ($errors->has('description'))
                                                            <span class="text-danger">{{ $errors->first('description') }}</span>
                                                        @endif
                                            </div>

                                        </div>

                                    </div>



                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Duration</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">


                                                <input type="text" value="" {{ old('duration') }} class="html-duration-picker form-control" id="duration" placeholder="Duration" name="duration" size="75">
                                                @if ($errors->has('duration'))
                                                    <span class="text-danger">{{ $errors->first('duration') }}</span>
                                                @endif
                                            </div>

                                        </div>

                                    </div>


                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Status</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">

                                                <select name="status" id="status" class="form-control" >
                                                    <option value="1" selected="selected">Active</option>
                                                    <option value="0" >Inactive</option>
                                                </select>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Upload Images</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">


                                                     <input type="file" class="form-control" name="image" accept="image/*">
                                                    @if ($errors->has('image'))
                                                        <span class="text-danger">{{ $errors->first('image') }}</span>
                                                    @endif
                                                    <span id="upload-file-info4">Upload Image</span>
                                                </label>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Upload Video</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">


                                                    <input type="file" class="form-control" name="video" accept="video/*">
                                                    @if ($errors->has('video'))
                                                        <span class="text-danger">{{ $errors->first('video') }}</span>
                                                    @endif
                                                    <span id="upload-file-info1">Upload Video</span>
                                                </label>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <hr />
                                    <h3 class="font-bold"><?php echo "Printable Information"; ?></h3>
                                    <hr />


                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Title</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">

                                                <input type="text" value=""{{ old('p_title') }} class="form-control" id="p_title" placeholder="Print Title" name="p_title" size="75">
                                                @if ($errors->has('p_title'))
                                                    <span class="text-danger">{{ $errors->first('p_title') }}</span>
                                                @endif
                                            </div>

                                        </div>

                                    </div>




                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Description</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">

                                                <textarea class="form-control" placeholder="Your print description" name="print_description" id="print_description" rows="3" {{ old('print_description') }}></textarea>
                                                @if ($errors->has('print_description'))
                                                    <span class="text-danger">{{ $errors->first('print_description') }}</span>
                                                @endif
                                            </div>

                                        </div>

                                    </div>





                                    <div class="form-group row">

                                        <label class="col-sm-2 col-xs-2 control-label">Upload Images</label>

                                        <div class="col-sm-10 col-xs-10">

                                            <div class="col-sm-12">


                                                    <input type="file" class="form-control" name="print_image" accept="print_image/*">
                                                    @if ($errors->has('print_image'))
                                                        <span class="text-danger">{{ $errors->first('print_image') }}</span>
                                                    @endif
                                                </label>

                                            </div>

                                        </div>

                                    </div>

                                    <hr/>
                                    <div class="form-group row">

                                        <div class="col-sm-offset-10 col-sm-2">

                                            <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_forms">

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

@endsection
@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')
