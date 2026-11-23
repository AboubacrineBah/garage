@extends('layouts.app')

@section('title', 'Nouvelle formulation de teinte')

@section('content')
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('stock.laboratoire') }}" class="btn btn-outline-secondary btn-sm me-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="h4 fw-bold text-primary-gm mb-0">Fiche de formulation de teinte</h1>
</div>

<form method="POST" action="{{ route('stock.formulation.store') }}">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Informations générales</div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="form-label">Nom de la formulation <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control" required maxlength="120" value="{{ old('nom') }}">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Code teinte constructeur</label>
                    <input type="text" name="code_teinte" class="form-control text-uppercase" maxlength="40" value="{{ old('code_teinte') }}">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Ordre de réparation</label>
                    <select name="ordre_id" class="form-select">
                        <option value="">— Aucun —</option>
                        @foreach($ordres as $o)
                            <option value="{{ $o->id }}" @selected(old('ordre_id') == $o->id)>{{ $o->numero }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between">
            <span><i class="bi bi-droplet me-1"></i> Composants (g / ml)</span>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addComp">
                <i class="bi bi-plus"></i> Ajouter
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="compTable">
                    <thead class="table-light">
                        <tr>
                            <th>Désignation</th>
                            <th>Pièce catalogue</th>
                            <th class="text-end">Quantité</th>
                            <th>Unité</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="compBody">
                        <tr class="comp-row">
                            <td><input type="text" name="composants[0][designation]" class="form-control form-control-sm" required></td>
                            <td>
                                <select name="composants[0][piece_id]" class="form-select form-select-sm">
                                    <option value="">—</option>
                                    @foreach($pieces as $p)
                                        <option value="{{ $p->id }}">{{ $p->reference }} – {{ $p->designation }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="composants[0][quantite]" class="form-control form-control-sm text-end" step="0.001" min="0" required></td>
                            <td>
                                <select name="composants[0][unite]" class="form-select form-select-sm">
                                    <option value="g">g</option>
                                    <option value="ml">ml</option>
                                    <option value="kg">kg</option>
                                    <option value="l">l</option>
                                </select>
                            </td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-comp"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('stock.laboratoire') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Enregistrer la formulation
        </button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    let idx = 1;
    const body = document.getElementById('compBody');
    const add = document.getElementById('addComp');
    add.addEventListener('click', function () {
        const row = body.querySelector('.comp-row').cloneNode(true);
        row.querySelectorAll('input, select').forEach(function (el) {
            const name = el.getAttribute('name');
            if (name) el.setAttribute('name', name.replace(/\[\d+\]/, '[' + idx + ']'));
            if (el.tagName === 'INPUT') el.value = '';
        });
        body.appendChild(row);
        idx++;
    });
    body.addEventListener('click', function (e) {
        if (e.target.closest('.remove-comp')) {
            const rows = body.querySelectorAll('.comp-row');
            if (rows.length > 1) e.target.closest('.comp-row').remove();
        }
    });
})();
</script>
@endpush
@endsection