<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

use App\Cart;
use App\Category;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $notifications = [];
            $cartProducts = [];
            
            if (Auth::check()) {
                $notifications = $user->notifications;
                $cartProducts = Cart::getProductsFromCart($user->id);
            }
    
            $footerCategories = Category::getFooterCategories();

            view()->share('cartProducts', $cartProducts);
            view()->share('notifications', $notifications);
            view()->share('footerCategories', $footerCategories);

            return $next($request);
        });
    }
}
