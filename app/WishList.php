<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WishList extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  // TODO: funcao pra retornar wishlist de um user
  public static function wishlist($userId){
    return DB::table('purchased_product')->join('products', 'products.id', '=', 'purchased_product.id_product')
    ->join('purchase', 'purchase.id', '=', 'purchased_product.id_purchase')
    ->select('purchased_product.id_product')
    ->where('products.stock', '>', '0')->whereRaw('DATE_PART(\'day\', now() - purchase.date_time) < 30')->groupby('purchased_product.id_product')->orderByRaw('COUNT(purchased_product.id_product) DESC')->limit(4)->get();
  }
}
