<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    public $timestamps  = false;

    protected $table = 'categories';
    
    // TODO: union e pagination = rip
    public static function getProductsFromCategory($id){
        $noRatings = DB::table('categories')
        ->join('products', 'products.id_category', '=', 'categories.id')
        ->selectRaw('categories.id, categories.name, products.id AS id_product, products.name, products.price, products.discount, 0')
        ->where('categories.id', $id)
        ->whereNotIn('products.id', function ($q) {
            $q->select('reviews.id_product')->from('reviews');
        });

            return DB::table('categories')
        ->join('products', 'products.id_category', '=', 'categories.id')
        ->join('reviews', 'products.id', '=', 'reviews.id_product')
        ->selectRaw('categories.id, categories.name, products.id AS id_product, products.name, products.price, products.discount, AVG(reviews.rating) AS rating')
        ->where('categories.id', $id)
        ->groupBy('categories.id', 'categories.name', 'products.id', 'products.name', 'products.price', 'products.discount')
        ->union($noRatings)
        ->paginate(5);
    }

    public static function getFooterCategories(){
        return DB::table('categories')
        ->select('id', 'name')
        ->limit(8)
        ->get();
    }
}
