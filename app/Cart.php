<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{

    public $timestamps  = false;

    public static function exists($userId, $productId){
        return DB::table('carts')
        ->select('id', 'id_product', 'id_client')
        ->where('id_client', $userId)
        ->where('id_product', $productId)
        ->first();
    }

    public static function getProductsFromCart($userId){
        $noRatings = DB::table('carts')
        ->join('products', 'products.id', '=', 'carts.id_product')
        ->selectRaw('carts.id_product, products.name, products.price, products.discount, 0')
        ->where('carts.id_client', $userId)
        ->whereNotIn('carts.id_product', function ($q) {
            $q->select('reviews.id_product')->from('reviews');
        });
    
            return DB::table('carts')
        ->join('products', 'products.id', '=', 'carts.id_product')
        ->join('reviews', 'carts.id_product', '=', 'reviews.id_product')
        ->selectRaw('carts.id_product, products.name, products.price, products.discount, AVG(reviews.rating) AS rating')
        ->where('carts.id_client', $userId)
        ->groupBy('carts.id_product', 'products.name', 'products.price', 'products.discount')
        ->union($noRatings)
        ->get();
    }
}
