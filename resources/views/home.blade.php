@extends('html')@section('title', 'Homepage')@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
            <h1>Hi {{ Auth::user()->name }}, this is microffice's homepage.</h1>
            <a href="{{ url('/logout') }}">Logout</a>
@endsection