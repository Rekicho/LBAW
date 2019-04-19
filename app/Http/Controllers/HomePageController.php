<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class HomePageController extends Controller
{
    public function show(){

        $time = Carbon::now();

        $topProducts =  DB::table('purchased_product')->join('products', 'products.id', '=', 'purchased_product.id_product')
        ->join('purchase', 'purchase.id', '=', 'purchased_product.id_purchase')
        ->select('purchased_product.id_product')
        ->where('products.stock', '>', '0')->whereRaw('DATE_PART(\'day\', now() - purchase.date_time) < 30')->groupby('purchased_product.id_product')->orderByRaw('COUNT(purchased_product.id_product) DESC')->limit(4)->get();

        return view('pages.home', ['topProducts' => $topProducts]);

    }
}
