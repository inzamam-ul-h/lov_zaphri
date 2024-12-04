<?php
$Site_Title = Site_Settings($Model_Data, 'site_title');
$general = $Model_Data;
$setting = $Setting_Data;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" style="background-color: #F9C303;">
                <li class="active"><a data-toggle="tab" href="#tab-1">Settings</a></li>
                <li><a data-toggle="tab" href="#tab-2">Verify</a></li>
                <li><a data-toggle="tab" href="#tab-10">Verification Code</a></li>
                <li><a data-toggle="tab" href="#tab-3">Welcome</a></li>
                <li><a data-toggle="tab" href="#tab-4">Forgot</a></li>
                <li><a data-toggle="tab" href="#tab-8">Reset</a></li>
                <li><a data-toggle="tab" href="#tab-5">Request</a></li>
                <li><a data-toggle="tab" href="#tab-6">Booking</a></li>
                <li><a data-toggle="tab" href="#tab-9">Reschedule</a></li>
                <li><a data-toggle="tab" href="#tab-7">Cancel Schedule</a></li>
                <li><a data-toggle="tab" href="#tab-11">Event Inquiry</a></li>
            </ul>
            <div class="tab-content">

                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update', $setting->id)}}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">


                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Settings</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="setting_submit_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Site Title</label>

                                            <div class="col-sm-10">{{ $setting->site_title }}</div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Site URL</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="site_url" placeholder="Site URL" size="75" value="{{ $setting->site_url }}" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Support Email</label>

                                            <div class="col-sm-10">

                                                <input type="email" class="form-control" name="support_email" placeholder="Support Email" size="75" value="{{ $setting->support_email }}" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Paypal Settings</label>

                                            <div class="col-sm-10">

                                                <select name="paypal">
                                                    <option value="1" {{ $setting->paypal == 1 ? 'selected' : '' }}>Sandbox</option>
                                                    <option value="2" {{ $setting->paypal == 2 ? 'selected' : '' }}>Live</option>
                                                </select>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Paypal Account</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="paypal_account" placeholder="Paypal Account" size="75" value="{{ $setting->paypal_account }}" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Paypal Client ID</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="paypal_client_id" placeholder="Paypal Client ID" size="75" value="{{ $setting->paypal_client_id }}" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Paypal Secret Key</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="paypal_secret_key" placeholder="Paypal Secret Key" size="75" value="{{ $setting->paypal_secret_key }}" required>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                        <br>


                        <?php

						$about_zaphry = $general->about_zaphry;
						$contact_phone_no = $general->phone;
						$contact_email = $general->email;
						$contact_address = $general->address;
						$contact_whatsapp = $general->whatsapp;
						$contact_facebook = $general->facebook;
						$contact_twitter = $general->twitter;
						$contact_dribble = $general->dribble;
						$contact_linkdin = $general->linkdin;
						$contact_youtube = $general->youtube;

                        ?>
                        <form name="contact_form" method="post" action="{{ route('general.update', $setting->id)}}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <hr />

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Contact Details</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_contact_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">About Zaphri</label>

                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="about_zaphry" placeholder="About Zaphri" size="75" maxlength="150" required><?php echo $about_zaphry;?></textarea>



                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Phone No.</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="phone_no" placeholder="Phone Number" size="75" value="<?php echo $contact_phone_no;?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Contact Email</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="contact_email" placeholder="Email" size="75" value="<?php echo $contact_email;?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Address</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="address" placeholder="Address" size="75" value="<?php echo $contact_address;?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Whatsapp</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="whatsapp" placeholder="Whatsapp" size="75" value="<?php echo $contact_whatsapp;?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Facebook Link</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="facebook" placeholder="Facebook" size="75" value="<?php echo $contact_facebook;?>" required>

                                            </div>

                                        </div>
                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Twitter Link</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="twitter" placeholder="Twitter" size="75" value="<?php echo $contact_twitter;?>" required>

                                            </div>

                                        </div>
                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Dribble Link</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="dribble" placeholder="Dribble" size="75" value="<?php echo $contact_dribble;?>" required>

                                            </div>

                                        </div>
                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Linkdin Link</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="linkdin" placeholder="Linkdin" size="75" value="<?php echo $contact_linkdin;?>" required>

                                            </div>

                                        </div>
                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Youtube Link</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="youtube" placeholder="Youtube" size="75" value="<?php echo $contact_youtube;?>" required>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-2" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Verify Email</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_verify_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="verify_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->verify_subject);?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [SITENAME]
                                                <br />
                                                [USERNAME]
                                                <br />
                                                [USERTYPE]
                                                <br />
                                                [Email]
                                                <br />
                                                [code]
                                                <br />
                                                [Button: link to verify]
                                                <br />
                                                [text: link to verify]
                                                <br>

                                                <textarea id="verify_email" name="verify_email" style="height: 200px; width: 730px;">{{ stripslashes($setting->verify_email) }}</textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-10" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Verification Code Email</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_verification_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="verification_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->verification_subject);?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [SITENAME]
                                                <br />
                                                [USERNAME]
                                                <br />
                                                [USERTYPE]
                                                <br />
                                                [Email]
                                                <br />
                                                [code]
                                                <br />
                                                [Button: link to verify]
                                                <br />
                                                [text: link to verify]
                                                <br>


                                                <textarea id="verification_email" name="verification_email"><?php echo stripslashes($setting->verification_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-3" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Welcome Email</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_welcome_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="welcome_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->welcome_subject);?>" required>

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [SITENAME]
                                                <br />
                                                [USERNAME]
                                                <br />
                                                [USERTYPE]
                                                <br />
                                                [Email]
                                                <br />

                                                <textarea id="welcome_email" name="welcome_email"><?php echo stripslashes($setting->welcome_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-4" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}" >
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Forgot Password</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_forgot_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="forgot_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->forgot_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [SITENAME]
                                                <br />
                                                [USERNAME]
                                                <br />
                                                [USERTYPE]
                                                <br />
                                                [Email]
                                                <br />
                                                [Button: link to reset]
                                                <br>
                                                [text: link to reset]
                                                <br>

                                                <textarea id="forgot_email" name="forgot_email"><?php echo stripslashes($setting->forgot_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-8" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}"  >
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Reset Password</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_reset_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="reset_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->reset_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [SITENAME]
                                                <br />
                                                [USERNAME]
                                                <br />
                                                [USERTYPE]
                                                <br />
                                                [Email]
                                                <br />

                                                <textarea id="reset_email" name="reset_email"><?php echo stripslashes($setting->reset_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-5" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post"  action="{{ route('general.update',$setting->id) }}" >
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Request Email</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_request_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="request_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->request_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [Username-1]
                                                <br>
                                                [Username-2]
                                                <br>
                                                [DateTime]
                                                <br>
                                                [Cancel-Link]
                                                <br>
                                                [Reschedule-Link]
                                                <br>
                                                [SITENAME]
                                                <br>

                                                <textarea id="request_email" name="request_email"><?php echo stripslashes($setting->request_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-6" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Booking Email</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_booking_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="booking_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->booking_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [Username-1]
                                                <br>
                                                [Username-2]
                                                <br>
                                                [DateTime]
                                                <br>
                                                [Cancel-Link]
                                                <br>
                                                [Reschedule-Link]
                                                <br>
                                                [SITENAME]
                                                <br>

                                                <textarea id="booking_email" name="booking_email"><?php echo stripslashes($setting->booking_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-9" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Reschedule</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_reschedule_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="reschedule_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->reschedule_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [Username-1]
                                                <br>
                                                [Username-2]
                                                <br>
                                                [DateTime]
                                                <br>
                                                [Cancel-Link]
                                                <br>
                                                [Reschedule-Link]
                                                <br>
                                                [SITENAME]
                                                <br>

                                                <textarea id="reschedule_email" name="reschedule_email"><?php echo stripslashes($setting->reschedule_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-7" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Cancel Schedule</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="submit_cancel_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="cancel_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->cancel_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [Username-1]
                                                <br>
                                                [Username-2]
                                                <br>
                                                [DateTime]
                                                <br>
                                                [Cancel-Link]
                                                <br>
                                                [Reschedule-Link]
                                                <br>
                                                [SITENAME]
                                                <br>

                                                <textarea id="cancel_email" name="cancel_email"><?php echo stripslashes($setting->cancel_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div id="tab-11" class="tab-pane">
                    <div class="panel-body">

                        <form name="settings_form" method="post" action="{{ route('general.update',$setting->id) }}">
                            @csrf
                            @method('put')
                            <div class="content-group">
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">

                                        <div class="form-group row">

                                            <div class="col-sm-9">
                                                <h3 class="font-bold">Event Inquiry</h3>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="submit" class="btn btn-primary sub-btn" value="Save" name="inquire_event_submit_form">
                                            </div>

                                        </div>
                                        <hr />

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Subject</label>

                                            <div class="col-sm-10">

                                                <input type="text" class="form-control" name="event_inquiry_subject" placeholder="Email Subject" size="75" value="<?php echo stripslashes($setting->inquire_event_subject);?>">

                                            </div>

                                        </div>

                                        <div class="form-group row">

                                            <label class="col-sm-2 control-label">Email Template</label>

                                            <div class="col-sm-10">

                                                Please do not delete following tags, it will be replaced dynamicaly while sending email to user
                                                <br />
                                                [USER_NAME]
                                                <br>
                                                [INQUIRY]
                                                <br>
                                                [INQUIRY_USER_NAME]
                                                <br>
                                                [EVENT_TITLE]
                                                <br>
                                                [SITENAME]
                                                <br>

                                                <textarea id="event_inquiry_email" name="event_inquiry_email"><?php echo stripslashes($setting->inquire_event_email);?></textarea>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>

        <div class="row mt-60">
            <div class="col-sm-12">
                &nbsp;
            </div>
        </div>
    </div>
</div>
