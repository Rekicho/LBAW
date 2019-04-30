@extends('layouts.app')

@section('css')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="container">
    <h1 class="my-3">FAQ</h1>
    <div id="accordion" class="pb-3">
        <div class="card mb-1">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false"
                        aria-controls="collapseOne">
                        How can I buy a product?
                    </button>
                </h5>
            </div>

            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <p>To buy a product, just click on the "Add to Cart" button next to the product.</p>
                    <p>After adding all products to the cart, you can proceed to checkout.</p>
                </div>
            </div>
        </div>
        <div class="card mb-1">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                        aria-controls="collapseTwo">
                        How can I can checkout my cart?
                    </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                <div class="card-body">
                    <p>After adding all products to your cart, you can checkout to get your products.</p>
                    <p>You will need to be logged in to checkout.</p>
                    <p>Click the cart button on top of every page and proceed to checkout.</p>
                    <p>After that you will be redirected to a page to choose your payment method.</p>
                </div>
            </div>
        </div>
        <div class="card mb-1">
            <div class="card-header" id="headingThree">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false"
                        aria-controls="collapseThree" style="white-space: normal;">
                        What are the payment methods available?
                    </button>
                </h5>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                <div class="card-body">
                    <p class="max-text-width">You can pay using you paypal account or opt for offline payment, where we will give
                        you eitheir our bank
                        account information so you can transfer the money.</p>
                    <p class="max-text-width">After that you will have to contact us so we can confirm your payment to ship your
                        product.</p>
                </div>
            </div>
        </div>
        <div class="card mb-1">
            <div class="card-header" id="headingFour">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false"
                        aria-controls="collapseFour" style="white-space: normal;">
                        How long until I get the product I have bought?
                    </button>
                </h5>
            </div>

            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                <div class="card-body">
                    <p class="max-text-width">The time till you get your product depends on your location and the time it takes us
                        to confirm your payment.</p>
                    <p class="max-text-width">We try to be as quick as possible, but the time also depends on our parteners that
                        ship the product.</p>
                    <p class="max-text-width">You can see the shipping state and an estimative on how long it will take to reach you
                        on your purchase
                        states.</p>
                </div>
            </div>
        </div>
    </div>
</div> <!-- wrapper -->
@endsection