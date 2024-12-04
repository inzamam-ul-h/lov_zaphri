

<div class="form-group row">
    <div class="col-lg-6 col-md-6 col-sm-12">
        <label class="col-sm-4 control-label">Reg No *</label>
        <div class="col-sm-8">
            <input type="text" value="{{ old('reg_no') }}"
                class="form-control " id="reg_no"
                placeholder="Reg No" name="reg_no" size="75" required>
            @if ($errors->has('reg_no'))
                <span class="text-danger">{{ $errors->first('reg_no') }}</span>
            @endif
        </div>
    </div>
</div>
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
        <label class="col-sm-4" for="exampleInputName">photo *</label>
        <label class="col-sm-8 btn btn-info">
            <input type="file" name="photo" required>
            <span>Upload photo</span>
        </label>
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
                        name="email" size="75" required >
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
			<div class="col-sm-6">
				<label class="col-sm-4 control-label">Contact Number *</label>
				<div class="col-sm-8">
					<input type="number" value="{{ old('contact_number') }}"
						class="form-control " id="contact_number"
						placeholder="Phone Number" name="contact_number" size="75" required>
					@if ($errors->has('contact_number'))
						<span class="text-danger">{{ $errors->first('contact_number') }}</span>
					@endif
				</div>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-lg-6 col-md-6 col-sm-12">
				<label class="col-sm-4 control-label">Contact Person *</label>
				<div class="col-sm-8">
					<input type="text" value="{{ old('contact_person') }}"
						class="form-control " id="contact_person"
						placeholder="Contact Person" name="contact_person" size="75" required>
					@if ($errors->has('contact_person'))
						<span class="text-danger">{{ $errors->first('contact_person') }}</span>
					@endif
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12">
				<label class="col-sm-4 control-label">Address *</label>
				<div class="col-sm-8">
					<textarea class="form-control" {{ old('address') }} placeholder="Your Address" name="address" id="address"
						rows="3" required></textarea>
					@if ($errors->has('address'))
						<span class="text-danger">{{ $errors->first('address') }}</span>
					@endif
				</div>
			</div>
		</div>
    </div>
</div>
