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
        $username = Auth::user()->username;
        $products = Product::products();
        $categories = Category::categories();

        return view('pages.stock', ['username' => $username, 'products' => $products, 'categories' => $categories]);
    }
}
