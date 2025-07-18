<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Tentative de connexion
        if (Auth::attempt($credentials)) {
            // Vérifier si l'utilisateur est actif
            if (!auth()->user()->is_active) {
                // Déconnecter l'utilisateur
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.'
                ])->onlyInput('email');
            }

            // Si l'utilisateur est actif, régénérer la session
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Email ou mot de passe invalide'
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Logout method
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
