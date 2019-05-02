<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Cart;

class CartController extends Controller
{
   public function show(){
        $user = Auth::user();
        $products = Cart::getProductsFromCart($user->id);

        return view('pages.cart', ['products' => $products]);
   }

   public function create(Request $request)
   {
     $cart = new Cart();

     // $this->authorize('create', $user);

     $cart->id_product = intval($request->input('id_product'));
     $cart->id_client = Auth::user()->id;
     $cart->quantity = intval($request->input('quantity'));

     $cart->save();

     return $cart;
   }

   public function update(Request $request){
      $cartEntry = Cart::find($id);

      $cart->quantity = intval($request->input('quantity'));

      $cart->save();

      return $cart;
   }

   public function delete(Request $request, $id){
       $cartEntry = Cart::find($id);

      //  $this->authorize('delete', $card);
       $cartEntry->delete();
 
       return $cartEntry;  
   }
}
