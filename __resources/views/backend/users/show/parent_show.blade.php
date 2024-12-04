
<div class="form-group row">
    <div class="col-lg-8 col-md-8 col-sm-12">
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">First Name :</label>
                <div class="col-sm-12">
                    {{ $UserPersonal->first_name }}
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Last  Name :</label>
                <div class="col-sm-12">
                    {{ $UserPersonal->last_name }}
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Gender :</label>
                <div class="col-sm-12">
                    {{ $UserPersonal->gender}}
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
                <label class="col-sm-12 control-label">Email :</label>
                <div class="col-sm-12">
                    {{ $Model_Data->email }}
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Phone Number :</label>
                <div class="col-sm-12">
                    {{ $Model_Data->phone }}
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Address :</label>
                <div class="col-sm-12">
                    {{ $UserPersonal->address }}
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Zip Code :</label>
                <div class="col-sm-12">
                    {{ $UserPersonal->zip_code }}
                </div>
            </div>
        </div>
    </div>
</div>

