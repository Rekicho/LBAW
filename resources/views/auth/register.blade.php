@extends('layouts.app')

@section('css')
<link href="{{ asset('css/auth.css') }}" rel="stylesheet">
@endsection

@section('content')
<form class="form-signin my-5 text-center" method="POST" action="{{ route('register') }}">
    {{ csrf_field() }}
    <h1 class="h3 mb-3 font-weight-normal">Sign up</h1>

    <label for="username" class="sr-only">Name</label>
    <input type="text" name="username" id="username" class="form-control" placeholder="Nickname" value="{{ old('username') }}" required autofocus />

    @if ($errors->has('name'))
      <span class="error">
          {{ $errors->first('name') }}
      </span>
    @endif

    <label for="email" class="sr-only">Email address</label>
    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Email address" required/>
    
    @if ($errors->has('email'))
      <span class="error">
          {{ $errors->first('email') }}
      </span>
    @endif
    
    <label for="password" class="sr-only">Password</label>
    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required/>
    
    @if ($errors->has('password'))
      <span class="error">
          {{ $errors->first('password') }}
      </span>
    @endif
    
    <label for="password_confirmation" class="sr-only">Confirm Password</label>
    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required/>


    <button class="btn btn-lg btn-primary btn-block" type="submit">
				<i class="fas fa-sign-in-alt"></i> Sign up
			</button>
			<button class="btn btn-lg btn-facebook btn-block" type="submit">
				<i class="fab fa-facebook-square"></i> Sign up with Facebook
			</button>
			<button class="btn btn-lg btn-google btn-block" type="submit">
				<i class="fab fa-google"></i> Sign up with Google
			</button>
			<div class="alt">
				Already have an account? <a href="{{ route('login') }}">Sign in!</a>
			</div>
</form>
@endsection
