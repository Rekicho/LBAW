<?php

namespace App\Policies;

use App\User;
use App\ReportLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportLogPolicy
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

    public function create(User $user, ReportLog $reportLog){
        return $user->is_staff_member && $user->is_enabled;
    }
}
