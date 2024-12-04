<?php
$AUTH_USER = Auth::user();
?>
<div class="form-group">

    <div class="row mt-2">
        <div class="col-sm-3">
            {!! Form::label('name', 'Name:') !!}
        </div>
        <div class="col-sm-9">
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-sm-3">
            {!! Form::label('display_to', 'Role for:') !!}
        </div>
        <div class="col-sm-9">
            {!! Form::select('display_to', ['0' => 'Admin Users Only', '1' => 'Others'], null, ['class' => 'form-control']) !!}
        </div>
    </div>
    
    <?php
    if(isset($Model_Data))
    {
        ?>
        <div class="row mt-3">
            <div class=" form-group col-12 text-right">
                <?php echo update_button();?>
                <?php echo cancel_button('roles.index');?>
            </div>
        </div>
        <?php
    } 
	else
    {
        ?>
        <div class="row mt-3">
            <div class=" form-group col-12 text-right">
                <?php echo save_button();?>
                <?php echo cancel_button('roles.index');?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
