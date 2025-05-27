<?php

namespace App\Policies;

use App\Models\User;

class BusinessSettingsPolicy
{
    
    public function manage(User $user)
    {
        return $user->type === 'board';
    }

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
}
