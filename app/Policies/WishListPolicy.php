<?php

namespace App\Policies;

use App\User;
use App\WishList;
use Illuminate\Auth\Access\HandlesAuthorization;

class WishListPolicy
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

    public function delete(User $user, WishList $list){
        return $user->id == $list->id_client;
    }
}
