@extends('html')@section('title', 'Password Reset')@section('meta')
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta charset="UTF-8" />
@endsection
@section('style')
<link rel='stylesheet' href='{{ secure_asset("assets/auth/main.css") }}' type='text/css' />
@endsection
@section('js', '
')
@section('content')
<h1>Send me a password reset link, please.</h1>
<form method="POST" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}
                <input class="" type="text" name="email" value="{{ old('email') }}" placeholder="Email">
                <div class="form-footer">
                    <button id="login" type="submit">Send Password Reset Link</button>
                </div>
            </form>
@endsection
