<?php

namespace App\Policies;

use App\User;
use App\Report;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class ReportPolicy 
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $user, Report $report){
        return Auth::check();
    }
}
