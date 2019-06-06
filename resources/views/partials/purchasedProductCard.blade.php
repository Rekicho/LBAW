<div class="single-product-info-container">
    <a href="/product/{{$product->id_product}}"><img src="{{ asset("img/product$product->id_product.png") }}"
         alt=""></a>
    <div class="single-product-info-text">
        <div class="row">
            <div class="col-6">
            <a href="/product/{{$product->id_product}}"><span class="title">{{$product->name}}</span></a>
            </div>
        </div>
        {{-- {{$product->date}} --}}
        <div>
            <a role="button" class="btn btn-secondary" href="/product/{{$product->id_product}}">Review</a>
        </div>
        <span class="price float-right">{{$product->quantity}} x {{$product->price}}€</span>
    </div>
</div>