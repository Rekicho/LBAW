@extends('layouts.app')

@section('content')
<div class="mt-3 container">
    <div class="product-right-block mobile-only">
        <span class="product-title float-left">Rubber Duck</span>
        <div class="float-right product-rating">
            <span class="n-ratings mr-1">629</span>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="far fa-star"></i>
            <i class="far fa-star"></i>
        </div>
        <br style="clear:both">
        <hr>
    </div>
    <div class="img-frame">
        <img src="https://images-na.ssl-images-amazon.com/images/I/71XnVF8HL4L._SL1500_.jpg" alt="">
    </div>
    <div class="product-info">
        <div class="product-right-block desktop-only">
            <span class="product-title float-left">{{$product->prodname}}</span>
            <div class="float-right product-rating">
                <span class="n-ratings mr-1">{{$product->numratings}}</span>
                @for($i=0; $i<$product->rating; $i++)
                    <i class="fas fa-star"></i>
                @endfor
                @for($i=0; $i< 5 - $product->rating; $i++)
                <i class="far fa-star"></i>
                @endfor
            </div>
            <br style="clear:both">
            <hr>
        </div>
    <p class="p-3 mb-0">{{$product->description}}</p>
        <div class="p-3">
            <button type="button" class="btn btn-secondary float-left">Review</button>
            <button type="button" class="btn btn-primary float-right" onclick="$('.cart').attr('data-count',parseInt($('.cart').attr('data-count'))+parseInt($('.product-quantity').val()));">
                Add to cart
            </button>
            <span class="price float-right">{{$product->price}}€</span>
            <label class="float-right quantity">
                <input type="number" class="product-quantity" value="1"> x
            </label>
        </div>
    </div>
    <br style="clear:both">
    <div class="reviews bg-light">
        <article class="review">
            <a href="profile.html"><span class="username">António Manuel</span></a>
            <i class="fas fa-flag" data-toggle="modal" data-target="#reportModal"></i>
            <div class="float-right product-rating">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="far fa-star"></i>
                <i class="far fa-star"></i>
            </div>
            <p>O pato ajudou me a resolver o maior bug com que ja me deparei na minha vida profissional. 526 000 linhas de código feitas inúteis por um "= vs ==".</p>
            <span class="date">24/04/2019</span>
        </article>

        <hr>

        <article class="review">
            <a href="profile.html"><span class="username">Dona Maria</span></a>
            <i class="fas fa-flag"></i>
            <div class="float-right product-rating">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="far fa-star"></i>
                <i class="far fa-star"></i>
            </div>
            <p>Nunca vi um pato tão sábio, o seu silêncio esclareceu me não só as minhas dúvidas no trabalho como me ajudou a conhecer me a mim mesmo e a encontrar paz interior.</p>
            <span class="date">24/04/2019</span>
        </article>
    </div>
</div>
@endsection