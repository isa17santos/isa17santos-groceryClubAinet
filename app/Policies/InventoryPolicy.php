<?php

namespace App\Policies;

use App\Models\User;

class InventoryPolicy
{
    public function access(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }
    
}
