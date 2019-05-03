<?php

namespace App\Http\Controllers\BackOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Review;
use App\Report;

class ModeratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('staffmember');
    }

    public function show()
    {
        $username = Auth::user()->username;
        $clients = User::clients();
        $reports = Report::reports();
        $reviews = Review::reviews();

        return view('pages.moderator', ['username' => $username, 'clients' => $clients, 'reviews' => $reviews]);
    }
}
