<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function create(User $user, User $new_user){
        return $user->is_staff_member && $user->is_enabled;
    }

    public function update(User $user, User $other_user){
        return ($user->is_staff_member && $user->is_enabled) || $user->id == $other_user->id;
    }
}
