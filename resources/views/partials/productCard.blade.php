<li class="single-product-info-container">
    <a href={{"product$product->id"}}><img src={{ asset("img/product$product->id.jpg") }} alt=""></a>
    <div class="single-product-info-text">
        <div class="row">
            <div class="col-6">
            <a href="product.html"><span class="title">{{$product->prodname}}</span></a>
            </div>
            <div class="col-6 state">
                <a href="profile.html"><i class="fas fa-trash remove"></i></a>
            </div>
        </div>
        {{$product->description}}
        <div>
            @showRating($product->rating)
        </div>
        <span class="oldprice"></span>
        <span class="price float-right">{{$product->price}}</span>
    </div>
</li>