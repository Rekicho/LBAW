<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\BillingInformation;
use App\WishList;
use App\Purchase;

class BillingInfoController extends Controller
{
    /**
     * Creates a new card.
     *
     * @return Card The card created.
     */
    public function create(Request $request)
    {
      $billingInfo = new BillingInformation();

      // $this->authorize('create', $user);

      $billingInfo->full_name = $request->input('full_name');
      $billingInfo->address = $request->input('address');
      $billingInfo->city = $request->input('city');
      $billingInfo->state = $request->input('state');
      $billingInfo->zip_code = $request->input('zip_code');
      $billingInfo->id_client = Auth::user()->id;
      
      $billingInfo->save();

      return $billingInfo;
    }

    public function update(Request $request, $id){
      $billingInfo = BillingInformation::find($id);

      // TODO: $this->authorize('update', $user);
    
      $billingInfo->full_name = $request->input('full_name');
      $billingInfo->address = $request->input('address');
      $billingInfo->city = $request->input('city');
      $billingInfo->state = $request->input('state');
      $billingInfo->zip_code = $request->input('zip_code');

      $billingInfo->save();

      return $billingInfo;
    }
}
