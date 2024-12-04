<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>    
        @include('frontend.layouts.guest.head')
        @yield('headerInclude')
    </head>
    <body class="home">

        <div id="ritekhela-loader">

            <div id="ritekhela-loader-inner">

                <div id="ritekhela-shadow"></div>

                <div id="ritekhela-box"></div>

            </div>

        </div>


        <div class="ritekhela-wrapper">

            <header id="ritekhela-header" class="">

                @include('frontend.layouts.guest.header')

            </header>

            @yield('content')

            @include('frontend.layouts.guest.footer')

        </div>

        @include('frontend.layouts.guest.modals')

        @include('frontend.layouts.guest.foot')

        @yield('footerInclude')

    </body>
</html>