# Intégration Spatie Laravel Permission

## 1. Installation

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

## 2. Configuration du modèle User

Dans `app/Models/User.php`, ajouter le trait :

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}
```

> **Note** : Les méthodes `hasRole()` et `hasPermission()` déjà présentes dans notre modèle User
> continueront de fonctionner. Spatie fournit des versions plus puissantes (`hasRole`, `hasPermissionTo`, `can`, etc.).

## 3. Enregistrement du middleware (optionnel)

Dans `bootstrap/app.php` (Laravel 11+) ou `app/Http/Kernel.php` :

```php
// Laravel 11
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

## 4. Seed des rôles et permissions Spatie

Vous pouvez adapter le `RoleSeeder` existant ou créer un seeder dédié :

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Exemple
$permissions = [
    'ordres.view', 'ordres.create', 'ordres.update', 'ordres.delete', 'ordres.facturer',
    'clients.view', 'clients.create', 'clients.update',
    'stock.view', 'stock.mouvement', 'stock.manage',
    'rapports.view',
];

foreach ($permissions as $perm) {
    Permission::firstOrCreate(['name' => $perm]);
}

$gerant = Role::firstOrCreate(['name' => 'gerant']);
$gerant->givePermissionTo(Permission::all());

$receptionniste = Role::firstOrCreate(['name' => 'receptionniste']);
$receptionniste->givePermissionTo([
    'ordres.view', 'ordres.create', 'ordres.update',
    'clients.view', 'clients.create', 'clients.update',
    'stock.view',
]);
// etc.
```

Puis assigner le rôle à l’utilisateur :

```php
$user->assignRole('gerant');
```

## 5. Utilisation dans les contrôleurs

```php
// Via Policy (recommandé)
$this->authorize('create', OrdreReparation::class);
$this->authorize('update', $ordre);

// Via middleware sur les routes
Route::middleware(['role:gerant|receptionniste'])->group(...);
Route::middleware(['permission:ordres.facturer'])->group(...);

// Via Gate / can
if ($user->can('ordres.facturer')) { ... }
```

## 6. Policies déjà fournies

| Policy                    | Modèle              | Méthodes principales                          |
|---------------------------|---------------------|-----------------------------------------------|
| `OrdreReparationPolicy`   | OrdreReparation     | view, create, update, delete, facturer, downloadPdf |
| `ClientPolicy`            | Client              | view, create, update, delete, manageCredit    |
| `PieceDetacheePolicy`     | PieceDetachee       | view, create, update, delete, mouvement       |

Enregistrez-les via `AuthServiceProvider` (déjà fourni).

## 7. Compatibilité avec le système actuel

Le modèle `Role` et la colonne `role_id` sur `users` peuvent coexister avec Spatie.
Pour une migration progressive :

1. Installer Spatie
2. Synchroniser les rôles existants vers les rôles Spatie
3. Remplacer progressivement `hasRole('xxx')` par `$user->hasRole('xxx')` (Spatie)
4. (Optionnel) Supprimer la table `roles` custom et la colonne `role_id`
