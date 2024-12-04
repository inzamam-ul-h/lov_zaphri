@php
use App\Models\ContactDetail;

$Settings = ContactDetail::find(1);

@endphp

<!DOCTYPE html>
<html lang="en">
    <head>    
        @include('backend.layouts.portal.head')
        @yield('headerInclude')
    </head>
    <body class="">

        <div id="wrapper">

            @include('backend.layouts.portal.sidebar')

            <div id="page-wrapper" class="gray-bg dashbard-1">

                @include('backend.layouts.portal.header')

                @yield('content')

                @include('backend.layouts.portal.footer')

            </div>

        </div>

        @include('backend.layouts.portal.foot')

        @yield('footerInclude')

    </body>

</html>