@extends('html')@section('title', trans('user.index'))@section('meta')
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
        <link rel='stylesheet' href='{{ secure_asset("assets/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
            <h1>{{ trans('user.index') }}</h1>
            <ul id='user-list'>
                @each('user.list-item', $users, 'user')

            </ul>
@can('create', [Microffice\User::class])
            <a href="{{ action('UserController@create') }}">{{ trans('user.create')}}</a>
@endcan
            <a href="{{ url('/logout') }}">{{ trans('auth.to-logout')}}</a>
@endsection