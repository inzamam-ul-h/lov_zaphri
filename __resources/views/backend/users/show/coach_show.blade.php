
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
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Time Zone :</label>
                <div class="col-sm-12">
                    @foreach ($TimeZones as $TimeZone)
                    {{ (isset($UserCalendar->time_zone) && $UserCalendar->time_zone == $TimeZone->id) ? $TimeZone->name : '' }}
                    @endforeach
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Public URL :</label>
                <div class="col-sm-12">
                    {{ $Model_Data->public_url }}
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Meeting Link :</label>
                <div class="col-sm-12">
                    {{ $UserPersonal->meetinglink }}
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <label class="col-sm-2 control-label">About Me :</label>
                <div class="col-sm-10">
                    {{ $UserPersonal->about_me }}
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
<div class="row mt-10">
    <hr>
    <div class="col-lg-12 row mt-10">
        <div class="form-group row">
            <div class="col-sm-12">
                <h3 class="font-bold">Professional Experience</h3>
            </div>
        </div>
        <hr />
       	<div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Club/Org: </label>
                <div class="col-sm-12">
                    {{ $UserProfessional->organizational_name }}
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label"> Highest age that you coached :</label>
                <div class="col-sm-12">
                    {{ $UserProfessional->agegroups }}
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Year of Experience :</label>
                <div class="col-sm-12">
                    {{ $UserProfessional->no_of_experience }}
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-10">
    <hr>
    <div class="col-lg-12 row mt-10">
        <div class="form-group row">
            <div class="col-sm-12">
                <h3 class="font-bold">Club Association</h3>
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <label class="col-sm-12 control-label">Club : </label>
                <div class="col-sm-12">
                    @foreach ($clubs as $club)
                    {{ (isset($UserProfessional->club) && $UserProfessional->club == $club->id) ? $club->name : '' }}
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
