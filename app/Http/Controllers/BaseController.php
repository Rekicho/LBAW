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
				$cartQuantity = count(Cart::getProductsFromCart($user->id));
	
			else $cartQuantity = -1;
	
			view()->share('cartQuantity', $cartQuantity);

			return $next($request);
		});
	}
}