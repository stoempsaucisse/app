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
            <h1>{!! trans('auth.password-send') !!}</h1>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}
                <input class="" type="text" name="email" value="{{ old('email') }}" placeholder="{!! trans('form.email') !!}">
                <div class="form-footer">
                    <button id="login" type="submit">{!! trans('form.send') !!}</button>
                </div>
            </form>
@endsection
