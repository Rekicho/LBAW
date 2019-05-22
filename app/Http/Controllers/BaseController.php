<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$user = Auth::user();
            $notifications = [];

			if(Auth::check()){
                $notifications = $user->notifications;
            }
	
            view()->share('notifications', $notifications);

			return $next($request);
		});
	}
}
