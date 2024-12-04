<div class="{{ $parent_classes }}" >
    <div class="ibox">
        <div class="ibox-title">
            <h5>
                <?php
                if (isset($checkbox)) {
                    ?>
                    <input type="checkbox" name="video_ids[]" value="{{ $video->id }}" style="float: left" <?php if (isset($checked) && $checked == 1) echo 'checked="checked"'; ?>> &nbsp;&nbsp; 
                    <?php }
                ?>
                {{ $video->title }}
            </h5>
        </div>
        <div class="ibox-content">

            <?php
            $video_id = $video->id;
            $SITE_URL = env('APP_URL');
            $defaultImage = asset(upload_url('defaults/video.png'));
            $uploadsPath = asset(upload_url('videos/' . $video_id));
            $video_image = $defaultImage;
            if (!empty($video->image) && $video->image != 'default_image') {
                $video->image = trim(str_replace('default_image,', '', $video->image));
                $video->image = trim(str_replace(',default_image', '', $video->image));
                $video->image = trim(str_replace('default_image', '', $video->image));
                $images = $video->image;
                $arr = explode(",", $images);
                $event_images = $uploadsPath . "/" . $arr[0];
                $images = explode(",", $video->image);
                $count = count($images);
                $i = 0;
                foreach ($images as $image) {
                    if ((empty($image) || $image == 'default_image') && $count > 1) {
                        
                    }
                    elseif ($i == 0) {
                        $i++;
                        $event_images = $defaultImage;
                        if (!empty($image) && $image != 'default_image') {
                            $video_image = $uploadsPath . "/" . $image;
                        }
                    }
                }
            }
            ?>
            <a href="{{ url('/manage/videos/'.$video->id) }}" target="_blank">
                <img src="{{ $video_image }}" style="width: 100%" >
            </a>
            <br>
            <label class="control-label">Duration :{{ $video->duration }} </label><br>
            <label class="control-label">Category :{{ $video->category_name }} </label><br>
            <label class="control-label">Created By :{{ $video->user_name }} </label><br>
            <p style="text-align: center">
                <a href="{{ url('/manage/videos/'.$video->id) }}" class="btn btn-xs btn-primary" target="_blank">
                    View Video
                </a>
            </p>
        </div>
    </div>
</div>

