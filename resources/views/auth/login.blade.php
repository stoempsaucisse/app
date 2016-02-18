@extends('html')@section('title', trans('auth.to-login'))@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/auth/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
<h1{{trans('auth.welcome')}}</h1>
<form method="POST" action="{{ url('/login') }}">
                {!! csrf_field() !!}
                <input class="stacked first" type="text" name="login" value="{{ old('login') }}" placeholder="{{trans('auth.login')}}">
                <input class="stacked" type="password" name="password" id="password" placeholder="{{trans('auth.password')}}">
                <label class="input stacked" for="remember">{{trans('auth.remember')}}<input id="remember" type="checkbox" name="remember" /></label>
                <div class="form-footer">
                    <button id="login" type="submit">{{trans('auth.to-login')}}</button>
                </div>
            </form>
            <a class="btn btn-link" href="{{ url('/password/reset') }}">{{trans('auth.forgotten')}}</a>
@endsection