<?php

namespace App\Http\Controllers\BackOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Purchase;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    } 
  
    public function show()
    {
    //   if (!Auth::check()) return redirect('/login');

     // $this->authorize('show', BackOfficePolicy::class);

      $username = Auth::user()->username;

      $payments = Purchase::getProductsWaitingForConfirmation();

      return view('pages.accounting', ['username' => $username, 'payments' => $payments]);
    }
}
