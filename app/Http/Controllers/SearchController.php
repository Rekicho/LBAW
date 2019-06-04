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
		$price = Input::get('price');

		if($category != "")
			$products = Product::search($search)->where('id_category',$category)->paginate(16);
		
		else $products = Product::search($search)->paginate(16);

		$categories = Category::getAllCategories();

		return view('pages.search', ['products' => $products->appends(Input::except('page')), 'query' => $search, 'categories' => $categories]);
    }
}
