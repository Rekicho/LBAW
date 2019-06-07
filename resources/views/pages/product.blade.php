@extends('layouts.app')

@section('title', $product->prodname)

@section('open-graph')
    <meta property="og:title" content="{{$product->prodname}}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="http://lbaw1844.lbaw-prod.fe.up.pt/product/{{$product->id}}/" />
@endsection

@section('css')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
<link href="{{ asset('css/product.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="mt-3 container">
    <div class="product-right-block mobile-only">
        <span class="product-title float-left">{{$product->prodname}}</span>
        <div class="float-right product-rating">
            <span class="n-ratings mr-1">{{$reviewsStats->numratings}}</span>
            @showRating(floor($reviewsStats->rating))
        </div>
        <br style="clear:both">
        <hr>
    </div>
    <div class="img-frame">
            @if (file_exists(public_path("storage/img/product$product->id.png")))
            <img src="{{ asset("storage/img/product$product->id.png") }}" alt=""/>
            @else
            <img src="{{ asset("storage/img/product-image-placeholder.jpg") }}" alt="">
            @endif
    </div>
    <div class="product-info">
        <div class="product-right-block desktop-only">
            <span class="product-title float-left">{{$product->prodname}}</span>
            <div class="float-right product-rating">
                <span class="n-ratings mr-1">{{$reviewsStats->numratings}}</span>

                @showRating(floor($reviewsStats->rating))

            </div>
            <br style="clear:both">
            <hr>
        </div>
    <p class="p-3 mb-0">{{$product->description}}</p>
        <div class="p-3">
            
            
            <form id="updateCart">
                <button type="submit" class="btn btn-primary float-right" onclick="$('.cart').attr('data-count',parseInt($('.cart').attr('data-count'))+1);">
                    Add to cart
                </button>
                <input type="hidden" class="d-none    " name="id_product" value={{$product->id}}>
                <span class="price float-right">{{$product->price}}€</span>
                <label class="float-right quantity">
                    <input type="number" name="quantity" class="product-quantity" value="1"> <span class="times">x</span>
                </label>
                
            </form>
            <br style="clear:both">
            <form id="updateWishlist">
               
                <input type="hidden" class="d-none    " name="id_product" value={{$product->id}}>
            @if($wishlist != null)
                <input type="hidden" class="d-none    " name="id" value={{$wishlist->id}}>
                <button type="submit" class="btn btn-primary float-right">
                    Remove from wishlist <i class="fas fa-bookmark"></i>
                </button>
            @else
                <button type="submit" class="btn btn-primary float-right">
                    Add to wishlist <i class="fas fa-bookmark"></i>
                </button>
            @endif
        </form>     
            <br style="clear:both">
            @if($canReview)
                <button type="button" class="btn btn-secondary reviewBtn" data-toggle="modal" data-target="#reviewModal">Review</button>
            @else
                <button type="button" class="btn btn-secondary reviewBtn" data-toggle="modal" data-target="#reviewModal" disabled>Review</button>
            @endif
        </div>
    </div>
    <br style="clear:both">
    <div class="reviews bg-light">
        @if(count($reviews) != 0)
        @each('partials.review', $reviews, 'review')
        @else
        <div style="text-align: center">No reviews found!</div>
        @endif
    </div>
    	<!-- MODALS -->
	<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Report</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form>

						<div class="radio">
							<label><input type="radio" name="optradio" checked>Report User</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="optradio">Report Review</label>
						</div>

						<div class="form-group">
							<label for="message-text" class="col-form-label">Reason:</label>
							<textarea class="form-control" id="message-text"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Send</button>
				</div>
			</div>
		</div>
    </div>
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Review Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addReview">
                    <input type="hidden" name="id_product" value={{$product->id}}>
                    <div class="review-rating">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <textarea class="review-text-area" id="comment"></textarea>
                    <input type="hidden" name="rating" value="0">
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addReview" class="btn btn-primary">Review</button>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection