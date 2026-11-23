<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $client_id
 * @property string $immatriculation
 * @property string $marque
 * @property string $modele
 * @property int|null $annee
 * @property int|null $kilometrage_actuel
 * @property string|null $type_carburant
 * @property-read string $libelle
 * @property-read Client|null $client
 */
class Vehicule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'immatriculation',
        'marque',
        'modele',
        'annee',
        'couleur',
        'numero_chassis',
        'kilometrage_actuel',
        'type_carburant',
        'date_mise_circulation',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'annee' => 'integer',
        'kilometrage_actuel' => 'integer',
        'date_mise_circulation' => 'date',
    ];

    public function getLibelleAttribute(): string
    {
        return trim("{$this->marque} {$this->modele} ({$this->immatriculation})");
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany<OrdreReparation, $this>
     */
    public function ordres(): HasMany
    {
        return $this->hasMany(OrdreReparation::class);
    }
}
