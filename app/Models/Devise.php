<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'symbole',
        'taux_vers_xof',
        'decimales',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'taux_vers_xof' => 'decimal:6',
        'decimales' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Retourne la devise par défaut (XOF).
     */
    public static function default(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Trouve une devise par son code.
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('code', strtoupper($code))->where('is_active', true)->first();
    }
}
