<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'piece_id',
        'type',
        'quantite',
        'ordre_id',
        'user_id',
        'commentaire',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
    ];

    public function piece(): BelongsTo
    {
        return $this->belongsTo(PieceDetachee::class, 'piece_id');
    }

    public function ordre(): BelongsTo
    {
        return $this->belongsTo(OrdreReparation::class, 'ordre_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
