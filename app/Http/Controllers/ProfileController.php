<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Get statistics for a specific user (used in profile and dashboard)
     */
    public static function getUserStats($user)
    {
        $stats = [
            'entregados_hoy' => 0,
            'pendientes' => 0,
            'efectividad' => 0,
            'is_callcenter' => $user->hasRole('Gestor de Llamadas'),
        ];

        if ($stats['is_callcenter']) {
            // Estadísticas de Call Center
            $stats['entregados_hoy'] = \App\Models\Llamada::where('usuario_id', $user->id)
                ->whereDate('fecha_llamada', today())
                ->count();

            $totalAsignados = \App\Models\AsignacionLlamada::where('usuario_id', $user->id)->count();
            $stats['pendientes'] = \App\Models\AsignacionLlamada::where('usuario_id', $user->id)
                ->where('activa', true)
                ->count();
            
            $stats['efectividad'] = $totalAsignados > 0 ? round(($stats['entregados_hoy'] / $totalAsignados) * 100, 1) : 0;
        } elseif ($user->responsable_id) {
            // Estadísticas de Logística (Existente)
            $stats['entregados_hoy'] = \App\Models\Afiliado::where('responsable_id', $user->responsable_id)
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->whereDate('updated_at', today())
                ->count();

            $totalAsignados = \App\Models\Afiliado::where('responsable_id', $user->responsable_id)->count();
            $stats['pendientes'] = \App\Models\Afiliado::where('responsable_id', $user->responsable_id)
                ->whereHas('estado', function($q) { $q->where('nombre', '!=', 'Completado'); })
                ->count();
            
            $stats['efectividad'] = $totalAsignados > 0 ? round(($stats['entregados_hoy'] / $totalAsignados) * 100, 1) : 0;
        }

        return $stats;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $stats = self::getUserStats($user);

        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email', 'phone', 'position']));

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
