<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormulationPeinture extends Model
{
    use HasFactory;

    protected $table = 'formulations_peinture';

    protected $fillable = [
        'ordre_id', 'vehicule_id', 'user_id', 'code_teinte', 'nom', 'notes',
    ];

    public function ordre(): BelongsTo
    {
        return $this->belongsTo(OrdreReparation::class, 'ordre_id');
    }

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function composants(): HasMany
    {
        return $this->hasMany(ComposantFormulation::class, 'formulation_id');
    }
}
