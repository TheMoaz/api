<?php

namespace App\Policies;

use App\User;

class UserPolicy
{

    public function assign_roles(User $user)
    {
        return $user->role === 'Admin';
    }
    //
    // Can the user see all user logs 
    //
    public function view_all_logs(User $user)
    {
        return  in_array($user->role, ['Admin'], true);
    }

    /**************************************************************************/
    // MEMBERS
    /**************************************************************************/
    //
    // List all members
    //
    public function list_members(User $user)
    {
        return  in_array($user->role, ['Admin'], true);
    }
    //
    // View one member
    //
    public function view_member(User $user)
    {
        return  in_array($user->role, ['Admin', 'Provider', 'Merchant'], true);
    }

    /**************************************************************************/
    // MERCHANTS
    /**************************************************************************/
    //
    // List all merchants
    //
    public function list_all_merchants(User $user)
    {
        return  $user->role === 'Admin';
    }
    //
    // List own merchants
    //
    public function list_own_merchants(User $user)
    {
        return  in_array($user->role, ['Admin','Provider'], true);
    }
    //
    // View own merchant
    //
    public function view_own_merchant(User $user, User $merchant)
    {
        return  in_array($user->role, ['Admin','Provider'], true);
    }
    //
    // Add a new merchant
    //
    public function add_new_merchant(User $user)
    {
        return in_array($user->role, ['Admin','Provider'], true);
    }
    //
    // Edit own merchant
    //
    public function edit_own_merchant(User $user, User $merchant)
    {
        return  ($user->role === 'Admin') ||
                (
                    ($user->role === 'Provider') && ($user->name === $merchant->provider)
                );
    }
    //
    // Edit own merchant
    //
    public function view_own_merchant_log(User $user, User $merchant)
    {
        return  ($user->role === 'Admin') ||
                (
                    ($user->role === 'Provider') && ($user->name === $merchant->provider)
                );
    }


    /**************************************************************************/
    // PROVIDERS
    /**************************************************************************/
    //
    // Can the authorized user list all providers
    //
    public function list_providers(User $user)
    {
        return  $user->role === 'Admin';
    }
    //
    // Can the authorized user list one provider
    //
    public function show_provider(User $user, User $provider)
    {
        return  ($user->role === 'Admin') || 
                (
                    ($user->role === 'Provider') && ($user->user_id === $provider->user_id)
                );
    }
    //
    // Can the authorized user view provider activity
    //
    public function view_provider_log(User $user, User $provider)
    {
        return  ($user->role === 'Admin') || 
                (
                    ($user->role === 'Provider') && ($user->user_id === $provider->user_id)
                );
    }
}