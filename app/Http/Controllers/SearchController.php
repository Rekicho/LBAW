<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use App\Product;

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
        $products = Product::search($search)->paginate(16);
      
        return view('pages.search', ['products' => $products, 'query' => $search]);
    }
}
