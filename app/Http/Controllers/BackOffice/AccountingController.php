<?php

namespace App\Http\Controllers\BackOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Purchase;

class AccountingController extends BaseBOController
{
    public function __construct()
    {    parent::__construct();

        $this->middleware('staffmember');
    }

    public function getAccountingPayments()
    {
        $payments = Purchase::getProductsWaitingForConfirmation();
        return view('pages.accountingPayments', ['payments' => $payments]);
    }
  
    public function show()
    {
    //   if (!Auth::check()) return redirect('/login');

     // $this->authorize('show', BackOfficePolicy::class);

      return view('pages.accounting');
    }
}
