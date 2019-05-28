@extends('layouts.app')

@section('css')
<link href="{{ asset('css/errors.css') }}" rel="stylesheet"> 
@endsection

@section('content')
    
    <p>Ooops! That product does not exist!</p>
    <p>{{$errorInfo}} </p>

    <a href="{{ url()->previous() }}">Click here to go back.</a>
@endsection