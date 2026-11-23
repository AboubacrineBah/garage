<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sinistre extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordre_id', 'assurance_id', 'numero_sinistre',
        'franchise', 'part_assurance', 'part_client', 'statut', 'notes',
    ];

    protected $casts = [
        'franchise' => 'decimal:0',
        'part_assurance' => 'decimal:0',
        'part_client' => 'decimal:0',
    ];

    public function ordre(): BelongsTo
    {
        return $this->belongsTo(OrdreReparation::class, 'ordre_id');
    }

    public function assurance(): BelongsTo
    {
        return $this->belongsTo(Assurance::class);
    }
}
