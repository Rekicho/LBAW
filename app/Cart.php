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
        ->selectRaw('carts.id AS id_context, carts.id_product, carts.quantity AS quantity, products.name, products.description, products.price, products.discount, 0')
        ->where('carts.id_client', $userId)
        ->whereNotIn('carts.id_product', function ($q) {
            $q->select('reviews.id_product')->from('reviews');
        });
    
        $products = DB::table('carts')
        ->join('products', 'products.id', '=', 'carts.id_product')
        ->join('reviews', 'carts.id_product', '=', 'reviews.id_product')
        ->selectRaw('carts.id AS id_context, carts.id_product, carts.quantity AS quantity, products.name, products.description, products.price, products.discount, AVG(reviews.rating) AS rating')
        ->where('carts.id_client', $userId)
        ->groupBy('id_context', 'carts.id_product', 'quantity', 'products.name', 'products.description', 'products.price', 'products.discount')
        ->union($noRatings)
		->get();
		
		return Product::applyDiscounts($products); 
	}

	public static function removeAllProductsFromCart($id_client) { 
		DB::table('carts')->where('id_client',$id_client)->delete();
	}
	
	public static function getCartTotal($products) {
		$total = 0;

		foreach($products as $product)
			$total += $product->price * $product->quantity;

		return $total;
	}
}
