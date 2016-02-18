@extends('html')@section('title', trans('auth.password-reset'))@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/auth/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
            <h1>{{trans('auth.password-reset-title')}}</h1>
            <form method="POST" action="{{ url('/password/reset') }}">
                {!! csrf_field() !!}
                <input type="hidden" name="token" value="{{ $token }}">
                <input class="stacked first" type="email" name="email" value="{{ $email or old('email') }}" placeholder="Email">
                <input class="stacked" type="password" name="password" placeholder="{{ trans('auth.password') }}">
                <input class="stacked" type="password" name="password_confirmation" placeholder="{{ trans('auth.confirm_password') }}">
                <div class="form-footer">
                    <button id="login" type="submit">{{trans('form.reset')}}</button>
                </div>
            </form>
@endsection
