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

        return view('pages.moderator', ['username' => $username]);
    }

    public function getModeratorType($type)
    {
        if ($type == 'users') {
            $clients = User::clients();
            $view = view('pages.moderatorUsersAjax', ['clients' => $clients]);
        } else if ($type == 'reports') {
            $reports = Report::reports();
            $view = view('pages.moderatorReportsAjax', ['reports' => $reports]);
        } else {
            $reviews = Review::reviews();
            $view = view('pages.moderatorReviewsAjax', ['reviews' => $reviews]);
        }

        echo $view;
        exit;
    }
}
