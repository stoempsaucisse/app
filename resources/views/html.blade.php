<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title')</title>
        @yield('meta')
        @yield('style')
    </head>
    <body>
        <main>
            @yield('content')
        </main>
        @yield('js')
    </body>
</html>
