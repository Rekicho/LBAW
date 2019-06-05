<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Cart;

class BaseController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$user = Auth::user();

			if(Auth::check())
				$cartProducts = Cart::getProductsFromCart($user->id);
	
			else $cartProducts = [];
	
			view()->share('cartProducts', $cartProducts);

			return $next($request);
		});
	}
}