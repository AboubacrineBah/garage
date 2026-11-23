@extends('layouts.app')

@section('title', 'Mouvement de stock')

@section('content')
@php
    $titres = [
        'entree'     => 'Entrée de stock',
        'sortie'     => 'Sortie de stock',
        'ajustement' => 'Ajustement de stock',
        'inventaire' => 'Inventaire',
    ];
    $couleurs = [
        'entree'     => 'success',
        'sortie'     => 'danger',
        'ajustement' => 'warning',
        'inventaire' => 'info',
    ];
@endphp

<div class="mb-3">
    <a href="{{ route('stock.index') }}" class="text-decoration-none small text-muted">
        <i class="bi bi-arrow-left"></i> Stock
    </a>
    <h1 class="h4 fw-bold text-primary-gm mt-1">
        {{ $titres[$type] ?? 'Mouvement de stock' }}
    </h1>
</div>

<form method="POST" action="{{ route('stock.mouvement.store') }}" class="card border-0 shadow-sm">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Pièce *</label>
            <select name="piece_id" id="piece_id" class="form-select" required>
                <option value="">— Sélectionner une pièce —</option>
                @foreach($pieces as $p)
                    <option value="{{ $p->id }}"
                            data-stock="{{ $p->stock_actuel }}"
                            data-unite="{{ $p->unite }}"
                            @selected(old('piece_id', $pieceId) == $p->id)>
                        {{ $p->reference }} — {{ $p->designation }}
                        (stock: {{ number_format($p->stock_actuel, 0, ',', ' ') }} {{ $p->unite }})
                    </option>
                @endforeach
            </select>
            @error('piece_id') <div class="text-danger small">{{ $message }}</div> @enderror
            <div class="form-text" id="stock-info"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">
                @if($type === 'ajustement')
                    Nouveau stock *
                @else
                    Quantité *
                @endif
            </label>
            <input type="number" name="quantite" class="form-control" 
                   value="{{ old('quantite') }}" min="0.01" step="0.01" required>
            @error('quantite') <div class="text-danger small">{{ $message }}</div> @enderror
            @if($type === 'sortie')
                <div class="form-text text-muted">Le stock ne pourra pas devenir négatif.</div>
            @endif
            @if($type === 'ajustement')
                <div class="form-text text-muted">Indiquez le stock réel constaté (inventaire).</div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Commentaire</label>
            <input type="text" name="commentaire" class="form-control"
                   value="{{ old('commentaire') }}"
                   placeholder="Ex: Livraison fournisseur X, casse, inventaire...">
        </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-end gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-{{ $couleurs[$type] ?? 'primary' }}">
            Enregistrer le mouvement
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('piece_id')?.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const info = document.getElementById('stock-info');
    if (opt && opt.dataset.stock !== undefined) {
        info.textContent = 'Stock actuel : ' + opt.dataset.stock + ' ' + (opt.dataset.unite || '');
    } else {
        info.textContent = '';
    }
});
// Trigger on load if preselected
document.getElementById('piece_id')?.dispatchEvent(new Event('change'));
</script>
@endpush
