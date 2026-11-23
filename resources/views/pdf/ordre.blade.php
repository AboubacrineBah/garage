<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $titre }} {{ $numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1A1A2E;
            line-height: 1.4;
        }
        .page { padding: 25px 30px; }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px solid #0D3B66;
            padding-bottom: 12px;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .header-right { text-align: right; }
        .logo-text {
            font-size: 18px;
            font-weight: bold;
            color: #0D3B66;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #0D3B66;
            text-transform: uppercase;
        }
        .doc-numero {
            font-size: 13px;
            color: #F4A261;
            font-weight: bold;
        }
        .section {
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #6C757D;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .box {
            background: #F8F9FA;
            padding: 10px 12px;
            border-radius: 4px;
        }
        .two-cols {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .col {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        .col + .col { padding-left: 4%; }
        table.lignes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.lignes th {
            background: #0D3B66;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.lignes th.right, table.lignes td.right {
            text-align: right;
        }
        table.lignes td {
            padding: 6px 8px;
            border-bottom: 1px solid #e9ecef;
        }
        table.lignes tr:nth-child(even) td {
            background: #F8F9FA;
        }
        .totaux {
            width: 280px;
            margin-left: auto;
            margin-bottom: 20px;
        }
        .totaux table {
            width: 100%;
            border-collapse: collapse;
        }
        .totaux td {
            padding: 4px 8px;
        }
        .totaux .label { text-align: right; color: #6C757D; }
        .totaux .value { text-align: right; font-weight: bold; }
        .totaux .total-row td {
            border-top: 2px solid #0D3B66;
            font-size: 13px;
            color: #0D3B66;
            padding-top: 8px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #6C757D;
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-devis { background: #e7f1ff; color: #0D3B66; }
        .badge-facture { background: #d1e7dd; color: #0f5132; }
        .notes {
            font-size: 10px;
            color: #6C757D;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<div class="page">
    {{-- En-tête --}}
    <div class="header">
        <div class="header-left">
            <div class="logo-text">{{ $atelier['nom'] }}</div>
            <div>{{ $atelier['adresse'] }}</div>
            <div>{{ $atelier['telephone'] }}</div>
            @if(!empty($atelier['email']))
                <div>{{ $atelier['email'] }}</div>
            @endif
            @if(!empty($atelier['nif']))
                <div>NIF : {{ $atelier['nif'] }}</div>
            @endif
        </div>
        <div class="header-right">
            <div class="doc-title">{{ $titre }}</div>
            <div class="doc-numero">{{ $numero }}</div>
            <div style="margin-top:6px;">
                <span class="badge {{ $type === 'facture' ? 'badge-facture' : 'badge-devis' }}">
                    {{ $type === 'facture' ? 'Facture' : 'Devis' }}
                </span>
            </div>
            <div style="margin-top:8px; font-size:10px;">
                Date : {{ $ordre->date_entree?->format('d/m/Y') ?? now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- Client & Véhicule --}}
    <div class="two-cols">
        <div class="col">
            <div class="section-title">Client</div>
            <div class="box">
                <strong>{{ $ordre->client->nom_complet ?? '—' }}</strong><br>
                {{ $ordre->client->telephone ?? '' }}<br>
                @if($ordre->client->email ?? false)
                    {{ $ordre->client->email }}<br>
                @endif
                @if($ordre->client->adresse ?? false)
                    {{ $ordre->client->adresse }}
                @endif
            </div>
        </div>
        <div class="col">
            <div class="section-title">Véhicule</div>
            <div class="box">
                <strong>{{ $ordre->vehicule->immatriculation ?? '—' }}</strong><br>
                {{ $ordre->vehicule->marque ?? '' }} {{ $ordre->vehicule->modele ?? '' }}<br>
                Kilométrage : {{ number_format($ordre->kilometrage_entree ?? 0, 0, ',', ' ') }} km<br>
                @if($ordre->vehicule->type_carburant ?? false)
                    Carburant : {{ ucfirst($ordre->vehicule->type_carburant) }}
                @endif
            </div>
        </div>
    </div>

    {{-- Travaux --}}
    @if($ordre->description_travaux)
        <div class="section">
            <div class="section-title">Travaux / Symptômes</div>
            <div class="box">{{ $ordre->description_travaux }}</div>
        </div>
    @endif

    {{-- Lignes --}}
    <table class="lignes">
        <thead>
            <tr>
                <th style="width:8%">Type</th>
                <th>Désignation</th>
                <th class="right" style="width:10%">Qté</th>
                <th class="right" style="width:15%">P.U. HT</th>
                <th class="right" style="width:10%">Remise</th>
                <th class="right" style="width:15%">Montant HT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ordre->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->type_ligne }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td class="right">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                    <td class="right">{{ number_format($ligne->prix_unitaire_ht, 0, ',', ' ') }}</td>
                    <td class="right">{{ $ligne->remise_pct > 0 ? number_format($ligne->remise_pct, 0).' %' : '—' }}</td>
                    <td class="right">{{ number_format($ligne->montant_ht, 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#6C757D;">Aucune ligne</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Totaux --}}
    <div class="totaux">
        <table>
            <tr>
                <td class="label">Total HT</td>
                <td class="value">{{ number_format($ordre->montant_ht, 0, ',', ' ') }} F CFA</td>
            </tr>
            @if($ordre->montant_tva > 0)
                <tr>
                    <td class="label">TVA</td>
                    <td class="value">{{ number_format($ordre->montant_tva, 0, ',', ' ') }} F CFA</td>
                </tr>
            @endif
            <tr>
                <td class="label">Acompte</td>
                <td class="value">{{ number_format($ordre->acompte, 0, ',', ' ') }} F CFA</td>
            </tr>
            <tr class="total-row">
                <td class="label">{{ $type === 'facture' ? 'Net à payer' : 'Total TTC' }}</td>
                <td class="value">{{ number_format($ordre->reste_a_payer > 0 ? $ordre->reste_a_payer : $ordre->montant_ttc, 0, ',', ' ') }} F CFA</td>
            </tr>
        </table>
    </div>

    @if($type === 'devis')
        <div class="notes">
            Ce devis est valable 15 jours à compter de sa date d’émission.
            Les prix sont exprimés en Francs CFA (XOF).
        </div>
    @endif

    @if($type === 'facture' && $ordre->mode_paiement)
        <div class="notes">
            Mode de paiement : {{ str_replace('_', ' ', ucfirst($ordre->mode_paiement)) }}
        </div>
    @endif

    <div class="footer">
        {{ $atelier['nom'] }} — {{ $atelier['adresse'] }} — {{ $atelier['telephone'] }}<br>
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>
</body>
</html>
