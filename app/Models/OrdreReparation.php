<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $numero
 * @property int $client_id
 * @property int $vehicule_id
 * @property string $statut
 * @property float|string $montant_ht
 * @property float|string $montant_tva
 * @property float|string $montant_ttc
 * @property float|string $acompte
 * @property float|string $reste_a_payer
 * @property array<int, int>|null $mecaniciens_ids
 * @property-read Client|null $client
 * @property-read Vehicule|null $vehicule
 * @property-read Collection<int, LigneOrdre> $lignes
 */
class OrdreReparation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordres_reparation';

    protected $fillable = [
        'numero',
        'client_id',
        'vehicule_id',
        'statut',
        'date_entree',
        'date_sortie_prevue',
        'date_sortie_reelle',
        'kilometrage_entree',
        'description_travaux',
        'diagnostic',
        'mecaniciens_ids',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'devise_id',
        'taux_applique',
        'acompte',
        'reste_a_payer',
        'mode_paiement',
        'assurance_nom',
        'numero_sinistre',
        'franchise',
        'part_assurance',
        'part_client',
        'created_by',
        'closed_by',
        'notes_internes',
    ];

    protected $casts = [
        'date_entree' => 'datetime',
        'date_sortie_prevue' => 'datetime',
        'date_sortie_reelle' => 'datetime',
        'kilometrage_entree' => 'integer',
        'mecaniciens_ids' => 'array',
        'montant_ht' => 'decimal:0',
        'montant_tva' => 'decimal:0',
        'montant_ttc' => 'decimal:0',
        'taux_applique' => 'decimal:6',
        'acompte' => 'decimal:0',
        'reste_a_payer' => 'decimal:0',
        'franchise' => 'decimal:0',
        'part_assurance' => 'decimal:0',
        'part_client' => 'decimal:0',
    ];

    /**
     * Génère un numéro unique OR-YYYY-XXXXX.
     * Verrou transactionnel pour limiter les collisions concurrentes.
     */
    public static function genererNumero(): string
    {
        $annee = now()->format('Y');

        return DB::transaction(function () use ($annee) {
            $dernier = static::withTrashed()
                ->where('numero', 'like', "OR-{$annee}-%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->value('numero');

            $sequence = 1;
            if ($dernier && preg_match('/OR-\d{4}-(\d+)/', (string) $dernier, $m)) {
                $sequence = (int) $m[1] + 1;
            }

            return sprintf('OR-%s-%05d', $annee, $sequence);
        });
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Vehicule, $this>
     */
    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * @return BelongsTo<Devise, $this>
     */
    public function devise(): BelongsTo
    {
        return $this->belongsTo(Devise::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * @return HasMany<LigneOrdre, $this>
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(LigneOrdre::class, 'ordre_id')->orderBy('ordre_affichage');
    }

    /**
     * @return HasMany<NotificationLog, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'ordre_id');
    }

    public function recalculerMontants(): void
    {
        $ht = (float) $this->lignes()->sum('montant_ht');
        $this->montant_ht = $ht;
        $this->montant_tva = 0;
        $this->montant_ttc = $ht + (float) $this->montant_tva;
        $this->reste_a_payer = (float) $this->montant_ttc - (float) $this->acompte;
        $this->save();
    }
}
