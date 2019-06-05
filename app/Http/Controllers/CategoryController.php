<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use App\Category;

class CategoryController extends BaseController
{
    public function show($id)
    {
        try {
            $category = Category::find($id);

            if($category == null){
                return view('errors.page_not_found', ['error' => 'That category doesn\'t exist yet!']);
            }
        } catch (QueryException $e) {
            return view('errors.page_not_found', ['error' => 'Category not found!']);
        }

        $footerCategories = Category::getFooterCategories();
        $products = Category::getProductsFromCategory($id);

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
