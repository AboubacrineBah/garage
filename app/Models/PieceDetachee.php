<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PieceDetachee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pieces_detachees';

    protected $fillable = [
        'reference',
        'designation',
        'categorie',
        'marque',
        'prix_achat',
        'prix_vente',
        'stock_actuel',
        'stock_minimum',
        'unite',
        'emplacement',
        'fournisseur_principal',
        'is_active',
    ];

    protected $casts = [
        'prix_achat' => 'decimal:0',
        'prix_vente' => 'decimal:0',
        'stock_actuel' => 'decimal:2',
        'stock_minimum' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Indique si le stock est sous le seuil minimum.
     */
    public function getStockCritiqueAttribute(): bool
    {
        return $this->stock_actuel <= $this->stock_minimum;
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'piece_id');
    }

    public function lignesOrdre(): HasMany
    {
        return $this->hasMany(LigneOrdre::class, 'piece_id');
    }
}
