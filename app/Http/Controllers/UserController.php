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
use App\Category;

class UserController extends BaseController
{
    /**
     * Creates a new card.
     *
     * @return Card The card created.
     */
    public function create(Request $request)
    {
        $user = new User();

        $this->authorize('create', $user);

        $validator = \Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors'=> $validator->errors()->all()]);
        }

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

        $type = $request->input('type');

         $this->authorize('update', $user);

        if ($type == 'updateEmail') {
            $email = $request->input('email');

            $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

            if ($validator->fails()) {
                return response()->json(['errors'=> $validator->errors()->all()]);
            }

            $user->email = $email;
        } elseif ($type == 'updatePassword') {
            $old_password = $request->input('old_password');

            $validator = \Validator::make($request->all(), [
        'password' => 'required|string|min:6|confirmed',
    ]);
    
            if ($validator->fails()) {
                return response()->json(['errors'=> $validator->errors()->all()]);
            }

            if (!Hash::check($old_password, $user->password)) {
                return response()->json(['errors' => ['Old password doesn\'t match']]); // Status code here
            }

            $user->password = bcrypt($request->input('password'));
        } elseif ($type == "updateStaffPassword") {
            $old_password = $request->input('old_password');

            $validator = \Validator::make($request->all(), [
        'password' => 'required|string|min:6|',
    ]);
    
            if ($validator->fails()) {
                return response()->json(['errors'=> $validator->errors()->all()]);
            }

            if (!Hash::check($old_password, $user->password)) {
                return response()->json(['errors' => ['Old password doesn\'t match']]); // Status code here
            }

            $user->password = bcrypt($request->input('password'));
        } else {
            $is_enabled = $request->input('is_enabled');
            $user->is_enabled = $is_enabled === 'true' ? true : false;
        }

        $user->save();
        $user->id_client = $id;

        return $user;
    }

    public function showProfile()
    {
        $user = Auth::user();
        $wishlist = WishList::wishlist($user->id);
        $billingInfo = BillingInformation::billingInformation($user->id);
        $purchaseHistory = User::purchaseHistory($user->id);
        $footerCategories = Category::getFooterCategories();

        return view('pages.profile', ['user' => $user, 'wishlist'=> $wishlist, 'billingInfo' => $billingInfo, 'purchaseHistory' => $purchaseHistory, 'categories' => $footerCategories]);
    }
}
