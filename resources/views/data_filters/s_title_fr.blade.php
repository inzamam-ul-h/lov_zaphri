<?php
$FR_check = FR_language_check();
?>
@if($FR_check == 1)
    <div class="col-sm-3 mb-1">
        <p class="mg-b-10">Title [Fr]</p>
        <input type="text" id="s_title_fr" name="s_title_fr" class="form-control filters_dt_cls" autocomplete="off" placeholder="Title">
    </div>
@endif