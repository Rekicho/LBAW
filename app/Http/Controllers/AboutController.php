<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Category;

class AboutController extends BaseController
{
    public function showAbout(){
        $footerCategories = Category::getFooterCategories();
        return view('pages.about', ['categories' => $footerCategories]);
    }

    public function showFaq(){
        $footerCategories = Category::getFooterCategories();
        return view('pages.faq', ['categories' => $footerCategories]);
    }

    public function showContact(){
        $footerCategories = Category::getFooterCategories();
        return view('pages.contact', ['categories' => $footerCategories]);
    }
}
