<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Product;

class Category extends Model
{

    public $timestamps  = false;

    public static function categories(){
         $categories = Category::paginate(10);

         foreach($categories as $category){
             $category->num_products = Product::getNumProductsFromCategory($category->id)["num_products"];
         }

         return $categories;
    }
}
