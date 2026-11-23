<?php

namespace App\Policies;

use App\Models\OrdreReparation;
use App\Models\User;

class OrdreReparationPolicy
{
    /**
     * Liste des OR : rôles opérationnels.
     * Filtrage fin (mécanicien = ses OR) côté contrôleur.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('gerant')
            || $user->hasRole('receptionniste')
            || $user->hasRole('chef_atelier')
            || $user->hasRole('mecanicien')
            || $user->hasRole('comptable');
    }

    public function view(User $user, OrdreReparation $ordre): bool
    {
        // Rôles privilégiés : accès total
        if ($user->hasRole('gerant')
            || $user->hasRole('receptionniste')
            || $user->hasRole('chef_atelier')
            || $user->hasRole('comptable')
        ) {
            return true;
        }

        // Mécanicien pur : uniquement les ordres qui lui sont assignés
        if ($user->hasRole('mecanicien')) {
            $ids = $ordre->mecaniciens_ids ?? [];

            return in_array($user->id, $ids, true);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('gerant')
            || $user->hasRole('receptionniste')
            || $user->hasRole('chef_atelier');
    }

    public function update(User $user, OrdreReparation $ordre): bool
    {
        if ($user->hasRole('gerant') || $user->hasRole('receptionniste') || $user->hasRole('chef_atelier')) {
            return true;
        }

        // Mécanicien peut mettre à jour le statut de ses ordres
        if ($user->hasRole('mecanicien')) {
            $ids = $ordre->mecaniciens_ids ?? [];

            return in_array($user->id, $ids, true);
        }

        return false;
    }

    public function delete(User $user, OrdreReparation $ordre): bool
    {
        return $user->hasRole('gerant');
    }

    public function facturer(User $user, OrdreReparation $ordre): bool
    {
        return $user->hasRole('gerant') || $user->hasRole('comptable') || $user->hasRole('receptionniste');
    }

    public function downloadPdf(User $user, OrdreReparation $ordre): bool
    {
        return $this->view($user, $ordre);
    }
}
