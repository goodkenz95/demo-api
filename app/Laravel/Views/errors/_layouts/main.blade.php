<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">

    <head>
        
        @include('errors._components.metas')
        @include('errors._components.styles')

    </head>

    <body>

        @yield('content')

        @include('errors._components.scripts')

    </body>

</html>