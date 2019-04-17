<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    public function show(){
        return View::make('pages/admin');
    }
}
