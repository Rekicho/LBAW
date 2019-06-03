@extends('layouts.app')

@section('css')
<link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="billing">
		<div class="container mb-5">
			<h1 class="mt-3 mb-3 text-center"><i class="far fa-file-alt p-3 nav-icon"></i>Update Shipping & Billing Information</h1>
			@if(count($billingInfo) == 0)
			<form class="form-edit-billing light-main-color-bg px-3 billingInfo">
				<div class="form-group">
					<label for="fullName">Full Name</label>
					<input type="text" name="full_name" class="form-control" placeholder="Full Name" />
				</div>
				<div class="form-group">
					<label for="address">Address</label>
					<input type="text" name="address" class="form-control" placeholder="Address" />
				</div>
				<div class="form-group">
					<label for="city">City</label>
					<input type="text" name="city" class="form-control" placeholder="City" />
				</div>
				<div class="form-group">
					<label for="state">State</label>
					<input type="text" name="state" class="form-control" placeholder="State" />
				</div>
				<div class="form-group">
					<label for="zip">Zip Code</label>
					<input type="text" name="zip_code" id="zip" class="form-control" placeholder="zip" />
				</div>
				<button class="btn btn-lg btn-primary my-2 float-right" type="submit">
					Edit
				</button>
			</form>
			@else
			@each('partials.billingInfo', $billingInfo, 'billingInfo')
			@endif
		</div>
		<div class="container">
			<button class="btn btn-lg btn-primary proceed my-2">
					<i class="fas fa-credit-card p-3 nav-icon"></i> Proceed to Payment
			</button>
		</div>
	</div>
	<div class="container payment mb-5 d-none">
			<h1 class="mt-3 mb-3 text-center"><i class="fas fa-credit-card p-3 nav-icon"></i>Payment</h1>
			<form class="form-edit-billing light-main-color-bg px-3 checkout-theme checkout3" action="#">
				<div class="my-3 text-center">
					<h3>Cart</h3>
				</div>
				<div class="narrow">
					<ul>
						<li style="clear:both">
							<a class="name-item float-left" href="product.html">Bola</a>
							<span class="price-item float-right">6,54€</span>
						</li>
						<li style="clear:both">
							<a class="name-item float-left" href="product.html">Bola</a>
							<span class="price-item float-right">6,54€</span>
						</li>
						<li style="clear:both">
							<a class="name-item float-left" href="product.html">Bola</a>
							<span class="price-item float-right">6,54€</span>
						</li>
						<li style="clear:both">
							<a class="name-item float-left" href="product.html">Bola</a>
							<span class="price-item float-right">6,54€</span>
						</li>
					</ul>
					<hr style="clear:both">
					<div>
						<span class="float-left"> <b>Total</b> </span>
						<span class="price-item float-right">19,62€</span>
					</div>
				</div>
				<br style="clear:both">
		
				<button type="button" class="btn btn-primary float-right m-1"  data-toggle="modal" data-target="#offlinepayment">
					<i class="fas fa-money-bill"></i> Pay
					Offline
				</button>
				<button type="button" class="btn btn-primary float-right m-1" style="background-color:rgb(22, 155, 215) !important">
					<i class="fab fa-paypal paypal-icon"></i> Pay with PayPal
				</button>
			</form>
			<div class="container">
					<button class="btn btn-lg btn-primary proceed go-back">
							<i class="far fa-file-alt p-3 nav-icon"></i> Go back to Billing & Shipping Information
					</button>
			</div>
	</div>
	<div class="modal" id="offlinepayment" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Offline payment</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>We will contact you by email with instructions to proceed with your payment.</p>
					</div>
					<div class="modal-footer">
						<a href="profile.html"> <button type="button" class="btn btn-primary">Proceed with offline payment</button></a>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
	</div>
@endsection