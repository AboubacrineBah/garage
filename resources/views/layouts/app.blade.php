<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0F2942">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>@yield('title', 'Garage Moderne')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --gm-primary: #0F2942;
            --gm-primary-dark: #0a1d30;
            --gm-secondary: #F97316;
            --gm-accent: #2A9D8F;
            --gm-danger: #E76F51;
            --gm-bg: #F8F9FA;
            --gm-text: #1A1A2E;
            --gm-muted: #6C757D;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--gm-bg);
            color: var(--gm-text);
            margin: 0;
            min-height: 100vh;
        }

        /* ---------- Boutons / utilitaires ---------- */
        .btn-primary {
            background-color: var(--gm-primary) !important;
            border-color: var(--gm-primary) !important;
        }
        .btn-primary:hover { filter: brightness(1.1); }
        .btn-accent {
            background-color: var(--gm-secondary);
            border-color: var(--gm-secondary);
            color: #1A1A2E;
            font-weight: 600;
        }
        .btn-accent:hover { filter: brightness(1.05); color: #1A1A2E; }
        .text-primary-gm { color: var(--gm-primary) !important; }
        .text-accent { color: var(--gm-accent) !important; }
        .bg-primary-gm { background-color: var(--gm-primary) !important; }
        .card-kpi {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            transition: transform .15s;
        }
        .card-kpi:active { transform: scale(.98); }
        .statut-badge {
            font-size: .75rem;
            font-weight: 600;
            padding: .35em .65em;
            border-radius: 50rem;
        }
        .table-mobile td { vertical-align: middle; }

        /* ========== SIDEBAR DESKTOP ========== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--gm-primary);
            color: #fff;
            z-index: 1040;
            display: none; /* mobile : cachée */
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,.12);
            overflow: hidden; /* pas de barre de scroll sur la sidebar */
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.12);
            text-decoration: none;
            color: #fff;
        }
        .sidebar-brand:hover { color: #fff; }
        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .sidebar-brand-text {
            font-weight: 700;
            font-size: 1.05rem;
            line-height: 1.2;
        }
        .sidebar-brand-sub {
            font-size: .7rem;
            opacity: .7;
            font-weight: 400;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem .75rem;
            list-style: none;
            margin: 0;
            /* Masquer la barre de défilement (garder le scroll à la molette) */
            scrollbar-width: none;          /* Firefox */
            -ms-overflow-style: none;       /* IE / Edge legacy */
        }
        .sidebar-nav::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;                  /* Chrome, Safari, Edge */
        }
        .sidebar-nav .nav-section {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255,255,255,.45);
            padding: .75rem .75rem .35rem;
            font-weight: 600;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: .7rem .9rem;
            color: rgba(255,255,255,.8);
            text-decoration: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 500;
            margin-bottom: 2px;
            transition: background .15s, color .15s;
        }
        .sidebar-nav a i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            opacity: .9;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
        .sidebar-nav a.active {
            background: rgba(255,255,255,.18);
            color: #fff;
            font-weight: 600;
        }
        .sidebar-nav a.active i {
            color: var(--gm-secondary);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.12);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: .75rem;
        }
        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: .9rem;
        }
        .sidebar-user-name {
            font-size: .85rem;
            font-weight: 600;
            line-height: 1.2;
        }
        .sidebar-user-role {
            font-size: .7rem;
            opacity: .65;
        }

        /* ========== TOP BAR MOBILE ========== */
        .topbar-mobile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--gm-primary);
            color: #fff;
            padding: .75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .topbar-mobile .brand {
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ========== CONTENU PRINCIPAL ========== */
        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            padding: 1rem;
        }

        /* ========== BOTTOM NAV MOBILE ========== */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: #fff;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1030;
            padding-bottom: env(safe-area-inset-bottom);
        }
        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: .7rem;
            color: var(--gm-muted);
            text-decoration: none;
            padding: 6px 10px;
        }
        .bottom-nav a.active,
        .bottom-nav a:hover { color: var(--gm-primary); }
        .bottom-nav a i { font-size: 1.35rem; margin-bottom: 2px; }

        body.has-bottom-nav { padding-bottom: 72px; }

        /* ========== DESKTOP (≥ 768px) ========== */
        @media (min-width: 768px) {
            .sidebar { display: flex; }
            .topbar-mobile { display: none; }
            .bottom-nav { display: none; }
            body.has-bottom-nav { padding-bottom: 0; }

            .main-wrapper {
                margin-left: var(--sidebar-width);
            }
            .main-content {
                padding: 1.5rem 1.75rem;
            }

            /* Pas de topbar horizontale sur desktop : navigation = sidebar uniquement */
        }
    </style>
    @stack('styles')
