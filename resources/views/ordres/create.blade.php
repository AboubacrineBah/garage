@extends('layouts.app')

@section('title', 'Nouvel ordre de réparation')

@section('content')
<div class="mb-3">
    <a href="{{ route('ordres.index') }}" class="text-decoration-none small text-muted">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
    <h1 class="h4 fw-bold text-primary-gm mt-1">Nouvel ordre de réparation</h1>
</div>

<form method="POST" action="{{ route('ordres.store') }}" id="form-ordre">
    @csrf

    {{-- Étape 1 : Client --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">
            <span class="badge bg-primary me-1">1</span> Client
        </div>
        <div class="card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="nouveau_client" name="nouveau_client" value="1"
                       {{ old('nouveau_client') ? 'checked' : '' }}>
                <label class="form-check-label" for="nouveau_client">Nouveau client</label>
            </div>

            <div id="bloc-client-existant" class="{{ old('nouveau_client') ? 'd-none' : '' }}">
                <label class="form-label">Sélectionner un client</label>
                <select name="client_id" class="form-select">
                    <option value="">— Choisir —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" @selected(old('client_id') == $c->id)>
                            {{ $c->nom }} {{ $c->prenom }} — {{ $c->telephone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="bloc-nouveau-client" class="{{ old('nouveau_client') ? '' : 'd-none' }}">
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Nom / Raison sociale *</label>
                        <input type="text" name="client_nom" class="form-control"
                               value="{{ old('client_nom') }}" placeholder="Diallo">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="client_prenom" class="form-control"
                               value="{{ old('client_prenom') }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Téléphone *</label>
                        <input type="tel" name="client_telephone" class="form-control"
                               value="{{ old('client_telephone') }}" placeholder="70 00 00 00">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Type</label>
                        <select name="client_type" class="form-select">
                            <option value="particulier" @selected(old('client_type') === 'particulier')>Particulier</option>
                            <option value="entreprise" @selected(old('client_type') === 'entreprise')>Entreprise</option>
                        </select>
                    </div>
                </div>
            </div>
            @error('client_nom') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            @error('client_telephone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Étape 2 : Véhicule --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">
            <span class="badge bg-primary me-1">2</span> Véhicule
        </div>
        <div class="card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="nouveau_vehicule" name="nouveau_vehicule" value="1" checked>
                <label class="form-check-label" for="nouveau_vehicule">Nouveau véhicule</label>
            </div>

            <div class="row g-2">
                <div class="col-6 col-md-4">
                    <label class="form-label">Immatriculation *</label>
                    <input type="text" name="immatriculation" class="form-control text-uppercase"
                           value="{{ old('immatriculation') }}" placeholder="AA-123-BB" required>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Marque *</label>
                    <input type="text" name="marque" class="form-control"
                           value="{{ old('marque') }}" placeholder="Toyota" required>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Modèle *</label>
                    <input type="text" name="modele" class="form-control"
                           value="{{ old('modele') }}" placeholder="Corolla" required>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Kilométrage *</label>
                    <input type="number" name="kilometrage_entree" class="form-control"
                           value="{{ old('kilometrage_entree') }}" min="0" required>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Carburant</label>
                    <select name="type_carburant" class="form-select">
                        @foreach(['essence','diesel','hybride','electrique','gpl'] as $c)
                            <option value="{{ $c }}" @selected(old('type_carburant', 'essence') === $c)>
                                {{ ucfirst($c) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Année</label>
                    <input type="number" name="annee" class="form-control"
                           value="{{ old('annee') }}" min="1980" max="{{ date('Y')+1 }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Étape 3 : Travaux --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">
            <span class="badge bg-primary me-1">3</span> Travaux demandés
        </div>
        <div class="card-body">
            <label class="form-label">Description / Symptômes *</label>
            <textarea name="description_travaux" class="form-control" rows="3"
                      placeholder="Ex: Bruit au freinage, vidange à faire..." required>{{ old('description_travaux') }}</textarea>
            @error('description_travaux') <div class="text-danger small">{{ $message }}</div> @enderror

            <div class="row g-2 mt-2">
                <div class="col-12 col-md-6">
                    <label class="form-label">Date de sortie prévue</label>
                    <input type="date" name="date_sortie_prevue" class="form-control"
                           value="{{ old('date_sortie_prevue') }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Mécanicien(s)</label>
                    <select name="mecaniciens_ids[]" class="form-select" multiple size="3">
                        @foreach($mecaniciens as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Maintenir Ctrl/Cmd pour multi-sélection</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Étape 4 : Lignes (optionnel à la création) --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between">
            <span><span class="badge bg-primary me-1">4</span> Pièces & Main-d'œuvre</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-ligne">
                <i class="bi bi-plus"></i> Ajouter
            </button>
        </div>
        <div class="card-body" id="lignes-container">
            <p class="text-muted small mb-0" id="no-lignes-msg">
                Aucune ligne pour le moment. Vous pourrez en ajouter plus tard.
            </p>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
        <a href="{{ route('ordres.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check2-circle"></i> Créer le devis
        </button>
    </div>
</form>

{{-- Template ligne (JS) — avec sélecteur de pièce stock --}}
<template id="template-ligne">
    <div class="border rounded p-2 mb-2 ligne-item">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small">Type</label>
                <select name="lignes[__INDEX__][type_ligne]" class="form-select form-select-sm type-ligne">
                    <option value="piece">Pièce</option>
                    <option value="main_oeuvre">Main-d'œuvre</option>
                    <option value="forfait">Forfait</option>
                    <option value="autre">Autre</option>
                </select>
            </div>
            <div class="col-12 col-md-3 bloc-piece">
                <label class="form-label small">Pièce stock</label>
                <select name="lignes[__INDEX__][piece_id]" class="form-select form-select-sm select-piece">
                    <option value="">— Saisie libre —</option>
                    @foreach($pieces as $p)
                        <option value="{{ $p->id }}"
                                data-designation="{{ $p->designation }}"
                                data-prix="{{ $p->prix_vente }}"
                                data-stock="{{ $p->stock_actuel }}">
                            {{ $p->reference }} — {{ $p->designation }}
                            ({{ number_format($p->stock_actuel, 0, ',', ' ') }} {{ $p->unite }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small">Désignation</label>
                <input type="text" name="lignes[__INDEX__][designation]" class="form-control form-control-sm input-designation" required>
            </div>
            <div class="col-4 col-md-1">
                <label class="form-label small">Qté</label>
                <input type="number" name="lignes[__INDEX__][quantite]" class="form-control form-control-sm"
                       value="1" min="0.01" step="0.01" required>
            </div>
            <div class="col-4 col-md-1">
                <label class="form-label small">P.U.</label>
                <input type="number" name="lignes[__INDEX__][prix_unitaire_ht]" class="form-control form-control-sm input-prix"
                       value="0" min="0" step="1" required>
            </div>
            <div class="col-4 col-md-1">
                <label class="form-label small">Rem.%</label>
                <input type="number" name="lignes[__INDEX__][remise_pct]" class="form-control form-control-sm"
                       value="0" min="0" max="100">
            </div>
            <div class="col-12 col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ligne">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="small text-muted stock-hint d-none"></div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chkClient = document.getElementById('nouveau_client');
    const blocExistant = document.getElementById('bloc-client-existant');
    const blocNouveau = document.getElementById('bloc-nouveau-client');

    chkClient.addEventListener('change', function () {
        blocExistant.classList.toggle('d-none', this.checked);
        blocNouveau.classList.toggle('d-none', !this.checked);
    });

    let ligneIndex = 0;
    const container = document.getElementById('lignes-container');
    const noMsg = document.getElementById('no-lignes-msg');
    const tpl = document.getElementById('template-ligne');

    function bindLigneEvents(row) {
        const selectPiece = row.querySelector('.select-piece');
        const inputDes = row.querySelector('.input-designation');
        const inputPrix = row.querySelector('.input-prix');
        const hint = row.querySelector('.stock-hint');
        const typeSelect = row.querySelector('.type-ligne');
        const blocPiece = row.querySelector('.bloc-piece');

        selectPiece.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                inputDes.value = opt.dataset.designation || '';
                inputPrix.value = opt.dataset.prix || 0;
                hint.textContent = 'Stock disponible : ' + (opt.dataset.stock || '0');
                hint.classList.remove('d-none');
            } else {
                hint.classList.add('d-none');
            }
        });

        typeSelect.addEventListener('change', function () {
            const isPiece = this.value === 'piece';
            blocPiece.classList.toggle('d-none', !isPiece);
            if (!isPiece) {
                selectPiece.value = '';
                hint.classList.add('d-none');
            }
        });
    }

    document.getElementById('btn-add-ligne').addEventListener('click', function () {
        noMsg.classList.add('d-none');
        const html = tpl.innerHTML.replaceAll('__INDEX__', ligneIndex++);
        container.insertAdjacentHTML('beforeend', html);
        const row = container.querySelector('.ligne-item:last-child');
        bindLigneEvents(row);
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-ligne')) {
            e.target.closest('.ligne-item').remove();
            if (!container.querySelector('.ligne-item')) {
                noMsg.classList.remove('d-none');
            }
        }
    });
});
</script>
@endpush
