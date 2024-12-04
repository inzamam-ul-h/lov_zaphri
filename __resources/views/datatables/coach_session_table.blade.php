@include('backend.layouts.portal.content_middle')
<?php
$table_caption = (isset($table_caption)) ? $table_caption : "Today's Sessions";
$table_id = (isset($table_id)) ? $table_id : 'myDataTable1';
$table_url = (isset($table_url)) ? $table_url : route('dashboard.coach_upc_sessions_datatable');
?>
<div class="table table-striped table-hover">
    <input type="hidden" class="call_coach_datatable" data-table_id="<?php echo $table_id;?>" data-ajax_url="<?php echo $table_url;?>">
    <table id="<?php echo $table_id;?>" class="table" style="width:100%">
        <caption class="table-caption"><?php echo $table_caption;?></caption>
        <thead>
            <tr role ="row" class="heading table-heading">
                <th>S_no</th>
                <th>Start Time</th>
                <th>Session Date</th>
                <th>Type</th>
                <th>Player</th>
                <th>Payment status</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

@include('backend.layouts.portal.content_lower')