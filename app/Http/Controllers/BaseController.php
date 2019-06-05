<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Cart;
use Illuminate\Support\Facades\View;

{
	public function __construct()
class BaseController extends Controller
	{
		$this->middleware(function ($request, $next) {
			$user = Auth::user();
            $notifications = [];
			if(Auth::check()){
                $notifications = $user->notifications;
				$cartProducts = Cart::getProductsFromCart($user->id);
            }
	
			else $cartProducts = [];
	
			view()->share('cartProducts', $cartProducts);
            view()->share('notifications', $notifications);

			return $next($request);
		});
	}
}
