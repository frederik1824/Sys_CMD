<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Evitar redirección a rutas de API o AJAX después del login
        $intended = session()->get('url.intended');
        if ($intended && (
            str_contains($intended, '/api/') || 
            str_contains($intended, '-ajax') || 
            str_contains($intended, '/sync-progress') ||
            str_contains($intended, '/sync-progress-now') ||
            str_contains($intended, '/check-duplicate')
        )) {
            session()->forget('url.intended');
        }

        return redirect()->intended(route('portal', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
