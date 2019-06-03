@extends('layouts.app')

@section('css')
<link href="{{ asset('css/search.css') }}" rel="stylesheet">
<link href="{{ asset('css/profile.css') }}" rel="stylesheet">
<link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mb-5">
		<h1 class="mt-3 mb-3 text-center"><i class="fas fa-credit-card p-3 nav-icon"></i>Payment</h1>
		<form class="form-edit-billing light-main-color-bg px-3 checkout-theme" action="checkout2.html">
			<div class="my-3">
				<span class="float-right">1/3</span>
				<h3>Billing Information</h3>
			</div>
			<div class="form-group">
				<label for="fullName" class="font-weight-bold">Full Name</label>
				<input type="text" id="fullName" class="form-control" placeholder="Full Name" value="Bruno Miguel da Silva Barbosa de Sousa" />
			</div>
			<div class="form-group">
				<label for="address" class="font-weight-bold">Address</label>
				<input type="text" id="address" class="form-control" placeholder="Address" value="Rua City Nº 123" />
			</div>
			<div class="form-group">
				<label for="city" class="font-weight-bold">City</label>
				<input type="text" id="city" class="form-control" placeholder="City" value="Paredes" />
			</div>
			<div class="form-group">
				<label for="state" class="font-weight-bold">State</label>
				<input type="text" id="state" class="form-control" placeholder="State" value="Porto" />
			</div>
			<div class="form-group">
				<label for="zip" class="font-weight-bold">Zip Code</label>
				<input type="text" id="zip" class="form-control" placeholder="zip" value="4444-444" />
			</div>
			<button class="btn btn-lg btn-primary my-2 float-right" type="submit">
				Next
			</button>
		</form>
	</div>
@endsection