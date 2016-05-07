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
            <form  id='{{ $action }}-user' class="" method="POST" action="{{ ($action == 'create') ? action('UserController@store')  : action('UserController@update', ['user' => $user->id])}}">
                @if( $action == 'update' ) {{ method_field('PUT') }} @endif
                {{ csrf_field() }}
                @include('user.fieldset')

            </form>
            <span class='form-buttons'>
                <button type='submit' form='{{ $action }}-user' >{!! trans('form.save') !!}</button>
                <button type='reset' form='{{ $action }}-user' >{!! trans('form.reset') !!}</button>
                @if( $action == 'update' )
                    @can('delete', [Microffice\User::class, $user->id])
                    <form id='delete-user' class="" method="POST" action="{{ action('UserController@destroy', ['id' => $user->id]) }}">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type='submit' ><span id='delete-button'>{!! trans('form.delete') !!}<span></button>
                    </form>
                    @endcan
                @endif
            </span>
            <a href="{{ url('/logout') }}">{!! trans('auth.to-logout') !!}</a>
@endsection