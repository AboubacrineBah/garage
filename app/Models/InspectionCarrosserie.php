<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionCarrosserie extends Model
{
    use HasFactory;

    protected $table = 'inspections_carrosserie';

    protected $fillable = [
        'vehicule_id', 'ordre_id', 'user_id', 'kilometrage',
        'code_teinte', 'zones_choquees',
        'photo_avant', 'photo_arriere', 'photo_gauche', 'photo_droite',
        'date_inspection',
    ];

    protected $casts = [
        'kilometrage' => 'integer',
        'date_inspection' => 'datetime',
    ];

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
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
