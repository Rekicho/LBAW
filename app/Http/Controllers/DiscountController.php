<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Discount;

class DiscountController extends Controller
{
    public function create(Request $request){
        $discount = new Discount();

        $discount->value = $request->input('value');
        $discount->start_t = $request->input('start');
        $discount->end_t = $request->input('end');
        $discount->id_category = $request->input('id_category');
        
        $discount->save();

        return $discount;
    }
}
