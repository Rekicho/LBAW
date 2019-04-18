<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AboutController extends Controller
{
    public function showAbout(){
        return View::make('pages/about');
    }

    public function showFaq(){
        return View::make('pages/faq');
    }

    public function showContact(){
        return View::make('pages/contact');
    }
}
