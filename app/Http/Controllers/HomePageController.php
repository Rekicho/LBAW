<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Product;

class HomePageController extends BaseController
{
    public function show(){

        $time = Carbon::now();

        $topProducts =  Product::topProducts();

        return view('pages.home', ['topProducts' => $topProducts]);

    }
}
