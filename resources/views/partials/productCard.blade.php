<li class='single-product-info-container' data-id='{{$product->id_context}}'>
    <a href={{"/product/$product->id_product"}}><img src={{ asset("img/product$product->id_product.jpg") }} alt=''></a>
    <div class='single-product-info-text'>
        <div class='row'>
            <div class='col-6'>
            <a href={{"/product/$product->id_product"}}><span class='title'>{{$product->name}}</span></a>
            </div>
            <div class='col-6 state'>
                <a href='#' class='delete'><i class='fas fa-trash remove'></i></a>
            </div>
        </div>
        <div>
            @showRating($product->rating)
        </div>
        <span class='oldprice'></span>
        <span class='price float-right'>{{$product->price}} €</span>
    </div>
</li>