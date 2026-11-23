<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Authentification simple (login / logout) pour Garage Moderne.
 * Compatible Laravel 10/11 sans Breeze.
 */
class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Identifiants incorrects. Vérifiez votre e-mail et votre mot de passe.',
                ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Compte désactivé (si la colonne existe)
        if ($user && Schema::hasColumn('users', 'is_active') && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Votre compte a été désactivé. Contactez le gérant.',
                ]);
        }

        $request->session()->regenerate();

        // Mise à jour last_login_at uniquement si la colonne existe (évite l'erreur SQL)
        if ($user && Schema::hasColumn('users', 'last_login_at')) {
            $user->last_login_at = now();
            $user->save();
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Déconnexion.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
