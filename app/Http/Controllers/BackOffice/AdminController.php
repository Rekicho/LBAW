<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

  public function __construct()
  {
      $this->middleware('admin');
  } 

    public function show()
    {
    //   if (!Auth::check()) return redirect('/login');

     // $this->authorize('show', BackOfficePolicy::class);

      $staff_members = Auth::user()->staff_members();
      $username = Auth::user()->username;

      return view('pages.admin', ['staff_members' => $staff_members, 'username' => $username]);
    }
}
