@extends('layouts.app')

@section('title', $ordre->numero . ' – Ordre de réparation')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
    <div>
        <a href="{{ route('ordres.index') }}" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Retour aux ordres
        </a>
        <h1 class="h4 fw-bold text-primary-gm mb-1">{{ $ordre->numero }}</h1>
        @include('partials.statut-badge', ['statut' => $ordre->statut])
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('ordres.pdf.devis', $ordre) }}" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Devis PDF
        </a>
        <a href="{{ route('ordres.pdf.facture', $ordre) }}" class="btn btn-outline-success btn-sm" target="_blank">
            <i class="bi bi-receipt"></i> Facture PDF
        </a>
        @if(!in_array($ordre->statut, ['termine', 'facture', 'livre', 'annule']))
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalStatut">
                <i class="bi bi-check2"></i> Changer statut
            </button>
        @endif
    </div>
</div>

@include('partials.workflow-stepper', ['statut' => $ordre->statut])

<div class="row g-3">
    {{-- Infos client & véhicule --}}
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Client</div>
            <div class="card-body">
                <div class="fw-semibold">{{ $ordre->client->nom_complet }}</div>
                <div class="text-muted">
                    <i class="bi bi-telephone"></i> {{ $ordre->client->telephone }}
                </div>
                @if($ordre->client->email)
                    <div class="text-muted small">{{ $ordre->client->email }}</div>
                @endif
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Véhicule</div>
            <div class="card-body">
                <div class="fw-semibold">{{ $ordre->vehicule->immatriculation }}</div>
                <div>{{ $ordre->vehicule->marque }} {{ $ordre->vehicule->modele }}</div>
                <div class="small text-muted">
                    {{ number_format($ordre->kilometrage_entree, 0, ',', ' ') }} km
                    · {{ ucfirst($ordre->vehicule->type_carburant) }}
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Dates</div>
            <div class="card-body small">
                <div>Entrée : <strong>{{ $ordre->date_entree?->format('d/m/Y H:i') }}</strong></div>
                @if($ordre->date_sortie_prevue)
                    <div>Prévue : {{ $ordre->date_sortie_prevue->format('d/m/Y') }}</div>
                @endif
                @if($ordre->date_sortie_reelle)
                    <div>Sortie : {{ $ordre->date_sortie_reelle->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Travaux & lignes --}}
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Travaux demandés</div>
            <div class="card-body">
                <p class="mb-0">{{ $ordre->description_travaux }}</p>
                @if($ordre->diagnostic)
                    <hr>
                    <div class="small text-muted">Diagnostic :</div>
                    <p class="mb-0">{{ $ordre->diagnostic }}</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Détail</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Désignation</th>
                            <th class="text-end">Qté</th>
                            <th class="text-end">P.U.</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordre->lignes as $ligne)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark me-1">{{ $ligne->type_ligne }}</span>
                                    {{ $ligne->designation }}
                                </td>
                                <td class="text-end">{{ $ligne->quantite }}</td>
                                <td class="text-end">{{ number_format($ligne->prix_unitaire_ht, 0, ',', ' ') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($ligne->montant_ht, 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aucune ligne</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Total HT</td>
                            <td class="text-end fw-bold">{{ number_format($ordre->montant_ht, 0, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Acompte</td>
                            <td class="text-end">{{ number_format($ordre->acompte, 0, ',', ' ') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Reste à payer</td>
                            <td class="text-end fw-bold text-primary-gm fs-5">
                                {{ number_format($ordre->reste_a_payer, 0, ',', ' ') }} F CFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Facturation & assurance multi-devises --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-cash-coin me-1"></i> Facturation &amp; assurance (multi-devises)
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('ordres.update-facturation', $ordre) }}">
                    @csrf
                    @method('PATCH')
                    <div class="row g-2">
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Devise</label>
                            <select name="devise_id" class="form-select form-select-sm">
                                @foreach(\App\Models\Devise::where('is_active', true)->get() as $d)
                                    <option value="{{ $d->id }}" @selected($ordre->devise_id === $d->id)>
                                        {{ $d->code }} ({{ $d->symbole }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small">Acompte</label>
                            <input type="number" name="acompte" class="form-control form-control-sm"
                                   value="{{ $ordre->acompte }}" min="0">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small">Mode de paiement</label>
                            <select name="mode_paiement" class="form-select form-select-sm">
                                <option value="">—</option>
                                @foreach(['especes','orange_money','wave','moov','virement','assurance'] as $m)
                                    <option value="{{ $m }}" @selected($ordre->mode_paiement === $m)>
                                        {{ ucfirst(str_replace('_', ' ', $m)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-2">
                    <div class="small fw-semibold text-muted mb-1">Assurance / sinistre (double payeur)</div>
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Compagnie assurance</label>
                            <input type="text" name="assurance_nom" class="form-control form-control-sm"
                                   value="{{ $ordre->assurance_nom }}" maxlength="120" placeholder="ex: NSIA, AXA…">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">N° sinistre / bon de prise en charge</label>
                            <input type="text" name="numero_sinistre" class="form-control form-control-sm"
                                   value="{{ $ordre->numero_sinistre }}" maxlength="80">
                        </div>
                        <div class="col-4">
                            <label class="form-label small">Franchise (client)</label>
                            <input type="number" name="franchise" class="form-control form-control-sm"
                                   value="{{ $ordre->franchise }}" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label small">Part assurance</label>
                            <input type="number" name="part_assurance" class="form-control form-control-sm"
                                   value="{{ $ordre->part_assurance }}" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label small">Part client</label>
                            <input type="number" name="part_client" class="form-control form-control-sm"
                                   value="{{ $ordre->part_client }}" min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>

                <div class="mt-2 small text-muted">
                    Total TTC : <strong>{{ number_format($ordre->montant_ttc, 0, ',', ' ') }}</strong> ·
                    Reste à payer : <strong class="text-primary-gm">{{ number_format($ordre->reste_a_payer, 0, ',', ' ') }} F CFA</strong>
                </div>
            </div>
        </div>

        @if($ordre->notifications->isNotEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Notifications envoyées</div>
                <ul class="list-group list-group-flush">
                    @foreach($ordre->notifications as $notif)
                        <li class="list-group-item small d-flex justify-content-between">
                            <span>
                                <span class="badge bg-secondary">{{ $notif->canal }}</span>
                                {{ $notif->type_notification }}
                                → {{ $notif->destinataire }}
                            </span>
                            <span class="text-muted">{{ $notif->statut }} · {{ $notif->created_at->format('d/m H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

{{-- Modal changement de statut --}}
<div class="modal fade" id="modalStatut" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('ordres.update-statut', $ordre) }}" class="modal-content">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title">Changer le statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nouveau statut</label>
                <select name="statut" class="form-select mb-3" required>
                    @foreach(['devis','accepte','en_cours','termine','facture','livre','annule'] as $s)
                        <option value="{{ $s }}" @selected($ordre->statut === $s)>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
                <label class="form-label">Diagnostic / Notes</label>
                <textarea name="diagnostic" class="form-control" rows="2">{{ $ordre->diagnostic }}</textarea>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <label class="form-label">Acompte (F CFA)</label>
                        <input type="number" name="acompte" class="form-control"
                               value="{{ $ordre->acompte }}" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Mode de paiement</label>
                        <select name="mode_paiement" class="form-select">
                            <option value="">—</option>
                            @foreach(['especes','orange_money','wave','moov','virement'] as $m)
                                <option value="{{ $m }}" @selected($ordre->mode_paiement === $m)>
                                    {{ ucfirst(str_replace('_', ' ', $m)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
