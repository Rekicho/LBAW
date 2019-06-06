<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AdminController extends BaseBOController
{

  public function __construct()
  {
    parent::__construct();
      $this->middleware('staffmember');
  } 

    public function show()
    {
    //   if (!Auth::check()) return redirect('/login');

     // $this->authorize('show', BackOfficePolicy::class);

      $staff_members = Auth::user()->staff_members();

      return view('pages.admin', ['staff_members' => $staff_members]);
    }
}
