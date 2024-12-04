<?php
$AUTH_USER = Auth::user();
?>
<div class="form-group">

    <div class="row mt-2">
        <div class="col-sm-3">
            {!! Form::label('name', 'Name:') !!}
        </div>
        <div class="col-sm-9">
            <p>{{ $Model_Data->name }}</p>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-sm-3">
            {!! Form::label('display_to', 'Role for:') !!}
        </div>
        @if($Model_Data->display_to == 0)
        <div class="col-sm-9">
            <p>Admin Users Only</p>
        </div>
        @else
        <div class="col-sm-9">
            <p>Others</p>
        </div>
        @endif
    </div>
    
    <div class="row mt-2">
        <div class="col-sm-3">
            {!! Form::label('created_at', 'Created At:') !!}
        </div>
        <div class="col-sm-9">
            <p>{{ $Model_Data->created_at }}</p>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-sm-3">
            {!! Form::label('updated_at', 'Updated At:') !!}
        </div>
        <div class="col-sm-9">
            <p>{{ $Model_Data->updated_at }}</p>
        </div>
    </div>

    @if(Auth::user()->can('roles-edit') || Auth::user()->can('all'))
    <div class="row mt-2">
        <div class="col-sm-12 text-right">
            <?php echo edit_button('roles.edit', $Model_Data->id);?>
            <?php echo back_button('roles.index') ;?>
        </div>
    </div>
    @endif
</div>