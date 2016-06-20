<?php

namespace App\Policies;

use App\User;

class UserPolicy
{
    /**
     * Determine if the given post can be updated by the user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function view_all_logs(User $user)
    {
        return  $user->role === 'Admin';
    }

    public function view_merchant_logs(User $user, User $member)
    {
        return  ($user->role === 'Admin' || $user->role === 'Provider') && 
                ($user->provider === $member->provider);
    }
}