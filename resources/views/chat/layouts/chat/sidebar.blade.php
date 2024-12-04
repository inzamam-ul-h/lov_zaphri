<?php
$Site_Title = Site_Settings($Settings, 'site_title');

$AUTH_USER = Auth::user();
$logged_in_type = $AUTH_USER->user_type;
$user_id = $AUTH_USER->id;
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a href="{{ url('/manage/dashboard') }}">
                        <img src="{{ asset_url('images/logo_white.png') }}" alt="{{ $Site_Title }}">
                    </a>
                    <a href="{{ url('/manage/dashboard') }}">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ get_user_name($user_id) }}</strong>
                            </span>
                            <span class="text-muted text-xs block">
                                Zaphri <b class="caret"></b>
                            </span>
                        </span>
                    </a>
                    <?php /* ?>
                      <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                      <span class="clear">
                      <span class="block m-t-xs">
                      <strong class="font-bold">{{ get_user_name($user_id) }}</strong>
                      </span>
                      <span class="text-muted text-xs block">
                      Zaphri <b class="caret"></b>
                      </span>
                      </span>
                      </a><ul class="dropdown-menu animated fadeInRight m-t-xs">
                      <li><a href="{{ url('/manage/users/profile') }}">Profile</a></li>
                      <li><a href="{{ url('/manage/users/change-password') }}">Change Password</a></li>
                      <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                      </ul><?php */ ?>
                </div>
                <div class="logo-element">
                    {{ get_user_short_name($user_id) }}
                </div>
            </li>

            <li class="{{ request()->is('manage/dashboard') ? 'active' : '' }}">
                <a href="{{ url('/manage/dashboard') }}">
                    <i class="fa fa-home"></i>
                    <span class="nav-label">My Dashboard</span>
                </a>
            </li>
            @if(Auth::user()->user_type == 1)
            <li class="{{ request()->is('manage/availability*') ? 'active' : '' }}">
                <a href="{{ url('/manage/availability') }}">
                    <i class="fa fa-calendar"></i>
                    <span class="nav-label">Availability</span>
                </a>
            </li>
            @endif	 
            @if(Auth::user()->can('sessions-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/sessions*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-list "></i>
                    <span class="nav-label">Sessions</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('manage/sessions/upcoming*') ? 'active' : '' }}">
                        <a href="{{ url('/manage/sessions/upcoming') }}">
                            Upcoming
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/sessions/history') ? 'active' : '' }}">
                        <a href="{{ url('/manage/sessions/history') }}">
                            History
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->can('bookings-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/bookings*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-calendar-check-o"></i>
                    <span class="nav-label">Bookings</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('manage/bookings/upcoming') ? 'active' : '' }}">
                        <a href="{{ url('/manage/bookings/upcoming') }}">
                            Upcoming
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/bookings/history') ? 'active' : '' }}">
                        <a href="{{ url('/manage/bookings/history') }}">
                            History
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->can('payments-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/payments*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-money"></i>
                    <span class="nav-label">Payments</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    @if(Auth::user()->user_type == 1)
                    <li class="{{ request()->is('manage/payments/history') ? 'active' : '' }}">
                        <a href="{{ url('/manage/payments/history') }}">
                            My Payments
                        </a>
                    </li>
                    @else
                    <li class="{{ request()->is('manage/payments/pending') ? 'active' : '' }}">
                        <a href="{{ url('/manage/payments/pending') }}">
                            Pending
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/payments/history') ? 'active' : '' }}">
                        <a href="{{ url('/manage/payments/history') }}">
                            History
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if(Auth::user()->user_type == 3)
            <?php
            $li_class = '';
            if (request()->is('manage/clubs/member/coaches') || request()->is('manage/clubs/member/players')) {
                $li_class = 'active';
            }
            ?>
            <li class="{{ $li_class }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span class="nav-label">Associations</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('manage/clubs/member/coaches') ? 'active' : '' }}">
                        <a href="{{ url('/manage/clubs/member/coaches') }}">
                            Coaches
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/clubs/member/players') ? 'active' : '' }}">
                        <a href="{{ url('/manage/clubs/member/players') }}">
                            Players
                        </a>
                    </li>
                </ul>
            </li>
            @elseif(Auth::user()->user_type == 4)
            <?php
            $li_class = '';
            if (request()->is('manage/parent/member/players') || request()->is('manage/parent/index') || request()->is('manage/parent/history')) {
                $li_class = 'active';
            }
            ?>
            <li class="{{ $li_class }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span class="nav-label">Associations</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('manage/parent/member/players') ? 'active' : '' }}">
                        <a href="{{ url('/manage/parent/member/players') }}">
                            Players
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/parent/index') ? 'active' : '' }}">
                        <a href="{{ url('/manage/parent/index') }}">
                            Invite Players
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/parent/history') ? 'active' : '' }}">
                        <a href="{{ url('/manage/parent/history') }}">
                            Invite History
                        </a>
                    </li>
                </ul>
            </li>
            @elseif(Auth::user()->user_type == 2)
            <?php
            $li_class = '';
            if (request()->is('manage/player/member/parent') || request()->is('manage/parent/history')) {
                $li_class = 'active';
            }
            ?>
            <li class="{{ $li_class }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span class="nav-label">Associations</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('manage/player/member/parent') ? 'active' : '' }}">
                        <a href="{{ url('/manage/player/member/parent') }}">
                            Parent Profile
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/parent/history') ? 'active' : '' }}">
                        <a href="{{ url('/manage/parent/history') }}">
                            Invite History
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @if(Auth::user()->can('teams-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/teams*') ? 'active' : '' }}">
                <a href="{{ url('/manage/teams') }}">
                    <i class="fa fa-video-camera"></i>
                    <span class="nav-label">Teams</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('training-programs-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/training-programs*') ? 'active' : '' }}">
                <a href="{{ url('/manage/training-programs') }}">
                    <i class="fa fa-crosshairs"></i>
                    <span class="nav-label">Training Programs</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('training-plans-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/training-plans*') ? 'active' : '' }}">
                <a href="{{ url('/manage/training-plans') }}">
                    <i class="fa fa-crosshairs"></i>
                    <span class="nav-label">Training Plans</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('videos-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/videos*') ? 'active' : '' }}">
                <a href="{{ url('/manage/videos') }}">
                    <i class="fa fa-video-camera"></i>
                    <span class="nav-label">Videos</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('events-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/events*') ? 'active' : '' }}">
                <a href="{{ url('/manage/events') }}">
                    <i class="fa fa-calendar-minus-o"></i>
                    <span class="nav-label">Events</span>
                </a>
            </li>
            @endif


            @if(Auth::user()->can('subscribers-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/subscribers') ? 'active' : '' }}">
                <a href="{{ url('/manage/subscribers') }}">
                    <i class="fa fa-address-book-o"></i>
                    <span class="nav-label">Subscribers</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('contact_request-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/contacts') ? 'active' : '' }}">
                <a href="{{ url('/manage/contacts') }}">
                    <i class="fa fa-envelope-open"></i>
                    <span class="nav-label">Contact Requests</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('users-listing') || Auth::user()->can('all'))
            <?php
            $li_class = '';
            if (request()->is('manage/users/coaches/1') || request()->is('manage/users/players/2') ||
                    request()->is('manage/users/clubs/3') || request()->is('manage/users/parents/4') || request()->is('manage/users*')) {
                $li_class = 'active';
            }
            ?>
            <li class="{{ $li_class }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span class="nav-label">Users</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    @if(Auth::user()->user_type == 3)
                    <li class="{{ request()->is('manage/users/coaches/1') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/coaches/1') }}">
                            Coaches
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/users/players/2') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/players/2') }}">
                            Players
                        </a>
                    </li>
                    @else
                    <li class="{{ request()->is('manage/users/clubs/3') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/clubs/3') }}">
                            Clubs
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/users/coaches/1') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/coaches/1') }}">
                            Coaches
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/users/players/2') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/players/2') }}">
                            Players
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/users/parents/4') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/parents/4') }}">
                            Parents
                        </a>
                    </li>
                    <li class="{{ request()->is('manage/users/admins/0') ? 'active' : '' }}">
                        <a href="{{ url('/manage/users/admins/0') }}">
                            Admin Users
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if(Auth::user()->can('roles-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/roles*') ? 'active' : '' }}">
                <a href="{{ url('/manage/roles') }}">
                    <i class="fa fa-bars"></i>
                    <span class="nav-label">Roles</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('categories-listing') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/categories*') ? 'active' : '' }}">
                <a href="{{ url('/manage/categories') }}">
                    <i class="fa fa-bars"></i>
                    <span class="nav-label">Categories</span>
                </a>
            </li>
            @endif

            @if(Auth::user()->can('settings-edit') || Auth::user()->can('all'))
            <li class="{{ request()->is('manage/general') ? 'active' : '' }}">
                <a href="{{ url('/manage/general') }}">
                    <i class="fa fa-cog"></i>
                    <span class="nav-label">Settings</span>
                </a>
            </li>
            @endif
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</nav>
