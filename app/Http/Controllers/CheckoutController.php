<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// use App\Checkout;

class CheckoutController extends BaseController
{
   public function show(){
        $user = Auth::user();

        return view('pages.checkout');
   }
}