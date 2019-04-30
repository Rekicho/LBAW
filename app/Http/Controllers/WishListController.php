<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\BillingInformation;
use App\WishList;
use App\Purchase;

class WishListController extends Controller
{
    /**
     * Creates a new card.
     *
     * @return Card The card created.
     */
    public function create(Request $request)
    {
      $wishlist = new WishList();

      // $this->authorize('create', $user);

      $wishlist->id_product = intval($request->input('id_product'));
      $wishlist->id_client = Auth::user()->id;
      
      $wishlist->save();

      return $wishlist;
    }

    public function delete(Request $request, $id){
        $wishlistEntry = WishList::find($id);

       //  $this->authorize('delete', $card);
        $wishlistEntry->delete();
  
        return $wishlistEntry;  
    }
}
