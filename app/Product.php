<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
  use FullTextSearch;
  // Don't add create and update timestamps in database.
  public $timestamps  = false;

  protected $searchable = ['search'];

  public static function topProducts(){
    return DB::table('purchased_product')->join('products', 'products.id', '=', 'purchased_product.id_product')
    ->join('purchase', 'purchase.id', '=', 'purchased_product.id_purchase')
    ->select('purchased_product.id_product', 'products.name')
    ->where('products.stock', '>', '0')->whereRaw('DATE_PART(\'day\', now() - purchase.date_time) < 90')->groupBy('purchased_product.id_product', 'products.name')->orderByRaw('COUNT(purchased_product.id_product) DESC')->limit(4)->get();
  } 

  public static function topProductsFromCategory($id_category){
    return DB::table('purchased_product')->join('products', 'products.id', '=', 'purchased_product.id_product')
    ->join('purchase', 'purchase.id', '=', 'purchased_product.id_purchase')
    ->select('purchased_product.id_product', 'products.name')
    ->where('products.stock', '>', '0')->where('products.id_category', '=', $id_category)->whereRaw('DATE_PART(\'day\', now() - purchase.date_time) < 90')->groupBy('purchased_product.id_product', 'products.name')->orderByRaw('COUNT(purchased_product.id_product) DESC')->limit(4)->get();
  }

  public static function getProductInfo($id){
    return DB::table('products')->join('categories', 'products.id_category', '=', 'categories.id')
    ->select(DB::raw('products.id, products.name AS prodname, price, description, discount, stock, categories.name AS catname'))->where('products.id', $id)->groupBy('products.id', 'products.name', 'products.price', 'products.description', 'products.discount', 'products.stock', 'categories.name')->first();
  }

  public static function products(){
    return Product::paginate(10);
  }

  public static function getNumProductsFromCategory($id){
    return Product::selectRaw('COUNT(*) as num_products')
    ->where('id_category', $id)->first();
  }
}
