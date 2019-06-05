@extends('layouts.app')

@section('css')
<link href="{{ asset('css/cart.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container mb-5">
    <h1 class="mt-3 mb-3 text-center"><i class="fas fa-shopping-cart p-3 nav-icon"></i>Cart</h1>
    <ul>
        @each('partials/productCard', $products, 'product')
</ul>
<div class="final row mt-5 ml-3">
	<div class="col-md-6 total">
		<h2>Total: <span class="price">{{ $total }}€</span> </h2>
	</div>
    <div class="col-md-6 checkout">
		<button type="button" class="btn btn-primary checkout-btn"><i
		class="fas fa-money-bill"></i> Checkout</button>
	</div>
</div>

</div>

</div>
@endsection