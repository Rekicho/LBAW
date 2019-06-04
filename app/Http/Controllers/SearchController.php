<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use App\Product;
use App\Category;

class SearchController extends Controller
{
    /**
     * Shows the card for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {   
		$search = Input::get('search');
		$category = Input::get('category');
		$above = Input::get('above');
		$below = Input::get('below');

		$match = array();

		if($category != "")
			array_push($match,['id_category', '=', $category]);

		if($above != "")
			array_push($match,['price', '>=', $above]);

		if($below != "")
			array_push($match,['price', '<=', $below]);
		
		$products = Product::search($search)->where($match)->paginate(16);

		$categories = Category::getAllCategories();

		return view('pages.search', ['products' => $products->appends(Input::except('page')), 'query' => $search, 'categories' => $categories]);
    }
}
