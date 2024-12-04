@extends('backend.layouts.portal')

@section('content')

<?php
$AUTH_USER = Auth::user();
$data = [
    'show_breadcrumb' => 1,
	'show_buttons' => 1,




	'show_title' => 1,
	'title' => 'videos',

	'show_links' => 1,
	'b1_title' => 'videos',



    'show_buttons' => 1,
    'btn_add_route' => 'videos.create', // edit later
];
?>

@include('backend.layouts.portal.breadcrumb', $data)

@include('backend.layouts.portal.content_top')
@include('backend.layouts.portal.content_middle')

<div class="col-lg-12 mb-3">
    <h2>My Videos</h2>
</div>

<div class="row examplerow">


    <div class="col-lg-12">

         @if( $records_exists == 0 )
        {
            <h3 class="text-center">No data to display</h3>
        }



@else

@foreach ($videos as $key => $Model_Data)

                @if($key % 3 == 0)

                <div class="col-xl-12 col-lg-12 col-md-12">

              @endif



                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 10px">
                    <div class="file">
                        {{-- here give the video path --}}

                        <a href="{{ route('videos.show', $Model_Data->id) }}">
                            <span class="corner"></span>

                            <div class="image"  >
                                <img alt="image" class="img-fluid video_thumbnail" src="{{ asset(upload_url( 'videos/'.$Model_Data->id.'/'.$Model_Data->image) )}}">
                                <div class="category" >

                                            <span class="label label-primary">
                                                <?php
                                                // catgory
                                                ?>
                                            </span>

                                </div>
                            </div>

                            <div class="file-name">

                                <span style="color: #000000">
                                           {{-- show title --}}
                                        </span>
                                <br>

                                <small>
                                    CrAt:
                                 {{ $Model_Data->created_at }}
                                </small>

                                <a href="javascript:onclick=delete_video(<?php //echo $id;?>)" class="btn btn-xs btn-white" style="float: right;">
                                    <i class="fa fa-trash"></i>
                                    Delete
                                </a>
                                {{-- <a onclick="edit_video('<?php// echo $id ?>','<?php// echo $title ?>','<?php// echo $category ?>','<?php //echo $description ?>','<?php //echo// $date_of_creation ?>','<?php// echo $duration ?>','<?php// echo $status ?>')"  data-toggle="modal" data-target="#upModal_edit" title="Edit" class="btn btn-xs btn-white" style="float: right; margin-right: 5px;">
                                    <i class="fa fa-edit"></i> --}}
                                    <a href="{{ route('videos.edit',$Model_Data->id) }}" >
                                        <i class="fa fa-edit"></i>
                                    Edit
                                </a>
                                {{-- <a href="<?php// echo $SITE_URL;?>/manage/videos/view/<?php //echo $id;?>" class="btn btn-xs btn-white" style="float: right; margin-right: 5px;">
                                    <i class="fa fa-eye"></i> --}}
                                    <a href="{{ route('videos.show',$Model_Data->id) }}" class="btn btn-xs btn-white" style="float: right; margin-right: 5px;">
                                        <i class="fa fa-eye"></i>
                                    View
                                </a>


                            </div>


                        </a>

                    </div>
                </div>
                @endforeach
                @endif

                <?php
                // if($counter%3==0){
                    ?>
                    </div>
                    <?php
                // }
                ?>


                <?php
            // }

            ?>




            <div class="modal inmodal" id="upModal_edit" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated fadeIn">

                        <form name="video_form" method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Cancel</span>
                                </button>
                                <h4 class="modal-title">Edit Video</h4>
                                <p id="log_message"></p>
                            </div>
                            <div class="modal-body" id="modal-body_2">

                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">


                                        <input type="hidden" id="video_id" name="video_id">

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Title</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <input type="text" class="form-control" name="title" id="title" placeholder="Title" size="75"  required="required">

                                                </div>

                                            </div>

                                        </div>


                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Category</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <select name="category" id="category" class="form-control" >

                                                        <?php

                                                        // $results = $database->query("SELECT id, name from opt_categories WHERE status=1");

                                                        // while($sub = $database->fetch_assoc($results))

                                                        // {
                                                        //     $cat_id=$sub['id'];
                                                        //     $name=$sub['name'];
                                                            ?>

                                                            <option value="<?php //echo $cat_id;?>" > <?php //echo $name;?></option>

                                                            <?php

                                                        // }
                                                        ?>
                                                    </select>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Description</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <textarea class="form-control" placeholder="Description" name="description" id="description" rows="3" required="required"></textarea>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Date of Creation</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <input type="date" class="form-control" name="date_of_creation" id="date_of_creation" size="75" max="<?php //echo date("Y-m-d"); ?>" required="required">

                                                </div>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Duration</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <input class="html-duration-picker form-control" name="duration" id="duration" size="100" required="required" data-hide-seconds>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Status</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <select name="status" id="status" class="form-control" >
                                                        <option value="1" selected="selected">Active</option>
                                                        <option value="0" >Inactive</option>
                                                    </select>

                                                </div>

                                            </div>

                                        </div>

                                        <!--<div class="form-group row">

                                            <label class="col-sm-2 control-label">Upload Image</label>

                                            <div class="col-sm-10">

                                                <div class="col-sm-12">

                                                    <label class="btn btn-info" for="file-selector4">
                                                        <input id="file-selector4" type="file" style="display:none;" onchange="$('#upload-file-info4').html($(this).val());" name="image[]" multiple>
                                                        <span id="upload-file-info4">Upload Image</span>
                                                    </label>

                                                </div>

                                            </div>

                                        </div>-->



                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Save" name="submit_forms">
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <?php

        // }
        ?>



    </div>

    <div style="display: block">
        <div class="pagination col-lg-3 col-sm-12 col-xs-12 " style="float: right">

            <?php
            // if( $exists != 0 )
            // {
                // if($page_no_own != 1){
                    ?>
                    <a href="">&laquo;</a>
                    <?php
                // }
                // for($i=1;$i<($total_no_of_pages+1);$i++){

                    ?>
                    <a <?php //if($i==$page_no_own){ ?>class="active"  href=""></a>

                    <?php
                // }
                ?>
                <?php
               // if($page_no_own != $total_no_of_pages && $total_no_of_pages != 0){
                    ?>
                    <a href="">&raquo;</a>
                    <?php
            //     }
            // }
            ?>

        </div>
    </div>
</div>

@include('backend.layouts.portal.content_lower')
@include('backend.layouts.portal.content_bottom')

@endsection

@push('scripts')

<script>

</script>
@endpush
