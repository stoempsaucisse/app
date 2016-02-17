@extends('html')@section('title', trans('error.418'))@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
            <h1>{{ "418. I'm a teapot. Do you have some " . trans('error.418') . ", please?"}}</h1>
            <img src='{{ secure_asset("assets/img/camellia-sinensis.jpg") }}' />
@endsection