@extends('layouts.app')

@section('title', 'Sign up')

@section('css')
<link href="{{ asset('css/auth.css') }}" rel="stylesheet">
@endsection

@section('content')
<form class="form-signin my-5 text-center" method="POST" action="{{ route('register') }}">
    {{ csrf_field() }}

    <i class="fas fa-question-circle help-icon"  data-toggle="modal" data-target="#exampleModal"></i>   


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
            <a href="{{ url('login/facebook') }}" class="btn btn-med btn-facebook btn-block">
                <i class="fab fa-facebook-square"></i> Sign in with Facebook
            </a>
			<div class="alt">
				Already have an account? <a href="{{ route('login') }}">Sign in!</a>
			</div>
</form>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{ $text->description }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! $text->help_text !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> 
            </div>
            </div>
        </div>
    </div>
@endsection
