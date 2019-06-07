<?php

namespace App\Policies;

use App\User;
use App\Cart;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartPolicy
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

    public function create(User $user, Cart $cart){
        return Auth::check();
    }

    public function delete(User $user, Cart $cart){
        return $user->id == $cart->id_client;
    }
}
