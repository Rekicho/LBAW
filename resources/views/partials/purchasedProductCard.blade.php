<div class="single-product-info-container">
    <a href="/product/{{$product->id_product}}"><img src="{{ asset("img/product$product->id_product.jpg") }}"
         alt=""></a>
    <div class="single-product-info-text">
        <div class="row">
            <div class="col-6">
            <a href="/product/{{$product->id_product}}"><span class="title">{{$product->name}}</span></a>
            </div>
        </div>
        {{-- {{$product->date}} --}}
        <div>
            <a href="/product{{$product->id_product}}"><button type="button" class="btn btn-secondary">Review</button></a>
        </div>
        <span class="price float-right">{{$product->quantity}} x {{$product->price}}€</span>
    </div>
</div>