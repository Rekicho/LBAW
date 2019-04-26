<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

use App\User;
use App\BillingInformation;
use App\WishList;
use App\Purchase;

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

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $is_enabled = $request->input('is_enabled');
        $old_password = $request->input('old_password');
        $email = $request->input('email');
        // TODO: $this->authorize('update', $user);

        if ($is_enabled != null) {
            $user->is_enabled = $is_enabled === 'true' ? true : false;
        } else if ($email != null) {
            $this->validate($request, [
              'email' => 'required|string|email|max:255|unique:users',
          ]);

            $user->email = $email;
        } else {

          Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ])->validate();

          if(!Hash::check($old_password, $user->password)){
            return response()->json(['password' => 'Old password doesn\'t match']); // Status code here
          }

            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return $user;
    }

    public function showProfile()
    {
        $user = Auth::user();
        $wishlist = WishList::wishlist($user->id);
        $billingInfo = BillingInformation::billingInformation($user->id);
        $purchaseHistory = User::purchaseHistory($user->id);

        return view('pages.profile', ['user' => $user, 'wishlist'=> $wishlist, 'billingInfo' => $billingInfo, 'purchaseHistory' => $purchaseHistory]);
    }
}
