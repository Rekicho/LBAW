<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    public $timestamps  = false;

    protected $table = 'categories';
    
    public static function getProductsFromCategory($id, $limit)
    {
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
        ->paginate($limit);
    }

    public static function getCategoryByName($name){
        return Category::where('name', $name)->first();
    }

    public static function getFooterCategories()
    {
        return DB::table('categories')
        ->select('id', 'name')
        ->limit(8)
        ->get();
    }

    public static function getTopCategories()
    {
        return DB::table('categories')
        ->select('id', 'name')
        ->limit(4)
        ->get();
    }

    public static function getAllCategories()
    {
        return DB::table('categories')->select('id', 'name')->get();
    }
    public static function categories()
    {
        $categories = Category::paginate(10);
        foreach ($categories as $category) {
        }
        $category->num_products = Product::getNumProductsFromCategory($category->id)["num_products"];
        return $categories;
    }
}