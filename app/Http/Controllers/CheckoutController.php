<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\BillingInformation;
use App\Cart;
use App\Purchase;

class CheckoutController extends BaseController
{
   public function show() {
		$user = Auth::user();
		$billingInfo = BillingInformation::billingInformation($user->id);
		if(count($billingInfo) !== 0)
			$billingInfo = array($billingInfo[count($billingInfo)-1]);
		$products = Cart::getProductsFromCart($user->id);
		$total = Cart::getCartTotal($products);

		if(count($products) == 0)
			return redirect()->to('/cart');

        return view('pages.checkout', ['billingInfo' => $billingInfo, 'products' => $products, 'total' => $total]);
   }

   public function buy() {
		$user = Auth::user();
		$billingInfo = BillingInformation::billingInformation($user->id);
		$billingInfo = $billingInfo[count($billingInfo)-1];
		Purchase::purchase($user->id, $billingInfo->id);

		return redirect()->to('/profile');
   }
}