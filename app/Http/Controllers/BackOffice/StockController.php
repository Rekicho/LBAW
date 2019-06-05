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

    public function getStockType($type)
    {
        if ($type == 'products') {
            $products = Product::products();
            $view = view('pages.stockProducts', ['products' => $products]);
        } else {
            $categories = Category::categories();
            $view = view('pages.stockCategories', ['categories' => $categories]);
        }

        echo $view;
        exit;
    }

    public function show()
    {
        $username = Auth::user()->username;
        $categories = Category::categories();

        return view('pages.stock', ['username' => $username, 'categories' => $categories]);
    }
}
