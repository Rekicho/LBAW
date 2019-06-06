<?php

namespace App\Policies;

use App\User;
use App\BillingInformation;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillingInfoPolicy
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

    public function update(User $user, BillingInformation $billingInfo)
    {
      return $user->id == $billingInfo->id_client;
    }
}
