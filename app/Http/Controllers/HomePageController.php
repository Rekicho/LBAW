<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Product;
use App\Category;

class HomePageController extends BaseController
{
    public function show(){

        $time = Carbon::now();

		$topProducts =  Product::topProducts();

        $topCategories = Category::getTopCategories();

        $category_id = rand(1, 4);
        $category = Category::find($category_id);

        $featured = Product::topProductsFromCategory($category_id);

        $watches = Category::getProductsFromCategory(Category::getCategoryByName('Watches')->id, 4);

        return view('pages.home', ['topProducts' => $topProducts, 'featuredCategoryProducts' => $featured, 'featuredCategory' => $category, 'topCategories' => $topCategories, 'watches' => $watches]);

    }
}
