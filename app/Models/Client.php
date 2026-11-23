<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $type
 * @property string $nom
 * @property string|null $prenom
 * @property string $telephone
 * @property string|null $telephone_2
 * @property string|null $email
 * @property string|null $adresse
 * @property string|null $ville
 * @property float $credit_autorise
 * @property float $solde_credit
 * @property-read string $nom_complet
 */
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'nom',
        'prenom',
        'telephone',
        'telephone_2',
        'email',
        'adresse',
        'ville',
        'numero_contribuable',
        'credit_autorise',
        // solde_credit volontairement exclu : ne doit être modifié que via la logique métier (paiements / factures)
        'notes',
        'created_by',
    ];

    protected $casts = [
        'credit_autorise' => 'decimal:0',
        'solde_credit' => 'decimal:0',
    ];

    public function getNomCompletAttribute(): string
    {
        if ($this->type === 'entreprise') {
            return $this->nom;
        }

        return trim(($this->prenom ?? '').' '.$this->nom);
    }

    /**
     * @return HasMany<Vehicule, $this>
     */
    public function vehicules(): HasMany
    {
        return $this->hasMany(Vehicule::class);
    }

    /**
     * @return HasMany<OrdreReparation, $this>
     */
    public function ordres(): HasMany
    {
        return $this->hasMany(OrdreReparation::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
