<?php

namespace App\Policies;

use App\Models\PieceDetachee;
use App\Models\User;

class PieceDetacheePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PieceDetachee $piece): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('gerant')
            || $user->hasRole('chef_atelier')
            || $user->hasRole('comptable');
    }

    public function update(User $user, PieceDetachee $piece): bool
    {
        return $user->hasRole('gerant')
            || $user->hasRole('chef_atelier');
    }

    public function delete(User $user, PieceDetachee $piece): bool
    {
        return $user->hasRole('gerant');
    }

    public function mouvement(User $user): bool
    {
        return $user->hasRole('gerant')
            || $user->hasRole('chef_atelier')
            || $user->hasRole('receptionniste');
    }
}
