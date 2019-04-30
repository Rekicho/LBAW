@extends('layouts.app')

@section('css')
<link href="{{ asset('css/auth.css') }}" rel="stylesheet">
@endsection

@section('content')
<form class="form-signin my-5 text-center" method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}

    <h1 class="h3 mb-3 font-weight-normal">Sign in</h1>
   
    <label for="username" class="sr-only">Username</label>
    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" placeholder="Username" required autofocus />
   
    @if ($errors->has('username'))
        <span class="error">
          {{ $errors->first('username') }}
        </span>
    @endif

    <label for="password" class="sr-only">Password</label>
    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required/>

    @if ($errors->has('password'))
        <span class="error">
            {{ $errors->first('password') }}
        </span>
    @endif
    
    <div class="forgot">
        <a href="#">
            Forgot password?
        </a>
    </div>

    @if ($errors->has('is_enabled'))
    <span class="error">
        {{ $errors->first('is_enabled') }}
    </span>
    @endif

    <button class="btn btn-med btn-primary btn-block" type="submit">
        <i class="fas fa-sign-in-alt"></i> Sign in
    </button>
    <button class="btn btn-med btn-facebook btn-block" type="submit">
        <i class="fab fa-facebook-square"></i> Sign in with Facebook
			</button>
    <button class="btn btn-med btn-google btn-block" type="submit">
        <i class="fab fa-google"></i> Sign in with Google
    </button>
    <div class="alt">
        Don't have an account? <a href="{{ route('register') }}">Sign up!</a>
    </div>
</form>
@endsection
