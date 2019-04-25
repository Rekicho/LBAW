<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\BillingInformation;
use App\WishList;

class UserController extends Controller
{
    /**
     * Creates a new card.
     *
     * @return Card The card created.
     */
    public function create(Request $request)
    {
      $user = new User();

      // $this->authorize('create', $user);

      $user->username = $request->input('username');
      $user->password = bcrypt($request->input('password'));
      $user->is_staff_member = true;
      $user->is_enabled = true;
      
      $user->save();

      return $user;
    }

    public function update(Request $request, $id){
      $user = User::find($id);

      // TODO: $this->authorize('update', $user);

      $user->is_enabled = $request->input('is_enabled') === 'true' ? true : false;
      $user->save();

      return $user;
    }

    public function showProfile(){
      $user = Auth::user();
      $wishlist = WishList::wishlist($user->id);
      $billingInfo = BillingInformation::billingInformation($user->id);
      $purchaseHistory = User::purchaseHistory($user->id);

      return view('pages.profile', ['user' => $user, 'wishlist'=> $wishlist, 'billingInfo' => $billingInfo, 'purchaseHistory' => $purchaseHistory]);
    }
}
