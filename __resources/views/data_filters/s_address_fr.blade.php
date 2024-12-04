<?php
$FR_check = FR_language_check();
?>
@if($FR_check == 1)
    <div class="col-sm-3 mb-1">
        <p class="mg-b-10">Address [Fr]</p>
        <input type="text" id="s_address_fr" name="s_address_fr" class="form-control filters_dt_cls" autocomplete="off" placeholder="Address">
    </div>
@endif