<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\PurchaseLog;

class PurchaseLogController extends Controller
{
    public function create(Request $request, $id){
      $purchase_log = new PurchaseLog();
      $purchase_log->id_purchase = $id;
      $purchase_log->purchase_state = $request->state;

      $purchase_log->save();

      return $purchase_log;
    }
}
