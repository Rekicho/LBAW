<div class="single-product-info-container">
    	@if (file_exists(public_path("storage/img/product$product->id_product.png")))
        <a href="/product/{{$product->id_product}}"><img src="{{ asset("storage/img/product$product->id_product.png") }}"
            alt=""></a>
        @else
        <a href="/product/{{$product->id_product}}"><img alt="{{$product->name}}" src={{ asset("storage/img/product-image-placeholder.jpg") }}
            class="img-responsive imgSizing"></a>

        @endif


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