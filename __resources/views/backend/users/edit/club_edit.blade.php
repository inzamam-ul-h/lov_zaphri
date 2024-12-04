<div class="form-group row">
    <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">REG NO. *</label><br>
                <input type="text" value="{{ old('reg_no', $UserPersonal->reg_no) }}"
                       class="form-control " id="reg_no"
                       placeholder="Registration Number" name="reg_no"
                       size="75" required >
                @if ($errors->has('reg_no'))
                <span class="text-danger">{{ $errors->first('reg_no') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">First Name *</label><br>
                <input type="text" value="{{ old('first_name', $UserPersonal->first_name) }}" maxlength="20"
                       class="form-control validate" id="first_name"
                       placeholder="First Name" name="f_name" size="75"
                       data-parsley-minlength="2"
                       data-parsley-pattern="/^[a-z ,.'-]+$/i"
                       data-parsley-required required>
                @if ($errors->has('first_name'))
                <span class="text-danger">{{ $errors->first('first_name') }}</span>
                @endif
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Last Name *</label><br>
                <input type="text" value="{{ old('last_name', $UserPersonal->last_name) }}" maxlength="20"
                       class="form-control validate" id="last_name"
                       placeholder="Last Name " name="l_name" size="75"
                       data-parsley-minlength="2"
                       data-parsley-pattern="/^[a-z ,.'-]+$/i"
                       data-parsley-required required>
                @if ($errors->has('last_name'))
                <span class="text-danger">{{ $errors->first('last_name') }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">
        @include('backend.users.edit.common_image')

        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="control-label" for="photo">logo</label><br>
                <label class="btn btn-info">
                    <input type="file" name="photo">
                    <span>change logo</span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="row mt-10">
    <div class="col-lg-12 row mt-10">
        <div class="form-group row">
            <div class="col-sm-12">
                <hr>
                <h3 class="font-bold">Contact Details</h3>
                <hr/>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Email *</label><br>
                <?php if (!empty($Model_Data->email)) { ?>
                    <input type="email" value="{{ old('email', $Model_Data->email) }}"
                           class="form-control " id="email" placeholder="Email"
                           name="email" size="75" readonly>
                       <?php }
                       else { ?>
                    <input type="email" value="{{ old('email', $Model_Data->email) }}"
                           class="form-control " id="email" placeholder="Email"
                           name="email" size="75" required >
<?php } ?>
                @if ($errors->has('email'))
                <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div class="col-sm-6">
                <label class="control-label">Contact Number *</label><br>
<?php if (!empty($UserPersonal->contact_number)) { ?>
                    <input type="text" value="{{  old('contact_number', $UserPersonal->contact_number) }}"
                           class="form-control " id="contact_number"
                           placeholder="Contact Number" name="contact_number" size="75" readonly>
<?php }
else { ?>
                    <input type="text" value="{{  old('contact_number', $UserPersonal->contact_number) }}"
                           class="form-control " id="contact_number"
                           placeholder="Contact Number" name="contact_number" size="75" required >
<?php } ?>
                @if ($errors->has('contact_number'))
                <span class="text-danger">{{ $errors->first('contact_number') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Contact Person *</label><br>
                <input type="text" value="{{ old('contact_person', $UserPersonal->contact_person) }}"
                       class="form-control " id="contact_person"
                       placeholder="Contact Person" name="contact_person" size="75" required>
                @if ($errors->has('contact_person'))
                <span class="text-danger">{{ $errors->first('contact_person') }}</span>
                @endif
            </div>
            <div class="col-sm-6">
                <label class="control-label">Address *</label><br>
                <textarea class="form-control" placeholder="Your Address" name="address" id="address"
                          rows="3" required>{{ old('address', $UserPersonal->address) }}</textarea>
                @if ($errors->has('address'))
                <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
