<!DOCTYPE html>
@inject('cache', 'cache')
@inject('session', 'session')
<html>
    <head>
        <title>@yield('title')</title>
        <meta name="author" content="stoempsaucisse@hotmail.com">
@yield('meta')
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css' type='text/css' />
@yield('style')
@stack('scripts')
    </head>
    <body>
        <main>
@yield('content')
        </main>
        @if( count($errors) > 0)
        <ul>
            @foreach( $errors->all() as $error )
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif
@if ( $cache->has( $session->getId() . '_NotFoundHttpException' ) )
        <div id="error">
            <h1>404 error</h1>
            <p>Following url does not exist : {{ $cache->pull( $session->getId() . '_NotFoundHttpException' ) }}</p>
        </div>
@endif
@yield('js')
    </body>
</html>
