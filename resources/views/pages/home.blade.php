@extends('layouts.app')

@section('content')
<nav class="navbar navbar-expand-lg navbar-light lightgrey bg-light dept-navbar">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="category.html" class="dept-navbar-item">
						<span>
							Books
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="category.html" class="dept-navbar-item">
						<span>
							Movies
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="category.html" class="dept-navbar-item">
						<span>
							Games
						</span>
					</a>
				</li>
				<li class="nav-item">
					<a href="category.html" class="dept-navbar-item">
						<span>
							Clothes
						</span>
					</a>
				</li>
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
				<a href="category.html" class="seemore-block">See more</a>
				<br style="clear:both" />
				<div class="row imagetiles">

					@foreach ($topProducts as $topProduct)
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
					<a href="product/{{$topProduct->id_product}}"><img src={{ asset("img/product$topProduct->id_product.jpg") }}
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
							<a href="category.html"><img src="http://www.drawingforall.net/wp-content/uploads/2018/01/4-how-to-draw-a-closed-book.jpg"
								 class="img-responsive imgSizing"></a>
						</div>
					</div>
					<p class="description mt-4">
						Books are a uniquely portable magic. Checkout the best stories we have for you!
					</p>
					<a href="category.html" class="seemore-ind">See more</a>
				</div>

				<div class="single-product-block">
					<span class="product-block-title">PC Master Race</span>
					<br style="clear:both" />
					<div class="imagetiles">
						<div class="productSize">
							<a href="category.html"> <img src="https://comps.canstockphoto.com/personal-pc-hand-draw-clip-art-vector_csp8551936.jpg"
								 class="img-responsive imgSizing"> </a>
						</div>
					</div>
					<p class="description mt-4">
						Want to flex on console plebs? Find the best PC gear here!
					</p>
					<a href="category.html" class="seemore-ind">See more</a>
				</div>


				<div class="single-product-block">
					<span class="product-block-title">90's Throwback</span>
					<br style="clear:both" />
					<div class="imagetiles">
						<div class="productSize">
							<a href="category.html"><img src="https://www.cuinsight.com/wp-content/uploads/2017/09/bigstock-193075030.jpg"
								 class="img-responsive imgSizing" style="width:100%;"></a>
						</div>
					</div>
					<p class="description mt-4">
						Want to go back to the best time in your life? Try some of our vintage items!
					</p>
					<a href="category.html" class="seemore-ind">See more</a>
				</div>
			</div>

			<div class="product-block">
					<span class="product-block-title">Beauty best sellers</span>
					<a href="category.html" class="seemore-block">See more</a>
					<br style="clear:both" />
					<div class="row imagetiles">
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
							<a href="product.html"><img src=https://ybskin.com/media/catalog/product/y/o/youngblood_lips_lipstick_casablanca_lg1_1.jpg
								 class="img-responsive imgSizing"></a>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
							<a href="product.html"><img src=http://cdn.shopify.com/s/files/1/0023/9648/7716/products/12d6aeacb2d66c1734d1f1ec0633aa79_1024x1024.jpg?v=1544059212
								 class="img-responsive imgSizing"></a>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
							<a href="product.html"><img src=https://scontent.fopo1-1.fna.fbcdn.net/v/t1.0-9/13177472_10204777009384826_2295654131383211125_n.jpg?_nc_cat=105&_nc_ht=scontent.fopo1-1.fna&oh=c1ea48d863b5ef171334b479a8d11d58&oe=5D0A18EB
								 class="img-responsive imgSizing"></a>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
							<a href="product.html"><img src=https://www.sephora.com/productimages/sku/s1925965-main-hero.jpg class="img-responsive imgSizing"></a>
						</div>
					</div>
				</div>

			<div class="product-block">
				<span class="product-block-title">Watches</span>
				<a href="category.html" class="seemore-block">See more</a>
				<br style="clear:both" />
				<div class="row imagetiles">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
						<a href="product.html"><img src=https://n.nordstrommedia.com/ImageGallery/store/product/Zoom/4/_101387464.jpg?h=365&w=240&dpr=2&quality=45&fit=fill&fm=jpg
							 class="img-responsive imgSizing"></a>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
						<a href="product.html"><img src=https://ethos-cdn1.ethoswatches.com/pub/media/catalog/product/cache/749a04adc68de020ef4323397bb5eac7/d/a/daniel-wellington-classic-dw00100277.jpg
							 class="img-responsive imgSizing">></a>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
						<a href="product.html"><img src=http://cdn.shopify.com/s/files/1/0194/3447/products/Learner_L_plate_sticker_grande.png?v=1443425587
							 class="img-responsive imgSizing">></a>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 productSize">
						<a href="product.html"><img src=https://ethos-cdn1.ethoswatches.com/pub/media/catalog/product/cache/749a04adc68de020ef4323397bb5eac7/o/m/omega-de-ville-424-10-40-20-02-003.jpg
							 class="img-responsive imgSizing">></a>
					</div>
				</div>
			</div>

		</div> <!-- container -->
@endsection
