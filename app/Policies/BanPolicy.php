<?php

namespace App\Policies;

use App\User;
use App\Ban;
use Illuminate\Auth\Access\HandlesAuthorization;

class BanPolicy
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

    public function create(User $user, Ban $ban)
    {
      return $user->is_staff_member;
    }
}
