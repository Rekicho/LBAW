<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\BillingInformation;
use App\Cart;

class CheckoutController extends BaseController
{
   public function show(){
		$user = Auth::user();
		$billingInfo = BillingInformation::billingInformation($user->id);
		$products = Cart::getProductsFromCart($user->id);
		$total = Cart::getCartTotal($products);

        return view('pages.checkout', ['billingInfo' => $billingInfo, 'products' => $products, 'total' => $total]);
   }
}