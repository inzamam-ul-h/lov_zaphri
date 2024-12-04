<div class="ritekhela-topstrip ritekhela-header-one">
    <div class="container">
        <div class="row">

            <aside class="col-md-6 sm-hide">
                <strong>Latest News :</strong>
                <div class="ritekhela-latest-news-slider">
                    <div class="ritekhela-latest-news-slider-layer">Messi is soon to join us. What are you waiting for ??</div>
                </div>
            </aside>
            <aside class="col-md-6">
                <ul class="ritekhela-user-strip">
                    <?php if (Auth::user()): ?>
                        <li>
                            <a href="{{ route('dashboard') }}" >
                                <i class="fa fa-user-alt"></i>
                                My Zaphri
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modalLoginEmail" id="id_modalLogin">
                                <i class="fa fa-user-alt"></i>
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modalSignupEmail">
                                <i class="fa fa-sign-in-alt"></i>
                                Signup
                            </a>
                        </li>
                    <?php endif;?>
                </ul>
            </aside>

        </div>
    </div>
</div>
<div class="ritekhela-main-header">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-6 sm-left">
                <a href="{{url('/home')}}" class="ritekhela-logo">
                    <img src="{{ asset_url('images/logo.png') }}" alt="image">
                </a>
            </div>
            <div class="col-md-8 col-sm-6 col-xs-6 sm-right">
                <div class="ritekhela-right-section">
                    <ul class="ritekhela-navsearch">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#ritekhelamodalsearch">
                                <i class="fa fa-search"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="ritekhela-navigation">
                        <span class="ritekhela-menu-link">
                            <span class="menu-bar"></span>
                            <span class="menu-bar"></span>
                            <span class="menu-bar"></span>
                        </span>
                        <nav id="main-nav">
                            <ul id="main-menu" class="sm sm-blue">

                                <li class="megamenu-wrap">
                                    <a href="{{url('/coaches')}}">Coach</a>
                                </li>
                                <li>
                                    <a href="{{url('/player')}}">Player</a>
                                </li>
                                <li>
                                    <a href="{{url('/club')}}">Club</a>
                                </li>
                                <li>
                                    <a href="{{url('/events')}}">Events</a>
                                </li>
                                <li>
                                    <a href="{{url('/contact-us')}}">Contact</a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
