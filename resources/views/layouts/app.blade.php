<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
	 crossorigin="anonymous" />
	  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr"
   crossorigin="anonymous" />
		
		@yield('css')

    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/colors.css') }}" rel="stylesheet">

	 
	 <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
	 crossorigin="anonymous"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
	 crossorigin="anonymous"></script>
	  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
	 crossorigin="anonymous"></script>
   
    <script type="text/javascript">
        // Fix for Firefox autofocus CSS bug
        // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
    </script>
    <script type="text/javascript" src={{ asset('js/app.js') }} defer>
</script>
  </head>
  <body class="bg-primary">
	<div class="wrapper">
		<nav class="navbar navbar-expand-lg navbar-dark sticky-top main-color-bg">
			<a class="navbar-brand" href="/"> <span class="sec-color">KEY</span>VALUE</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
			 aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="faq">FAQ</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="about">ABOUT</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="contact">CONTACT</a>
					</li>
				</ul>
				<form class="form-inline my-2 my-lg-0" action="search.html">
					<input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
					<button class="btn pl-0 my-2 my-sm-0" type="submit"><i class="fas fa-search nav-icon"></i></button>
				</form>
				@if(!Auth::check())
				<a href="{{ route('login') }}"><i class="fas fa-sign-in-alt p-3 nav-icon"></i></a>
				@else
				<a href="/profile"><i class="fas fa-user p-3 nav-icon"></i></a>
				<span class="cart fa-stack has-badge dropdown show" data-count="{{ count($cartProducts) }}">
					<a class="fa fa-shopping-cart fa-stack-1x nav-icon dropdown-toggle" id="cartDropdown" href="#" data-toggle="dropdown"></a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="cartDropdown">
						<ul> @each('partials/productCard', $cartProducts, 'product') </ul>
						<a href="/cart">Go to Cart</a>
					</div>
				</span>
				@endif
			</div>
		</nav>
    <section id="content">
        @yield('content')
    </section>
    <footer class="page-footer font-small blue p-4 main-color-bg text-light">
		<div class="container-fluid text-center text-md-left">
			<div class="row">
				<div class="col-md-6 mt-md-0 mt-3">
					<h5 class="text-uppercase">About keyvalue</h5>
					<p>KEYVALUE is a project focused on the development of an information system with a web interface to support an online store to sell a wide variety of products.</p>
				</div>
				<hr class="clearfix w-100 d-md-none pb-3">
				<div class="col-md-3 mb-md-0 mb-3 footer-ul">
					<h5 class="text-uppercase">Categories</h5>
					<ul class="list-unstyled">
						<li>
							<a href="category.html">Balls</a>
						</li>
						<li>
							<a href="category.html">Watches</a>
						</li>
						<li>
							<a href="category.html">Beauty products</a>
						</li>
						<li>
							<a href="category.html">Video Games</a>
						</li>
					</ul>
				</div>
				<div class="col-md-3 mb-md-0 mb-3 position-relative">
					<ul class="list-unstyled push-down footer-ul">
						<li>
							<a href="category.html">Board Games</a>
						</li>
						<li>
							<a href="category.html">Unsold Curry merchandise</a>
						</li>
						<li>
							<a href="category.html">Computers</a>
						</li>
						<li>
							<a href="category.html">Mugs</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="footer-copyright text-center py-3">© 2019 Copyright:
			<a href="/"> KEYVALUE</a>
		</div>
	</footer>
  </body>
</html>
