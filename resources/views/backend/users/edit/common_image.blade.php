
<div class="form-group row" id="user_profile_image_div">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <?php $file_path = user_profile_image_path($Model_Data->id) ?>
        <img src="{{ $file_path }}" alt="here the image view" style="width: 100%; height: 300px;">
		@if (strpos($file_path, 'uploads/defaults') === false)
			<?php 
			$del_file = explode("/",$Model_Data->photo_url);
			$count_delete = count($del_file);
			$del_link = "users/".$Model_Data->id."/photo";
			$del_file_name = $del_file[$count_delete-1];
			?>
			<a class="btn btn-danger delete-confirm" 
			   data-file_name="{{ $del_file_name }}"
			   data-url="{{ url('manage/file-delete/'.$del_link) }}" 
			   data-parent="user_profile_image_div" 
			   title="Delete File">
				<i class="fa fa-trash fa-lg"></i> Delete
			</a>
		@endif
    </div>
</div>
