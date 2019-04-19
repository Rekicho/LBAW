<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  public function topProducts(){
    return DB::table('products')->join('purchased_product', 'products.id', '=', 'purchased_product.id_product')
    ->join('purchase', 'purchase.id', '=', 'purchased_product.id_purchase')
    ->select('product.id', 'product.name', 'product.description', 'product.price', 'product.discount')
    ->where([['stock', '>', '0'], ['DATE_PART(\'day\', now() - purchase.date_time', '<' ,'30']])->orderByRaw('COUNT(product.id) DESC')->limit(4)->get();
  }
}
