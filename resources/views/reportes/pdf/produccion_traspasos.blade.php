<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Detallado de Producción de Traspasos</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 9px; line-height: 1.4; }
        
        /* Header */
        .header { border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px; }
        .header table { width: 100%; }
        .logo { width: 100px; }
        .title { font-size: 16px; font-weight: bold; text-align: right; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { font-size: 9px; text-align: right; color: #64748b; }

        /* KPI Cards */
        .stats-grid { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 5px 0; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; text-align: center; }
        .stat-label { font-size: 7px; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 3px; }
        .stat-value { font-size: 14px; font-weight: bold; color: #0f172a; }
        .stat-hit { color: #4f46e5; }
        .stat-error { color: #e11d48; }

        /* Table Styles */
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.main-table th { background: #0f172a; color: white; padding: 6px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 7px; }
        table.main-table td { padding: 6px; border-bottom: 1px solid #f1f5f9; }
        .row-even { background-color: #ffffff; }
        .row-odd { background-color: #f8fafc; }

        /* Badges */
        .badge { padding: 2px 5px; border-radius: 3px; font-weight: bold; font-size: 7px; text-transform: uppercase; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0f2fe; color: #075985; }

        .page-break { page-break-after: always; }
        .section-title { font-size: 11px; font-weight: 900; margin-top: 20px; margin-bottom: 8px; border-left: 3px solid #0f172a; padding-left: 8px; color: #0f172a; text-transform: uppercase; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 7px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <img src="{{ public_path('img/Logo.png') }}" class="logo">
                    <p style="margin-top: 3px; font-weight: bold; font-size: 8px;">SYS_CARNET | Business Intelligence</p>
                </td>
                <td>
                    <div class="title">Producción de Traspasos</div>
                    <div class="subtitle">Periodo: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}</div>
                    <div class="subtitle">Generado: {{ now()->format('d/m/Y h:i A') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Resumen Ejecutivo -->
    <table class="stats-grid">
        <tr>
            <td class="stat-card" width="25%">
                <div class="stat-label">Solicitudes</div>
                <div class="stat-value">{{ number_format($stats['total_solicitudes']) }}</div>
            </td>
            <td class="stat-card" width="25%">
                <div class="stat-label">Efectivos</div>
                <div class="stat-value stat-hit">{{ number_format($stats['total_efectivos']) }}</div>
            </td>
            <td class="stat-card" width="25%">
                <div class="stat-label">Rechazos</div>
                <div class="stat-value stat-error">{{ number_format($stats['total_rechazados']) }}</div>
            </td>
            <td class="stat-card" width="25%">
                <div class="stat-label">Eficiencia (HR)</div>
                <div class="stat-value">{{ $stats['hit_rate_promedio'] }}%</div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Resumen de Desempeño por Agente</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">RK</th>
                <th width="35%">Agente / Responsable</th>
                <th width="10%" style="text-align: center;">Efect.</th>
                <th width="10%" style="text-align: center;">Vidas</th>
                <th width="10%" style="text-align: center;">Rech.</th>
                <th width="10%" style="text-align: center;">Pend.</th>
                <th width="20%" style="text-align: right;">Efectividad</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($rankingAgentes as $ag)
            <tr class="{{ $i % 2 == 0 ? 'row-even' : 'row-odd' }}">
                <td style="text-align: center;">{{ $i++ }}</td>
                <td style="font-weight: bold;">{{ $ag->agenteRel->nombre ?? 'N/A' }} <br><small style="color:#64748b">{{ $ag->agenteRel->supervisor->nombre ?? 'Sin Equipo' }}</small></td>
                <td style="text-align: center;">{{ $ag->efectivos }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $ag->total_vidas_efectivas }}</td>
                <td style="text-align: center; color: #e11d48;">{{ $ag->rechazados }}</td>
                <td style="text-align: center;">{{ $ag->pendientes }}</td>
                <td style="text-align: right; font-weight: bold; color: #4f46e5;">{{ $ag->hit_rate }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">2. Desglose Nominal de Afiliados (Auditoría Operativa)</div>
    <table class="main-table">
        <thead>
            <tr>
                <th width="12%">Cédula</th>
                <th width="30%">Nombre del Afiliado</th>
                <th width="20%">Agente Proceso</th>
                <th width="13%" style="text-align: center;">F. Solicitud</th>
                <th width="10%" style="text-align: center;">Estado</th>
                <th width="15%" style="text-align: center;">F. Efectiva/Rechazo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detallesAfiliados as $index => $traspaso)
            <tr class="{{ $index % 2 == 0 ? 'row-even' : 'row-odd' }}">
                <td style="font-family: monospace; font-size: 8px;">{{ $traspaso->cedula_afiliado }}</td>
                <td style="font-weight: bold;">{{ $traspaso->nombre_afiliado }}</td>
                <td>{{ $traspaso->agenteRel->nombre ?? 'N/A' }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($traspaso->fecha_solicitud)->format('d/m/Y') }}</td>
                <td style="text-align: center;">
                    @php 
                        $statusClass = 'badge-info';
                        $slug = $traspaso->estadoRel->slug ?? '';
                        if($slug == 'efectivo') $statusClass = 'badge-success';
                        if($slug == 'rechazado') $statusClass = 'badge-danger';
                        if($slug == 'pendiente') $statusClass = 'badge-warning';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $traspaso->estadoRel->nombre ?? 'N/A' }}</span>
                </td>
                <td style="text-align: center;">
                    {{ $traspaso->fecha_efectivo ? \Carbon\Carbon::parse($traspaso->fecha_efectivo)->format('d/m/Y') : ($traspaso->fecha_rechazo ? \Carbon\Carbon::parse($traspaso->fecha_rechazo)->format('d/m/Y') : '---') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento contiene información confidencial. Generado por SYS_CARNET para el equipo de gestión operativa.
    </div>

</body>
</html>
