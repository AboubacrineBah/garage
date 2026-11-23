<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\OrdreReparation;
use App\Models\PieceDetachee;
use App\Policies\ClientPolicy;
use App\Policies\OrdreReparationPolicy;
use App\Policies\PieceDetacheePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Enregistrement des Policies.
 *
 * Laravel 11+ : ajouter dans bootstrap/providers.php
 * Laravel 10  : déjà listé dans config/app.php
 */
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        OrdreReparation::class => OrdreReparationPolicy::class,
        Client::class => ClientPolicy::class,
        PieceDetachee::class => PieceDetacheePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
