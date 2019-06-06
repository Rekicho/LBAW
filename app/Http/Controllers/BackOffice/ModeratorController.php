<?php

namespace App\Http\Controllers\BackOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Review;
use App\Report;

class ModeratorController extends BaseBOController
{
    public function __construct()
    {    parent::__construct();

        $this->middleware('staffmember');
    }

    public function show()
    {
        return view('pages.moderator');
    }

    public function getModeratorType($type)
    {
        if ($type == 'users') {
            $clients = User::clients();
            $view = view('pages.moderatorUsers', ['clients' => $clients]);
        } else if ($type == 'reports') {
            $reports = Report::reports();
            $view = view('pages.moderatorReports', ['reports' => $reports]);
        } else {
            $reviews = Review::reviews();
            $view = view('pages.moderatorReviews', ['reviews' => $reviews]);
        }

        echo $view;
        exit;
    }
}
