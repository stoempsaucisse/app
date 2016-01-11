@extends('html')@section('title', 'Login')@section('meta')
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
<link rel='stylesheet' href='{{ secure_asset("assets/auth/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
<h1>Welcome to microffice, please log in.</h1>
<form method="POST" action="{{ url('/login') }}">
                {!! csrf_field() !!}
                <input class="stacked first" type="text" name="name" value="{{ old('name') }}" placeholder="Name">
                <input class="stacked" type="password" name="password" id="password" placeholder="Password">
                <label class="input stacked" for="remember">Remember Me <input id="remember" type="checkbox" name="remember" /></label>
                <div class="form-footer">
                    <button id="login" type="submit">Login</button>
                </div>
            </form>
            <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
@endsection