
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
	<li class="{{ request()->is('manage/payments*') ? 'active' : '' }}">
		<a href="#">
			<i class="fa fa-money"></i>
			<span class="nav-label">Payments</span>
			<span class="fa arrow"></span>
		</a>
		<ul class="nav nav-second-level">
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
		</ul>
	</li>
	<li class="{{ request()->is('manage/videos*') ? 'active' : '' }}">
		<a href="{{ url('/manage/videos') }}">
			<i class="fa fa-video-camera"></i>
			<span class="nav-label">Videos</span>
		</a>
	</li>
	<li class="{{ request()->is('manage/events*') ? 'active' : '' }}">
		<a href="{{ url('/manage/events') }}">
			<i class="fa fa-calendar-minus-o"></i>
			<span class="nav-label">Events</span>
		</a>
	</li>
	{{-- <li class="{{ request()->is('manage/training-programs*') ? 'active' : '' }}">
		<a href="{{ url('/manage/training-programs') }}">
			<i class="fa fa-crosshairs"></i>
			<span class="nav-label">Training Programs</span>
		</a>
	</li>
	<li class="{{ request()->is('manage/training-plans*') ? 'active' : '' }}">
		<a href="{{ url('/manage/training-plans') }}">
			<i class="fa fa-crosshairs"></i>
			<span class="nav-label">Training Plans</span>
		</a>
	</li> --}}
