<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0F2942">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion – Garage Moderne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gm-primary: #0F2942;
            --gm-secondary: #F97316;
            --gm-accent: #2A9D8F;
            --gm-bg: #F0F4F8;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(160deg, var(--gm-primary) 0%, #1a3a5a 50%, #0F2942 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            max-width: 400px;
            width: 100%;
            overflow: hidden;
        }
        .login-header {
            background: var(--gm-primary);
            color: #fff;
            padding: 1.75rem 1.5rem;
            text-align: center;
        }
        .login-header .brand-icon {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,.15);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: .75rem;
        }
        .login-body { padding: 1.75rem 1.5rem; }
        .btn-login {
            background: var(--gm-primary);
            border-color: var(--gm-primary);
            font-weight: 600;
            padding: .65rem;
            border-radius: 10px;
        }
        .btn-login:hover {
            background: #0a1d30;
            border-color: #0a1d30;
        }
        .form-control {
            border-radius: 10px;
            padding: .65rem .9rem;
        }
        .form-control:focus {
            border-color: var(--gm-accent);
            box-shadow: 0 0 0 .2rem rgba(42,157,143,.25);
        }
        .form-check-input:checked {
            background-color: var(--gm-primary);
            border-color: var(--gm-primary);
        }
        .login-footer {
            text-align: center;
            font-size: .8rem;
            color: #6c757d;
            padding: 0 1.5rem 1.25rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon">
                <i class="bi bi-wrench-adjustable-circle"></i>
            </div>
            <h1 class="h4 mb-0 fw-bold">Garage Moderne</h1>
            <p class="mb-0 mt-1 opacity-75 small">Gestion d'atelier automobile</p>
        </div>

        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ url('/login') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-medium small">Adresse e-mail</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-envelope text-muted"></i>
                        </span>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control border-start-0 @error('email') is-invalid @enderror"
                            value="{{ old('email', 'admin@garage-moderne.local') }}"
                            placeholder="admin@garage-moderne.local"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium small">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-lock text-muted"></i>
                        </span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control border-start-0 @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                </button>
            </form>
        </div>

        <div class="login-footer">
            Compte démo : <strong>admin@garage-moderne.local</strong> / <strong>password</strong>
        </div>
    </div>
</body>
</html>
