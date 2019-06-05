@extends('layouts.app')

@section('css')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endsection

@section('title', 'Homepage')


@section('content')
<nav class="navbar navbar-expand-lg navbar-light lightgrey bg-light dept-navbar">
			<ul class="navbar-nav">
				@foreach($topCategories as $category)
				<li class="nav-item">
				<a href="/category/{{$category->id}}" class="dept-navbar-item">
							<span>
								{{$category->name}}
							</span>
						</a>
					</li>
				@endforeach
			</ul>
		</nav>
		<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
			<div class="carousel-inner">
				<div class="carousel-item active">
					<img class="d-block w-100" src="https://d1m6qo1ndegqmm.cloudfront.net/discount-offers.png" alt="First slide">
				</div>
				<div class="carousel-item">
					<img class="d-block w-100" src="https://d1m6qo1ndegqmm.cloudfront.net/discount-offers.png" alt="Second slide">
				</div>
				<div class="carousel-item">
					<img class="d-block w-100" src="https://d1m6qo1ndegqmm.cloudfront.net/discount-offers.png" alt="Third slide">
				</div>
			</div>
			<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>

		<div class="container">


			<!-- 
				==================================================================================
			-->

			<div class="product-block">
				<span class="product-block-title">Top Products</span>
				<br style="clear:both" />
				<div class="row imagetiles">

					@foreach ($topProducts as $topProduct)
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
					<a href="product/{{$topProduct->id_product}}"><img alt="{{$topProduct->name}}" src={{ asset("img/product$topProduct->id_product.jpg") }}
						class="img-responsive imgSizing"></a>
						</div>
					@endforeach

				</div>
			</div>

			<div class="single-product-block-container">
				<div class="single-product-block">
					<span class="product-block-title">Books</span>
					<br style="clear:both" />
					<div class="imagetiles">
						<div class="productSize">
							<a href="category/8"><img src="http://www.drawingforall.net/wp-content/uploads/2018/01/4-how-to-draw-a-closed-book.jpg"
								 class="img-responsive imgSizing"></a>
						</div>
					</div>
					<p class="description mt-4">
						Books are a uniquely portable magic. Checkout the best stories we have for you!
					</p>
					<a href="category/8" class="seemore-ind">See more</a>
				</div>

				<div class="single-product-block">
					<span class="product-block-title">PC Master Race</span>
					<br style="clear:both" />
					<div class="imagetiles">
						<div class="productSize">
							<a href="category/3"> <img src="https://comps.canstockphoto.com/personal-pc-hand-draw-clip-art-vector_csp8551936.jpg"
								 class="img-responsive imgSizing"> </a>
						</div>
					</div>
					<p class="description mt-4">
						Want to flex on console plebs? Find the best PC gear here!
					</p>
					<a href="category/3" class="seemore-ind">See more</a>
				</div>


				<div class="single-product-block">
					<span class="product-block-title">90's Throwback</span>
					<br style="clear:both" />
					<div class="imagetiles">
						<div class="productSize">
							<a href="category/9"><img src="https://www.cuinsight.com/wp-content/uploads/2017/09/bigstock-193075030.jpg"
								 class="img-responsive imgSizing" style="width:100%;"></a>
						</div>
					</div>
					<p class="description mt-4">
						Want to go back to the best time in your life? Try some of our vintage items!
					</p>
					<a href="category/9" class="seemore-ind">See more</a>
				</div>
			</div>

			<div class="product-block">
					<span class="product-block-title">{{$featuredCategory->name}} best sellers</span>
					<a href="category/2" class="seemore-block">See more</a>
					<br style="clear:both" />

					<div class="row imagetiles">
						@foreach ($featuredCategoryProducts as $product)
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
						<a href="product/{{$product->id_product}}"><img alt="{{$product->name}}" src={{ asset("img/product$product->id_product.jpg") }}
							class="img-responsive imgSizing"></a>
							</div>
						@endforeach
					</div>
				</div>

			<div class="product-block">
				<span class="product-block-title">Watches</span>
				<a href="category/1" class="seemore-block">See more</a>
				<br style="clear:both" />
				<div class="row imagetiles">
					@foreach($watches as $product)
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
					<a href="product/{{$product->id_product}}"><img alt="{{$product->name}}" src={{ asset("img/product$product->id_product.jpg") }}
								class="img-responsive imgSizing"></a>
						</div>
					@endforeach
				</div>
			</div>

		</div> <!-- container -->
@endsection
