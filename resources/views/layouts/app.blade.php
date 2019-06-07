<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		@yield('open-graph')


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title')	- {{ config('app.name', 'Laravel') }}</title>

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
   
    <script>
        // Fix for Firefox autofocus CSS bug
        // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
    </script>
    <script src={{ asset('js/app.js') }} defer>
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
						<a class="nav-link {{ Request::is('faq') ? 'active' : '' }}" href="/faq">FAQ</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ Request::is('about') ? 'active' : '' }}" href="/about">ABOUT</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="/contact">CONTACT</a>
					</li>
					@if(Auth::check() && Auth::user()->is_admin)
					<li class="nav-item">
						<a class="nav-link" href="/back-office/admin">BACK-OFFICE</a>
					</li>
					@elseif(Auth::check() && Auth::user()->is_staff_member)
					<li class="nav-item">
						<a class="nav-link" href="/back-office/moderator">BACK-OFFICE</a>
					</li>
					@endif
				</ul>
				<form class="form-inline my-2 my-lg-0" action="/search">
					<input class="form-control mr-sm-2" type="search" name="search" placeholder="Search" aria-label="Search" required>
					<button class="btn pl-0 my-2 my-sm-0" type="submit"><i class="fas fa-search nav-icon"></i></button>
				</form>
				@if(!Auth::check())
				<a href="{{ route('login') }}"><i class="fas fa-sign-in-alt p-3 nav-icon"></i></a>
				@else
				<a href="/profile"><i class="fas fa-user p-3 nav-icon"></i></a>
				<div class="fa-stack has-badge dropdown show" data-count="{{ count($notifications) }}">
					@if(count($notifications) != 0)
					<a class="dropdown-toggle fas fa-bell fa-stack-1x nav-icon" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
					@else
					<i class="fas fa-bell fa-stack-1x nav-icon" aria-haspopup="true" aria-expanded="false"></i>
					@endif

					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
						
						@foreach($notifications as $notification)
							<div class="dropdown-item" href="#">A product on your wishlist is on sale! <a href="/product/{{$notification->data["product_id"]}}">Check it out</a></div>
						@endforeach
					</div>
				</div>
				<div class="cart fa-stack has-badge dropdown show" data-count="{{ count($cartProducts) }}">
					<a class="fa fa-shopping-cart fa-stack-1x nav-icon dropdown-toggle desktop-only" id="cartDropdown" href="#" data-toggle="dropdown"></a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="cartDropdown">
						<ul> @each('partials/productCard', $cartProducts, 'product') </ul>
						<div class="container">
							<div class="row justify-content-center">
								<a class="fa fa-shopping-cart nav-icon" href="/cart"> Cart</a>
							</div>
						</div>
					</div>
					<a class="fa fa-shopping-cart fa-stack-1x nav-icon mobile-only" href="/cart"></a>
				</div>
			@endif
		</div>
		</nav>
    <div id="content">
        @yield('content')
		</div>
	</div>
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
						@for($i = 0; $i < 4; $i++)
						<li>
							<a href="/category/{{$footerCategories[$i]->id}}">{{$footerCategories[$i]->name}}</a>
						</li>
						@endfor
					</ul>
				</div>
				<div class="col-md-3 mb-md-0 mb-3 position-relative">
					<ul class="list-unstyled push-down footer-ul">
							@for($i = 4; $i < 8; $i++)
							<li>
								<a href="/category/{{$footerCategories[$i]->id}}">{{$footerCategories[$i]->name}}</a>
							</li>
							@endfor
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
