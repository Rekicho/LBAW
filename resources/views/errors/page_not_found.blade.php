@extends('layouts.app')

@section('title', 'Page not found')

@section('css')
<link href="{{ asset('css/errors.css') }}" rel="stylesheet"> 
@endsection

@section('content')
    
    <p>{{$error}} </p>

    <a href="{{ url()->previous() }}">Click here to go back.</a>
@endsection