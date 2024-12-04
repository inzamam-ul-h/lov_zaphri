<!DOCTYPE html>
<html>
<head>
  <title><?php echo $plan->plan_name; ?></title>
</head>
<body>
  <div class="container">
    

    <h1><?php echo $plan->plan_name; ?></h1>

    <hr/>

    <?php
    foreach($planDetails as $detail)
    {
        ?>
        <p><b>Title : </b><?php echo $detail->title; ?></p>
        <p>
            <b>
                Description :
            </b>
            <?php echo $detail->description;?>
        </p>
        <p>
            <b>
                Duration :
            </b>
            <?php
            echo $detail->duration;
            ?>
        </p>
        <?php
		$uploadsPath = $video_uploadsPath.'/'.$detail->id;
		$images = explode(",", $detail->image);
		foreach ($images as $image) {
			if(!empty($image) && $image != 'default_image'){
            	$attachment = explode(".", $image);
				if($attachment[1] == 'jpg' || $attachment[1] == 'jpeg' || $attachment[1] == 'png' ){
					?>
					<img src="<?php echo $uploadsPath;?>/<?php echo $image;?>" alt="Image Not Found" style="height: 108px;width: 190px">
					<?php
				}
			}
		}
        ?>
        <hr/>
        <?php
    }
	?>
  </div>
</body>
</html>