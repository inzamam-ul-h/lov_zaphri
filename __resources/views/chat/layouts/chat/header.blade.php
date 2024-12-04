<?php
$AUTH_USER = Auth::user();
$logged_in_type = $AUTH_USER->user_type;
$user_id = $AUTH_USER->id;

?>
<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i> </a>
        </div>
        <ul class="nav navbar-top-links navbar-right sm-right">
            <li>
				<a href="{{ url('/manage/users/'.$user_id) }}">
                	Welcome <?php echo get_user_name($user_id);?>!
                </a>
            </li>
            <li>
                <a href="{{ url('/') }}" target="_blank">
                    <img src="{{ asset_url('images/zaphry_emblem.png') }}" height="35px" width="35px">
                </a>
            </li>
            <li class="dropdown sm-hide">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <img style=" border-radius: 50%;" src="{{ user_profile_image_path($user_id) }}"
                        alt="image" height="35px" width="35px">
                </a>
                <ul class="dropdown-menu dropdown-messages dropdown-menu-right">
					<li><a href="{{ url('/manage/users/'.$user_id) }}">Profile</a></li>
					<li><a href="{{ url('/manage/users/change-password') }}">Change Password</a></li>
					<li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</div>

<style>
    /*li {
        display: inline-block;
        margin: 0 10px;
    }*/
</style>

<?php if($logged_in_type > 0) { ?>
<input type="hidden" id="check_session_user_id" value="{{ $user_id }}">
<button type="button" id="btn_check_session" class="btn btn-primary hide" data-toggle="modal"
    data-target="#myModalCheckSession"> Check Session </button>
<div class="modal inmodal" id="myModalCheckSession" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content check_modal_content animated fadeIn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Cancel</span>
                </button>
                <h4 class="modal-title">
                    <span>YOUR NEXT SESSION WILL START IN</span>
                </h4>
                <h4 class="modal-title mt-20">
                    <span id="check_modal_date" class="countdown"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12" id="check_modal_body"></div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if($logged_in_type == 1) { ?>
                <a href="{{ url('/sessions') }}" class="btn btn-primary">MY SESSIONS</a>
                <a href="{{ url('/availability') }}" class="btn btn-primary">CREATE SESSION</a>
                <?php } else { ?>
                <a href="{{ url('/bookings/all') }}" class="btn btn-primary">MY TRAININGS</a>
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#upModal" title="Assess">BOOK TRAINING</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
