<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property array<int, string>|null $permissions
 * @property string|null $description
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'permissions',
        'description',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
