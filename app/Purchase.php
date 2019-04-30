<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

    public static function getProductsFromPurchase($id){
        return DB::table('purchased_product')
        ->select('id_product', 'name', 'price', 'discount', 'quantity')
        ->where('id_purchase', $id)
        ->get();
    }
}
