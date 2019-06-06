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

        // Watches
        $category = Category::find(1);
        $featured = Product::topProductsFromCategory(1);

        $electronics = Category::getProductsFromCategory(Category::getCategoryByName('Electronics')->id, 4);

        return view('pages.home', ['topProducts' => $topProducts, 'featuredCategoryProducts' => $featured, 'featuredCategory' => $category, 'topCategories' => $topCategories, 'electronics' => $electronics]);

    }
}
