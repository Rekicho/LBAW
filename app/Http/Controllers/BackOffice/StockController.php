<?php

namespace App\Http\Controllers\BackOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Product;
use App\Category;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('staffmember');
    }

    public function show()
    {
        $user = Auth::user();
        $products = Product::products();
        $categories = Category::categories();

        return view('pages.stock', ['user' => $user, 'products' => $products, 'categories' => $categories]);
    }
}
