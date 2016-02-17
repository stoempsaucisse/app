@extends('html')@section('title', 'microffice :: 404')@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
            <h1>{{ trans('error.404') }}</h1>
            <a href="{{ url('/logout') }}">{{ trans('auth.to-logout')}}</a>
@endsection