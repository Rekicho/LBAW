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
<div class="row mt-5 ml-3">
<div class="col-md-12">
    <h2>Total: <br class="br-mobile d-flex" />
        <span class="oldprice">414,95€</span>
        <span class="price">24,14€</span>
    </h2>
    <br style="clear:both">
    <button type="button" class="btn btn-primary checkout-btn" onclick="location.href='checkout1.html'" ><i
        class="fas fa-money-bill"></i> Checkout</button>
    </div>
</div>

</div>

</div>
@endsection