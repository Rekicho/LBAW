@extends('layouts.app')

@section('css')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection

@section('title', 'Contact')


@section('content')

<div class="container mb-5">
    <h1 class="my-3 space-title">Contact Us</h1>
    <div class="row pb-3">
        <div class="col-md-8">
            <iframe src="https://maps.google.com/maps?q=est%C3%A1dio%20do%20drag%C3%A3o&t=k&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
        </div>
        <!-- Contact Details Column -->
        <div class="col-md-4">
            <h3>Contact details</h3>
            <p>
                Estádio do Dragão<br>Via Futebol Clube do Porto<br>4350-415 Porto<br>
            </p>
            <p><i class="fa fa-envelope nav-icon"></i>
                <abbr title="Email"></abbr> <a href="mailto:up201604145@fe.up.pt">Bruno Sousa</a>
            </p>
            <p><i class="fa fa-envelope nav-icon"></i>
                <abbr title="Email"></abbr> <a href="mailto:up201603846@fe.up.pt">Pedro Fernandes</a>
            </p>
            <p><i class="fa fa-envelope nav-icon"></i>
                <abbr title="Email"></abbr> <a href="mailto:up201604470@fe.up.pt">Pedro Silva</a>
            </p>
            <p><i class="fa fa-envelope nav-icon"></i>
                <abbr title="Email"></abbr> <a href="mailto:up201605422@fe.up.pt">Simão Silva</a>
            </p>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="https://www.facebook.com/FCPorto/"><i class="fab fa-facebook fa-2x nav-icon"></i></a>
                </li>
                <li class="list-inline-item">
                    <a href="https://twitter.com/FCPorto"><i class="fab fa-twitter fa-2x nav-icon"></i></a>
                </li>
                <li class="list-inline-item">
                    <a href="https://www.instagram.com/fcporto/"><i class="fab fa-instagram fa-2x nav-icon"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection