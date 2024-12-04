<?php
$AUTH_USER = Auth::user();
?>
<div class="form-group row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <label class="col-sm-4 control-label">First Name *</label>
        <div class="col-sm-8">
            <input type="text" value="{{ old('f_name') }}" maxlength="20"
                class="form-control validate" id="first_name"
                placeholder="First Name" name="f_name" size="75"
                data-parsley-minlength="2"
                data-parsley-pattern="/^[a-z ,.'-]+$/i"
                data-parsley-required required>
            @if ($errors->has('first_name'))
                <span class="text-danger">{{ $errors->first('first_name') }}</span>
            @endif
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <label class="col-sm-4 control-label">Last Name *</label>

        <div class="col-sm-8">
            <input type="text" value="{{ old('l_name') }}" maxlength="20"
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
<div class="form-group row">
    <div class="col-sm-6">
        <label class="col-sm-4 control-label">About Me *</label>
        <div class="col-sm-8">
            <textarea class="form-control" {{ old('about_me') }} placeholder="About Me" name="about_me" id="about_me"
                rows="3" required></textarea>
            @if ($errors->has('about_me'))
                <span class="text-danger">{{ $errors->first('about_me') }}</span>
            @endif
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <label class="col-sm-4 control-label">Gender *</label>
        <div class="col-sm-8">
            <input type="radio" name="gender" value="Male" > Male
            <input type="radio" name="gender" value="Female" > Female
            <input type="radio" name="gender" value="Other" > Other
            @if ($errors->has('gender'))
                <span class="text-danger">{{ $errors->first('gender') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-6">
        <label class="col-sm-4" for="exampleInputName">photo *</label>
        <label class="col-sm-8 btn btn-info">
            <input type="file" name="photo" required>
            <span>Upload photo</span>
        </label>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-6">
        <label class="col-sm-4 control-label">Timezone *</label>
		<div class="col-sm-8">
			<div class="col-sm-12">
				<select class="form-control" id="time_zone" name="time_zone" required>
					<option value="">Choose Timezone</option>
					@foreach ($TimeZones as $TimeZone)
						<option value="{{ $TimeZone->id }}">
						{{ $TimeZone->name }}
						</option>
					@endforeach
				</select>

				@if ($errors->has('time_zone'))
				<span class="text-danger">{{ $errors->first('time_zone') }}</span>
				@endif
			</div>
		</div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <label class="col-sm-4 control-label">Date Of Birth *</label>
        <div class="col-sm-8">
            <input type="date" value=""
                class="form-control " id="dob"
                placeholder="Date Of Birth" name="dob"
                size="75" required>
            @if ($errors->has('dob'))
                <span class="text-danger">{{ $errors->first('dob') }}</span>
            @endif
        </div>
    </div>
</div>


<div class="row mt-10">
    <hr>
    <div class="col-lg-12 row mt-10">
        <div class="form-group row">
            <div class="col-sm-12">
                <h3 class="font-bold">Contact Details</h3>
                <hr>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-4 control-label">Email *</label>
                <div class="col-sm-8">
                    <input type="email" value="{{ old('email') }}"
                        class="form-control " id="email" placeholder="Email"
                        name="email" size="75" required>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
            </div>
			<div class="col-lg-6 col-md-6 col-sm-12">
				<label class="col-sm-4 control-label">Password *</label>
				 <div class="col-sm-8">
					<input type="password" class="form-control" value="" placeholder="Password" name="password" required>
						@if ($errors->has('password'))
							<span class="text-danger">{{ $errors->first('password') }}</span>
						@endif
				</div>
			</div>
        </div>
        <div class="form-group row">
			<div class="col-lg-6 col-md-6 col-sm-12">
				<label class="col-sm-4 control-label">Phone Number *</label>
				 <div class="col-sm-8">
					<input type="number" value="{{ old('phone') }}"
						class="form-control" id="phone"
						placeholder="Phone Number" name="phone" size="75" required>
					@if ($errors->has('phone'))
						<span class="text-danger">{{ $errors->first('phone') }}</span>
					@endif
				</div>
			</div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-4 control-label">Zip Code *</label>
                <div class="col-sm-8">
                    <input type="text" value="{{ old('zip_code') }}"
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
                <label class="col-sm-4 control-label">Experience</label>
				<div class="col-sm-8">
					<select class="form-control" id="organizational_name" name="organizational_name" required>
                        <option value="">Choose Experience</option>
                            <option value="{{ 'Recreational '}}" > {{ 'Recreational ' }}</option>
                            <option value="{{ 'Travel ' }}" > {{ 'Travel ' }} </option>

                    </select>
                    @if ($errors->has('organizational_name'))
						<span class="text-danger">{{ $errors->first('organizational_name') }}</span>
						@endif
				</div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-4 control-label">Number Of Years *</label>
				<div class="col-sm-8">
					<div class="col-sm-12">
                        <input type="number" value="{{ old('no_of_experience') }}"
						class="form-control" id="no_of_experience"
						placeholder="No of Years" name="no_of_experience" size="75" required>
						@if ($errors->has('no_of_experience'))
						<span class="text-danger">{{ $errors->first('no_of_experience') }}</span>
						@endif
					</div>
				</div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-4 control-label">Age Group *</label>
				<div class="col-sm-8">
					<div class="col-sm-12">
						<select class="form-control" id="age_group" name="age_group" required>
							<option value="">Choose Age Group</option>
							@foreach ($age_groups as $age_group)
								<option value="{{ $age_group->id }}" >
								{{ $age_group->title }}
								</option>
							@endforeach
						</select>
						@if ($errors->has('age_group'))
						<span class="text-danger">{{ $errors->first('age_group') }}</span>
						@endif
					</div>
				</div>
            </div>
        </div>
    </div>

</div>
<?php if($AUTH_USER->user_type == 3){?>

    <input type="hidden" name="club_associated" value="{{ $AUTH_USER->id }}" />
   <?php  }else { ?>
<div class="row mt-10" >
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
                <label class="col-sm-4 control-label">Club *</label>
				<div class="col-sm-8">
					<div class="col-sm-12">
						<select class="form-control" id="club_associated" name="club_associated" required>
							<option value="">Choose Club</option>
							@foreach ($clubs as $club)
								<option value="{{ $club->id }}">
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
    </div>

</div>
<?php } ?>
