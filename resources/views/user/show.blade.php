@extends('html')@section('title', trans('user.' . $action))@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
            <h1>{{ trans('user.' . $action) }}</h1>
            <ul>
                <li>{!! trans('user.name') !!} : {{ $user->name }}</li>
                <li>{!! trans('form.email') !!} : {{ $user->email }}</li>
            </ul>
            <a href="{{ url('/logout') }}">{!! trans('auth.to-logout') !!}</a>
@endsection