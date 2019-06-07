@extends('layouts.app')

@section('title', 'Page not found')

@section('css')
<link href="{{ asset('css/errors.css') }}" rel="stylesheet"> 
@endsection

@section('content')
    <div class="container error-div">
        <h3>{{$error}} </h3>        
        <a href="{{ url()->previous() }}">Click here to go back.</a>
    </div>
@endsection