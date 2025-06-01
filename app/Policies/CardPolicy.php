<?php

namespace App\Policies;

use App\Models\User;

class CardPolicy
{
    public function access(User $user): bool
    {
        return in_array($user->type, ['board', 'member']);
    }
}
