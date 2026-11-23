@extends('layouts.app')

@section('title', 'Tableau de bord – Garage Moderne')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 fw-bold text-primary-gm">Tableau de bord</h1>
    <span class="text-muted small">{{ now()->translatedFormat('l d F Y H:i') }}</span>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">En cours</div>
                <div class="fs-3 fw-bold text-primary-gm">{{ $ordresEnCours }}</div>
                <div class="small text-muted">véhicules</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Aujourd'hui</div>
                <div class="fs-3 fw-bold">{{ $ordresDuJour }}</div>
                <div class="small text-muted">ordres</div>
            </div>
        </div>
    </div>
    @if($peutVoirFinancier ?? false)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">CA du jour</div>
                <div class="fs-5 fw-bold text-accent">
                    {{ number_format($caDuJour ?? 0, 0, ',', ' ') }}
                </div>
                <div class="small text-muted">F CFA</div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card card-kpi h-100 {{ $stockCritique > 0 ? 'border border-warning' : '' }}">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Stock critique</div>
                <div class="fs-3 fw-bold {{ $stockCritique > 0 ? 'text-warning' : '' }}">
                    {{ $stockCritique }}
                </div>
                <div class="small text-muted">pièces</div>
            </div>
        </div>
    </div>
    @if($peutVoirFinancier ?? false)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card card-kpi h-100">
            <div class="card-body py-3">
                <div class="text-muted small mb-1">Crédits clients</div>
                <div class="fs-5 fw-bold text-danger">
                    {{ number_format($creditsClients ?? 0, 0, ',', ' ') }}
                </div>
                <div class="small text-muted">F CFA</div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-4 col-xl-2 d-flex align-items-stretch">
        <a href="{{ route('ordres.create') }}" class="card card-kpi h-100 text-decoration-none w-100">
            <div class="card-body py-3 d-flex flex-column justify-content-center align-items-center">
                <i class="bi bi-plus-circle-fill text-secondary fs-2"></i>
                <div class="small fw-semibold text-primary-gm mt-1">Nouvel ordre</div>
            </div>
        </a>
    </div>
</div>

{{-- Graphiques : couleurs & stock --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-pie-chart me-1"></i> Ordres par statut
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartStatuts" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-palette me-1"></i> Véhicules par couleur
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartCouleurs" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-box-seam me-1"></i> Stock par catégorie
            </div>
            <div class="card-body">
                <canvas id="chartStock" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Derniers ordres --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Derniers ordres</span>
        <a href="{{ route('ordres.index') }}" class="small">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-mobile mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Client / Véhicule</th>
                        <th class="d-none d-md-table-cell">Date</th>
                        <th>Statut</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersOrdres as $ordre)
                        <tr onclick="window.location='{{ route('ordres.show', $ordre) }}'" style="cursor:pointer">
                            <td class="fw-semibold">{{ $ordre->numero }}</td>
                            <td>
                                <div class="fw-medium">{{ $ordre->client->nom_complet ?? '—' }}</div>
                                <div class="small text-muted">
                                    {{ $ordre->vehicule->immatriculation ?? '' }}
                                    · {{ $ordre->vehicule->marque ?? '' }} {{ $ordre->vehicule->modele ?? '' }}
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell small">
                                {{ $ordre->date_entree?->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @include('partials.statut-badge', ['statut' => $ordre->statut])
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($ordre->montant_ttc, 0, ',', ' ') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucun ordre pour le moment.
                                <a href="{{ route('ordres.create') }}">Créer le premier</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const palette = ['#0F2942', '#F97316', '#2A9D8F', '#E76F51', '#6C757D', '#198754', '#0D6EFD', '#6F42C1'];

    // Ordres par statut
    const statuts = @json($repartitionStatuts ?? []);
    const statutLabels = Object.keys(statuts);
    const statutValues = Object.values(statuts);
    new Chart(document.getElementById('chartStatuts'), {
        type: 'doughnut',
        data: {
            labels: statutLabels.length ? statutLabels : ['Aucun'],
            datasets: [{ data: statutValues.length ? statutValues : [1], backgroundColor: palette }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Véhicules par couleur
    const couleurs = @json($repartitionCouleurs ?? []);
    const cLabels = Object.keys(couleurs);
    const cValues = Object.values(couleurs);
    new Chart(document.getElementById('chartCouleurs'), {
        type: 'pie',
        data: {
            labels: cLabels.length ? cLabels : ['Aucune donnée'],
            datasets: [{ data: cValues.length ? cValues : [1], backgroundColor: palette }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Stock par catégorie
    const stockCats = @json($stockParCategorie ?? []);
    const sLabels = stockCats.map(function (s) { return s.categorie || 'Sans catégorie'; });
    const sValues = stockCats.map(function (s) { return Number(s.total); });
    new Chart(document.getElementById('chartStock'), {
        type: 'bar',
        data: {
            labels: sLabels.length ? sLabels : ['Aucun'],
            datasets: [{ label: 'Pièces', data: sValues.length ? sValues : [0], backgroundColor: '#0F2942' }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
})();
</script>
@endpush
@endsection
