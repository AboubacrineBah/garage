<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComposantFormulation extends Model
{
    use HasFactory;

    protected $table = 'composants_formulation';

    protected $fillable = [
        'formulation_id', 'piece_id', 'designation', 'quantite', 'unite',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
    ];

    public function formulation(): BelongsTo
    {
        return $this->belongsTo(FormulationPeinture::class, 'formulation_id');
    }

    public function piece(): BelongsTo
    {
        return $this->belongsTo(PieceDetachee::class, 'piece_id');
    }
}
