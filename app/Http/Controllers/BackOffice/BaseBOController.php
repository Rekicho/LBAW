<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BaseBOController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
    
            view()->share('user', $user);

            return $next($request);
        });
    }

    public function showStaffProfile()
    {
        return view('pages.staff_profile');
    }
}
