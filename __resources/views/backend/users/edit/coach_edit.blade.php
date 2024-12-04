<div class="form-group row">
    <div class="col-lg-8 col-md-8 col-sm-12">
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
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Gender *</label><br>
                <input type="radio" name="gender" value="Male" {{ $UserPersonal->gender === 'Male' ? 'checked' : '' }}> Male
                <input type="radio" name="gender" value="Female" {{ $UserPersonal->gender === 'Female' ? 'checked' : '' }}> Female
                <input type="radio" name="gender" value="Other" {{ $UserPersonal->gender === 'Other' ? 'checked' : '' }}> Other
                @if ($errors->has('gender'))
                <span class="text-danger">{{ $errors->first('gender') }}</span>
                @endif
            </div>
            <div class="col-sm-6">
                <label class="control-label">Timezone *</label><br>
                <select class="form-control" id="time_zone" name="time_zone" required>
                    <option value="">Choose Timezone</option>
                    @foreach ($TimeZones as $TimeZone)
                    <option value="{{ $TimeZone->id }}"{{ (isset($UserCalendar->time_zone) && $UserCalendar->time_zone == $TimeZone->id) ? ' selected' : '' }}>
                        {{ $TimeZone->name }}
                    </option>
                    @endforeach
                </select>

                @if ($errors->has('time_zone'))
                <span class="text-danger">{{ $errors->first('time_zone') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="control-label">Public URL *</label><br>
                <input type="text" value="{{ old('public_url', $Model_Data->public_url) }}"
                       class="form-control " id="public_url" placeholder="Public URL"
                       name="public_url" size="75" readonly >
                @if ($errors->has('public_url'))
                <span class="text-danger">{{ $errors->first('public_url') }}</span>
                @endif
            </div>
        </div>
        <div class="from-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="control-label">Meeting Link *</label><br>
                <input type="text" value="{{ old('meeting_link', $UserPersonal->meetinglink) }}"
                       class="form-control " id="meeting_link"
                       placeholder="Meeting Link" name="meeting_link"
                       size="75" required>
                @if ($errors->has('meeting_link'))
                <span class="text-danger">{{ $errors->first('meeting_link') }}</span>
                @endif
            </div>
        </div>
        <div class="from-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="control-label">About Me *</label><br>
                <textarea class="form-control" placeholder="About Me" name="about_me" id="about_me"
                          rows="3" required>{{ old('about_me', $UserPersonal->about_me) }}</textarea>
                @if ($errors->has('about_me'))
                <span class="text-danger">{{ $errors->first('about_me') }}</span>
                @endif
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
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Phone Number *</label><br>
<?php if (!empty($Model_Data->phone)) { ?>
                    <input type="text" value="{{ old('phone', $Model_Data->phone) }}"
                           class="form-control " id="phone"
                           placeholder="Phone Number" name="phone" size="75" readonly>
<?php }
else { ?>
                    <input type="text" value="{{ old('phone', $Model_Data->phone) }}"
                           class="form-control " id="phone"
                           placeholder="Phone Number" name="phone" size="75" required>
<?php } ?>
                @if ($errors->has('phone'))
                <span class="text-danger">{{ $errors->first('phone') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-6">
                <label class="control-label">Address *</label><br>
                <textarea class="form-control" placeholder="Your Address" name="address" id="address"
                          rows="3">{{ old('address', $UserPersonal->address) }}</textarea>
                @if ($errors->has('address'))
                <span class="text-danger">{{ $errors->first('address') }}</span>
                @endif
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Zip Code *</label><br>
                <input type="text" value="{{ old('zip_code', $UserPersonal->zip_code) }}"
                       class="form-control " id="zip_code"
                       placeholder="Zip Code" name="zip_code"
                       size="75" required>
                @if ($errors->has('zip_code'))
                <span class="text-danger">{{ $errors->first('zip_code') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="row mt-10">

    <div class="col-lg-12 row mt-10">
        <div class="form-group row">
            <div class="col-sm-12">
                <hr>
                <h3 class="font-bold">Professional Profile</h3>
                <hr />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Year of Experience *</label><br>
                <select class="form-control" id="no_of_experience" name="no_of_experience" required>
                    <option value="" >Choose Experience</option>
                    @foreach ($no_of_experience as $experience)
                    <option value="{{ $experience->id }}" <?php echo (isset($UserProfessional->no_of_experience) && $UserProfessional->no_of_experience == $experience->id) ? ' selected' : ''; ?> >
                        {{ $experience->title }}
                    </option>
                    @endforeach
                </select>

                @if ($errors->has('no_of_experience'))
                <span class="text-danger">{{ $errors->first('no_of_experience') }}</span>
                @endif
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Highest age that you coached *</label><br>
                <select class="form-control" id="age_group" name="age_group" required>
                    <option value="">Choose Age Group</option>
                    @foreach ($age_groups as $age_group)
                    <option value="{{ $age_group->id }}" <?php echo (isset($UserProfessional->agegroups) && $UserProfessional->agegroups == $age_group->id) ? ' selected' : ''; ?> >
                        {{ $age_group->title }}
                    </option>
                    @endforeach
                </select>

                @if ($errors->has('age_group'))
                <span class="text-danger">{{ $errors->first('age_group') }}</span>
                @endif
            </div>
        </div>

       	<div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="control-label">Club/ Org *</label><br>
                <input type="text" value="{{ old('organizational_name', $UserProfessional->organizational_name) }}"
                       class="form-control " id="organizational_name"
                       placeholder="Club/ Org" name="organizational_name"
                       size="75" required>
                @if ($errors->has('organizational_name'))
                <span class="text-danger">{{ $errors->first('organizational_name') }}</span>
                @endif
            </div>
        </div>
    </div>

</div>

<div class="row mt-10">
    <div class="col-lg-12 row mt-10">
        <div class="form-group row">
            <div class="col-sm-12">
                <hr>
                <h3 class="font-bold">Club Association</h3>
                <hr>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="control-label">Club</label><br>
                <select class="form-control" id="club_associated" name="club_associated" required>
                    <option value="">Choose Club</option>
                    @foreach ($clubs as $club)
                    <option value="{{ $club->id }}"{{ (isset($UserProfessional->club) && $UserProfessional->club == $club->id) ? ' selected' : '' }}>
                        {{ $club->name }}
                    </option>
                    @endforeach
                </select>

                @if ($errors->has('club_associated'))
                <span class="text-danger">{{ $errors->first('club_associated') }}</span>
                @endif
            </div>
        </div>
    </div>

</div>
