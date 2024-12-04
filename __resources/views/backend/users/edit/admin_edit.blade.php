<?php
$name = explode(' ', $Model_Data->name);
$Model_Data->first_name = $name[0];
$Model_Data->last_name = '';
if (count($name) > 1)
    $Model_Data->last_name = $name[1];
?>
<div class="form-group row">
    <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-4 control-label">First Name *</label>
                <div class="col-sm-8">
                    <input type="text" value="{{ old('first_name', $Model_Data->first_name) }}" maxlength="20"
                           class="form-control validate" id="first_name"
                           placeholder="First Name" name="f_name" size="75"
                           data-parsley-minlength="2"
                           data-parsley-pattern="/^[a-z ,.'-]+$/i"
                           data-parsley-required>
                    @if ($errors->has('first_name'))
                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-4 control-label">Last Name *</label>
                <div class="col-sm-8">
                    <input type="text" value="{{ old('last_name', $Model_Data->last_name) }}" maxlength="20"
                           class="form-control validate" id="last_name"
                           placeholder="Last Name " name="l_name" size="75"
                           data-parsley-minlength="2"
                           data-parsley-pattern="/^[a-z ,.'-]+$/i"
                           data-parsley-required>
                    @if ($errors->has('last_name'))
                    <span class="text-danger">{{ $errors->first('last_name') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-4 control-label">Email *</label>
                <div class="col-sm-8">
                    <input type="email" value="{{ old('email', $Model_Data->email) }}"
                           class="form-control " id="email" placeholder="Email"
                           name="email" size="75" >
                    @if ($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-4 control-label">password</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" value="" placeholder="Password" name="password">
                    @if ($errors->has('password'))
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-4 control-label">Phone Number *</label>
                <div class="col-sm-8">
                    <input type="text" value="{{ old('phone', $Model_Data->phone) }}"
                           class="form-control " id="contact_person"
                           placeholder="Phone Number" name="phone" size="75">
                    @if ($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        @include('backend.users.edit.common_image')

        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="control-label" for="photo">Photo</label><br>
                <label class="btn btn-info">
                    <input type="file" name="photo">
                    <span>change photo</span>
                </label>
            </div>
        </div>
    </div>
</div>
