<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $table = 'notifications_logs';

    protected $fillable = [
        'ordre_id',
        'client_id',
        'canal',
        'destinataire',
        'type_notification',
        'message',
        'statut',
        'provider_response',
        'sent_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime',
    ];

    public function ordre(): BelongsTo
    {
        return $this->belongsTo(OrdreReparation::class, 'ordre_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
