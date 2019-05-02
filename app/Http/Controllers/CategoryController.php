<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Category;

class CategoryController extends Controller
{
    public function show($id){
        $products = Category::getProductsFromCategory($id);
        $category = Category::find($id);
        $footerCategories = Category::getFooterCategories();

        return view('pages.category', ['category' => $category, 'products' => $products, 'categories' => $footerCategories]);
    }
}
