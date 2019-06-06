<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

use App\Product;
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
            Log::error("User tried to access nonexistant category", ['id' => $id]);
            return view('errors.page_not_found', ['error' => 'Category not found!']);
        }

		$footerCategories = Category::getFooterCategories();
		
		$above = Input::get('above');
		$below = Input::get('below');
		$order = Input::get('order');

		$match = array();
		array_push($match,['id_category', '=', $id]);

		if($above != "")
			array_push($match,['price', '>=', $above]);

		if($below != "")
			array_push($match,['price', '<=', $below]);
		
		if($order == "DESC")		
			$products = Product::orderByRaw("price DESC")->where($match)->paginate(15);

		else if($order == "ASC")
			$products = Product::orderByRaw("price ASC")->where($match)->paginate(15);

		else $products = Product::where($match)->paginate(15);

		$products = Product::applyDiscounts($products);

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
