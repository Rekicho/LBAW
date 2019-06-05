<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Category;

class CategoryController extends BaseController
{
    public function show($id)
    {
        $products = Category::getProductsFromCategory($id);
        $category = Category::find($id);
        $footerCategories = Category::getFooterCategories();

        return view('pages.category', ['category' => $category, 'products' => $products, 'categories' => $footerCategories]);
    }

    public function create(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');


        $category->save();
        return $category;
    }
}
