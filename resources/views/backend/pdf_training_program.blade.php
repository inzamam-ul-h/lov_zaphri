<!DOCTYPE html>
<html>
<head>
  <title><?php echo $program->title; ?></title>
</head>
<body>
  <div class="container">


    <h1><?php echo $program->title; ?></h1>

    <hr/>

    <?php
    foreach($programDetails as $detail)
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

            if ($detail->duration == 1) {
                echo " Week";
            } else {
                echo " Weeks";
            }
            ?>
        </p>
        <p>
			<b>Start date-time : </b>
			<?php echo date('m/d/Y h:i:s a', strtotime($detail->start_date_time)); ?>
	  	</p>
        <?php
        $attachments=$detail->images;

        if( $attachments != ""){
            $attachment = explode(".", $attachments);

            if($attachment[0] == 'jpg' || $attachment[0] == 'jpeg' || $attachment[0] == 'png' ){
                ?>
                <img src="<?php echo $uploadsPath;?>/<?php echo $attachments;?>" alt="Image Not Found" style="height: 108px;width: 190px">
                <?php
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
