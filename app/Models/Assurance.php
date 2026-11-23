<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'code', 'telephone', 'email', 'adresse', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sinistres(): HasMany
    {
        return $this->hasMany(Sinistre::class);
    }
}
