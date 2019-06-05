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

        $footerCategories = Category::getFooterCategories();

        $featured = Product::topProductsFromCategory(rand(1, 4));

        return view('pages.home', ['topProducts' => $topProducts, 'featuredCategoryProducts' => $featured, 'categories' => $footerCategories]);

    }
}
