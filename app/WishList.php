<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WishList extends Model
{
    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $table = 'wishlists';

    public static function exists($userId, $productId){
        return DB::table('wishlists')
        ->select('id', 'id_product', 'id_client')
        ->where('id_client', $userId)
        ->where('id_product', $productId)
        ->first();
    }

    public static function usersWishlisted($product_id){
        return User::join('wishlists', 'users.id', 'wishlists.client_id')
        ->where('wishlists.id_product', $product_id)
        ->get();
    }

    public static function wishlist($userId)
    {
        $noRatings = DB::table('wishlists')
    ->join('products', 'products.id', '=', 'wishlists.id_product')
    ->selectRaw('wishlists.id, wishlists.id_product, products.name, products.price, products.discount, 0')
    ->where('wishlists.id_client', $userId)
    ->whereNotIn('wishlists.id_product', function ($q) {
        $q->select('reviews.id_product')->from('reviews');
    });

        return DB::table('wishlists')
    ->join('products', 'products.id', '=', 'wishlists.id_product')
    ->join('reviews', 'wishlists.id_product', '=', 'reviews.id_product')
    ->selectRaw('wishlists.id, wishlists.id_product, products.name, products.price, products.discount, AVG(reviews.rating) AS rating')
    ->where('wishlists.id_client', $userId)
    ->groupBy('wishlists.id', 'wishlists.id_product', 'products.name', 'products.price', 'products.discount')
    ->union($noRatings)
    ->get();
    }
}
