<?php
$FR_check = FR_language_check();
?>
@if($FR_check == 1)
    <div class="col-sm-3 mb-1">
        <p class="mg-b-10">Name [Fr]</p>
        <input type="text" id="s_name_fr" name="s_name_fr" class="form-control filters_dt_cls" autocomplete="off" placeholder="Name">
    </div>
@endif