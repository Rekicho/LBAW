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

  public function getStaffMembers()
  {
    $staff_members = Auth::user()->staff_members();

    echo view('pages.adminStaffMembers', ['staff_members' => $staff_members]);
    exit;
  }

  public function show()
  {
      //   if (!Auth::check()) return redirect('/login');

      // $this->authorize('show', BackOfficePolicy::class);

      return view('pages.admin');
  }
}
