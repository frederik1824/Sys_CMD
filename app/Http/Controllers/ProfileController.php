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
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfWeek = $now->copy()->startOfWeek();

        $stats = [
            // Core
            'entregados_hoy' => 0,
            'pendientes' => 0,
            'efectividad' => 0,
            'completados_mes' => 0,
            'completados_semana' => 0,
            'tiempo_promedio_min' => 0,
            'sla_cumplimiento' => 0,
            'rechazados_mes' => 0,
            'tendencia_semanal' => [], 
            
            // Role Flags
            'is_callcenter' => $user->hasRole('Gestor de Llamadas') || $user->hasRole('Representante'),
            'is_supervisor' => $user->hasRole('Supervisor') || $user->hasRole('Coordinador'),
            'is_analyst'    => $user->hasRole('Analista') || $user->hasRole('Admin'),
            
            // Gamification & Status
            'nivel_operativo' => 'Analista Jr.',
            'puntos_experiencia' => 0,

            // Ponchador / Asistencia (Call Center)
            'ponchador' => [
                'hora_entrada' => null,
                'estado_turno' => 'Inactivo', // Activo, Pausa, Terminado
                'horas_trabajadas_hoy' => 0,
                'tiempo_en_pausa' => 0, // minutos
            ]
        ];

        // Lógica de Ponchador (Mockup hasta conexión con DB real)
        if ($stats['is_callcenter']) {
            // Simulamos que el representante entró hace X horas hoy
            $horaEntrada = now()->startOfDay()->addHours(8); // 8:00 AM
            if (now()->format('H') >= 8) {
                $stats['ponchador']['hora_entrada'] = $horaEntrada->format('h:i A');
                $stats['ponchador']['estado_turno'] = 'Activo';
                $stats['ponchador']['horas_trabajadas_hoy'] = round(now()->diffInMinutes($horaEntrada) / 60, 1);
            }
        }

        // Consulta base para solicitudes asignadas a este usuario
        $baseQuery = \App\Models\SolicitudAfiliacion::where('asignado_user_id', $user->id);

        if ($stats['is_supervisor']) {
            // ==========================================
            // SUPERVISOR: Ve métricas de todo su equipo
            // ==========================================
            $stats['nivel_operativo'] = 'Supervisor Master';
            $stats['entregados_hoy'] = \App\Models\SolicitudAfiliacion::where('estado', 'Completada')->whereDate('updated_at', today())->count();
            $stats['pendientes'] = \App\Models\SolicitudAfiliacion::whereNotIn('estado', ['Completada', 'Rechazada', 'Cancelada'])->count();
            $stats['completados_mes'] = \App\Models\SolicitudAfiliacion::where('estado', 'Completada')->where('updated_at', '>=', $startOfMonth)->count();
            
            // Efectividad global
            $totalMesGlobal = \App\Models\SolicitudAfiliacion::whereIn('estado', ['Completada', 'Rechazada'])->where('updated_at', '>=', $startOfMonth)->count();
            $stats['rechazados_mes'] = \App\Models\SolicitudAfiliacion::where('estado', 'Rechazada')->where('updated_at', '>=', $startOfMonth)->count();
            $stats['efectividad'] = $totalMesGlobal > 0 ? round(($stats['completados_mes'] / $totalMesGlobal) * 100, 1) : 0;
            
            $stats['tiempo_promedio_min'] = 15; // Global SLA
            $stats['sla_cumplimiento'] = 92; // Global SLA %

            // Tendencia Semanal Global
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $stats['tendencia_semanal'][] = [
                    'day' => now()->subDays($i)->translatedFormat('D'),
                    'count' => \App\Models\SolicitudAfiliacion::where('estado', 'Completada')->whereDate('updated_at', $date)->count()
                ];
            }

        } elseif ($stats['is_callcenter']) {
            // ==========================================
            // CALL CENTER / REPRESENTANTE
            // ==========================================
            $stats['nivel_operativo'] = 'Representante N1';
            
            $stats['entregados_hoy'] = \App\Models\CallCenterGestion::where('operador_id', $user->id)->whereDate('created_at', today())->count();
            
            $totalAsignados = \App\Models\CallCenterRegistro::where('operador_id', $user->id)->count();
            $stats['pendientes'] = \App\Models\CallCenterRegistro::where('operador_id', $user->id)->whereDoesntHave('bandejaSalida')->count();
            
            $promocionesMes = \App\Models\CallCenterRegistro::where('operador_id', $user->id)
                ->whereHas('bandejaSalida', function($q) use ($startOfMonth) {
                    $q->where('created_at', '>=', $startOfMonth);
                })->count();
                
            $stats['efectividad'] = $totalAsignados > 0 ? round(($promocionesMes / $totalAsignados) * 100, 1) : 0;
            
            $stats['completados_mes'] = \App\Models\CallCenterGestion::where('operador_id', $user->id)->where('created_at', '>=', $startOfMonth)->count();
            $stats['puntos_experiencia'] = $stats['completados_mes'] * 5;

            // Tendencia Semanal Call Center (Gestiones por día)
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $stats['tendencia_semanal'][] = [
                    'day' => now()->subDays($i)->translatedFormat('D'),
                    'count' => \App\Models\CallCenterGestion::where('operador_id', $user->id)->whereDate('created_at', $date)->count()
                ];
            }

        } else {
            // ==========================================
            // ANALISTA / OPERACIONES (Por defecto)
            // ==========================================
            $stats['entregados_hoy'] = (clone $baseQuery)->where('estado', 'Completada')->whereDate('updated_at', today())->count();
            $stats['pendientes'] = (clone $baseQuery)->whereNotIn('estado', ['Completada', 'Rechazada', 'Cancelada'])->count();
            $stats['completados_mes'] = (clone $baseQuery)->where('estado', 'Completada')->where('updated_at', '>=', $startOfMonth)->count();
            $stats['completados_semana'] = (clone $baseQuery)->where('estado', 'Completada')->where('updated_at', '>=', $startOfWeek)->count();
            $stats['rechazados_mes'] = (clone $baseQuery)->where('estado', 'Rechazada')->where('updated_at', '>=', $startOfMonth)->count();

            // Cálculo de Efectividad
            $totalMes = (clone $baseQuery)->whereIn('estado', ['Completada', 'Rechazada'])->where('updated_at', '>=', $startOfMonth)->count();
            $stats['efectividad'] = $totalMes > 0 ? round(($stats['completados_mes'] / $totalMes) * 100, 1) : 0;

            // Tiempo Promedio de Resolución
            $tiempos = (clone $baseQuery)->where('estado', 'Completada')
                ->whereNotNull('fecha_cierre')
                ->where('updated_at', '>=', $startOfMonth)
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, fecha_cierre)) as avg_min')
                ->first();
            $stats['tiempo_promedio_min'] = round($tiempos->avg_min ?? 0);

            // Cumplimiento de SLA
            $totalesConSLA = (clone $baseQuery)->whereNotNull('sla_fecha_limite')->where('estado', 'Completada')->count();
            $enSLA = (clone $baseQuery)->whereNotNull('sla_fecha_limite')
                ->where('estado', 'Completada')
                ->whereRaw('fecha_cierre <= sla_fecha_limite')
                ->count();
            $stats['sla_cumplimiento'] = $totalesConSLA > 0 ? round(($enSLA / $totalesConSLA) * 100, 1) : 100;

            // Gamificación
            $stats['puntos_experiencia'] = ($stats['completados_mes'] * 10) - ($stats['rechazados_mes'] * 5);
            if ($stats['puntos_experiencia'] < 0) $stats['puntos_experiencia'] = 0;

            if ($stats['puntos_experiencia'] > 500) $stats['nivel_operativo'] = 'Analista Senior';
            elseif ($stats['puntos_experiencia'] > 200) $stats['nivel_operativo'] = 'Analista Pro';
            else $stats['nivel_operativo'] = 'Analista Jr.';
            
            // Tendencia Semanal
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $stats['tendencia_semanal'][] = [
                    'day' => now()->subDays($i)->translatedFormat('D'),
                    'count' => (clone $baseQuery)->where('estado', 'Completada')->whereDate('updated_at', $date)->count()
                ];
            }
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

        // Obtener la versión actual del sistema
        $latestUpdate = \App\Models\SystemUpdate::latest()->first();
        $systemVersion = $latestUpdate ? $latestUpdate->version : '3.0.0';

        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
            'systemVersion' => $systemVersion,
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
