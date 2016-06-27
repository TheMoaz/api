<?php

namespace App\Policies;

use App\User;
use App\Skill;

class SkillPolicy
{
    //
    // Check for Administrators
    //
    public function before($user)
    {
        if ($user->role === 'Admin') return true;
    }
    //
    // List approved skills
    //
    public function list(User $user)
    {
        return  in_array($user->role, ['Provider','Merchant'], true);
    }
    //
    // View a skill
    //
    public function show(User $user)
    {
        return  in_array($user->role, ['Provider','Merchant'], true);
    }
    //
    // Create a new skill
    //
    public function create(User $user)
    {
        return  in_array($user->role, ['Provider','Merchant'], true);
    }
    //
    // Edit an existing skill
    //
    public function edit(User $user, Skill $skill)
    {
        return  (in_array($user->role, ['Merchant'], true) && ($user->user_id === $skill->added_by));
    }
    //
    // Delete an existing skill
    //
    public function delete(User $user, Skill $skill)
    {
        return  (in_array($user->role, ['Merchant'], true) && ($user->user_id === $skill->added_by));
    }
    
}