<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\BillingInformation;

class CheckoutController extends BaseController
{
   public function show(){
		$user = Auth::user();
		$billingInfo = BillingInformation::billingInformation($user->id);

        return view('pages.checkout', ['billingInfo' => $billingInfo]);
   }
}