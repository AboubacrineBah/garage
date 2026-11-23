<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $ordre_id
 * @property string $type_ligne
 * @property int|null $piece_id
 * @property string $designation
 * @property float|string $quantite
 * @property float|string $prix_unitaire_ht
 * @property float|string $remise_pct
 * @property float|string $montant_ht
 * @property int $ordre_affichage
 * @property-read PieceDetachee|null $piece
 */
class LigneOrdre extends Model
{
    use HasFactory;

    protected $table = 'lignes_ordre';

    protected $fillable = [
        'ordre_id',
        'type_ligne',
        'piece_id',
        'designation',
        'quantite',
        'prix_unitaire_ht',
        'remise_pct',
        'montant_ht',
        'ordre_affichage',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire_ht' => 'decimal:0',
        'remise_pct' => 'decimal:2',
        'montant_ht' => 'decimal:0',
        'ordre_affichage' => 'integer',
    ];

    public function calculerMontant(): void
    {
        $brut = (float) $this->quantite * (float) $this->prix_unitaire_ht;
        $remise = $brut * ((float) $this->remise_pct / 100);
        $this->montant_ht = round($brut - $remise);
    }

    /**
     * @return BelongsTo<OrdreReparation, $this>
     */
    public function ordre(): BelongsTo
    {
        return $this->belongsTo(OrdreReparation::class, 'ordre_id');
    }

    /**
     * @return BelongsTo<PieceDetachee, $this>
     */
    public function piece(): BelongsTo
    {
        return $this->belongsTo(PieceDetachee::class, 'piece_id');
    }
}