</head>
<body class="has-bottom-nav">

    {{-- ========== SIDEBAR VERTICALE (desktop) ========== --}}
    <aside class="sidebar" aria-label="Navigation principale">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-wrench-adjustable-circle"></i>
            </div>
            <div>
                <div class="sidebar-brand-text">Garage Moderne</div>
                <div class="sidebar-brand-sub">Gestion d'atelier</div>
            </div>
        </a>

        <ul class="sidebar-nav">
            <li class="nav-section">Principal</li>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Tableau de bord</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ordres.index') }}" class="{{ request()->routeIs('ordres.index') || request()->routeIs('ordres.show') ? 'active' : '' }}">
                    <i class="bi bi-clipboard2-check"></i>
                    <span>Ordres de réparation</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ordres.create') }}" class="{{ request()->routeIs('ordres.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i>
                    <span>Nouvel ordre</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reception.inspection') }}" class="{{ request()->routeIs('reception.*') ? 'active' : '' }}">
                    <i class="bi bi-camera"></i>
                    <span>Réception 360°</span>
                </a>
            </li>

            <li class="nav-section">Référentiels</li>
            <li>
                <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Clients</span>
                </a>
            </li>
            <li>
                <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') && !request()->routeIs('stock.alertes') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Stock pièces</span>
                </a>
            </li>
            <li>
                <a href="{{ route('stock.alertes') }}" class="{{ request()->routeIs('stock.alertes') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Alertes stock</span>
                </a>
            </li>
            <li>
                <a href="{{ route('stock.mouvement') }}" class="{{ request()->routeIs('stock.mouvement*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Mouvement stock</span>
                </a>
            </li>
            <li>
                <a href="{{ route('stock.laboratoire') }}" class="{{ request()->routeIs('stock.laboratoire') ? 'active' : '' }}">
                    <i class="bi bi-palette"></i>
                    <span>Labo peinture</span>
                </a>
            </li>

            <li class="nav-section">Administration</li>
            <li>
                <a href="{{ route('parametres.index') }}" class="{{ request()->routeIs('parametres.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Paramètres atelier</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                    <div class="sidebar-user-role">
                        {{ auth()->user()->role->display_name ?? auth()->user()->role->name ?? 'Connecté' }}
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100 text-white"
                        style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:8px;">
                    <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- ========== ZONE PRINCIPALE ========== --}}
    <div class="main-wrapper">

        {{-- Top bar mobile --}}
        <div class="topbar-mobile">
            <a href="{{ route('dashboard') }}" class="brand">
                <i class="bi bi-wrench-adjustable-circle fs-5"></i>
                Garage Moderne
            </a>
            <a href="{{ route('ordres.create') }}" class="btn btn-accent btn-sm py-1 px-2">
                <i class="bi bi-plus-lg"></i>
            </a>
        </div>

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- ========== BOTTOM NAV MOBILE ========== --}}
    <nav class="bottom-nav d-md-none" aria-label="Navigation mobile">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Accueil</span>
        </a>
        <a href="{{ route('ordres.index') }}" class="{{ request()->routeIs('ordres.index') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-check"></i>
            <span>Ordres</span>
        </a>
        <a href="{{ route('ordres.create') }}" class="{{ request()->routeIs('ordres.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill" style="font-size:1.6rem;color:var(--gm-secondary)"></i>
            <span>Nouveau</span>
        </a>
        <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Clients</span>
        </a>
        <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Stock</span>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
