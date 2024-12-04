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
                <label class="col-sm-12 control-label">First Name *</label>
                <div class="col-sm-12">
                    <input type="text" value="{{  $Model_Data->first_name }}" maxlength="20"
                           class="form-control validate" id="first_name"
                           placeholder="First Name" name="f_name" size="75"
                           data-parsley-minlength="2"
                           data-parsley-pattern="/^[a-z ,.'-]+$/i"
                           data-parsley-required readonly>
                    @if ($errors->has('first_name'))
                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-12 control-label">Last Name *</label>
                <div class="col-sm-12">
                    <input type="text" value="{{  $Model_Data->last_name }}" maxlength="20"
                           class="form-control validate" id="last_name"
                           placeholder="Last Name " name="l_name" size="75"
                           data-parsley-minlength="2"
                           data-parsley-pattern="/^[a-z ,.'-]+$/i"
                           data-parsley-required readonly>
                    @if ($errors->has('last_name'))
                    <span class="text-danger">{{ $errors->first('last_name') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-12 control-label">Email *</label>
                <div class="col-sm-12">
                    <input type="email" value="{{  $Model_Data->email }}"
                           class="form-control " id="email" placeholder="Email"
                           name="email" size="75"  readonly>
                    @if ($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-12 control-label">Phone Number *</label>
                <div class="col-sm-12">
                    <input type="text" value="{{  $Model_Data->phone }}"
                           class="form-control " id="contact_person"
                           placeholder="Contact Person" name="phone" size="75" readonly>
                    @if ($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <img src="{{ user_profile_image_path($Model_Data->id) }}" alt="here the image view" style="width: 100%; height: 250px;">
            </div>
        </div>
    </div>
</div>
