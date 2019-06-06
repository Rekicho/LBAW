@if(isset($product->id_context))
    <li class='single-product-info-container' data-id='{{$product->id_context}}'>
@else
    <li class='single-product-info-container'>
@endif
    <a href={{"/product/$product->id_product"}}><img src={{ asset("img/product$product->id_product.jpg") }} alt=''></a>
    <div class='single-product-info-text'>
        <div class='row'>
            <div class='col-6'>
            <a href={{"/product/$product->id_product"}}><span class='title'>{{$product->name}}</span></a>
            </div>
            @if(isset($product->id_context))
            <div class='col-6 state'>
                <a href='#' class='delete'><i class='fas fa-trash remove'></i></a>
            </div>
            @endif
        </div>
        <div>
            @showRating(floor($product->rating))
		</div>
        <span class='price float-right'>
			@if(@isset($product->quantity))
			{{$product->quantity}} x 
			@endif 
			{{$product->price}} €
		</span>
    </div>
</li>