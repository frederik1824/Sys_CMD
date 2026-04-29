<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Carnets Pendientes - {{ date('d-m-Y') }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; margin: 0; padding: 40px; line-height: 1.5; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #334155; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 900; color: #2563eb; letter-spacing: -1px; }
        .report-title { text-align: right; }
        .report-title h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #0f172a; }
        .report-title p { margin: 5px 0 0; font-size: 12px; color: #64748b; font-weight: bold; }
        
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-around; }
        .stat { text-align: center; }
        .stat-label { font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .stat-value { font-size: 22px; font-weight: 900; color: #0f172a; }
        .stat-value.pending { color: #e11d48; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
        th { background: #f1f5f9; text-align: left; padding: 12px 15px; font-size: 11px; font-weight: 900; border-bottom: 1px solid #cbd5e1; text-transform: uppercase; }
        td { padding: 12px 15px; font-size: 11px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tr:nth-child(even) { background: #fafafa; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .badge-warning { background: #fffbeb; color: #92400e; }
        .badge-danger { background: #fff1f2; color: #9f1239; }

        .footer { margin-top: 80px; display: flex; justify-content: space-between; }
        .sign { border-top: 1px solid #94a3b8; width: 200px; text-align: center; padding-top: 10px; font-size: 11px; font-weight: bold; color: #64748b; }

        @media print {
            body { padding: 0; }
            button { display: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            SYS <span style="color: #64748b;">CARNET</span>
        </div>
        <div class="report-title">
            <h1>Reporte Ejecutivo: Inventario Pendiente</h1>
            <p>Generado el {{ date('d M, Y h:i A') }}</p>
        </div>
    </div>

    <div class="summary-box">
        <div class="stat">
            <div class="stat-label">Cortes Analizados</div>
            <div class="stat-value">{{ $reporteCortes->count() }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Ingresado</div>
            <div class="stat-value">{{ number_format($reporteCortes->sum('total')) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Pendiente</div>
            <div class="stat-value pending">{{ number_format($reporteCortes->sum('pendientes_count')) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">% Efectividad Global</div>
            <div class="stat-value">
                @php $totalG = $reporteCortes->sum('total'); @endphp
                {{ $totalG > 0 ? round(($reporteCortes->sum('entregados_count') / $totalG) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Corte Mensual / Período</th>
                <th>Fecha Creación</th>
                <th style="text-align: right;">Total Cargados</th>
                <th style="text-align: right;">Entregados</th>
                <th style="text-align: right;">Pendientes</th>
                <th style="text-align: right;">Avance %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporteCortes as $c)
            @php $porcentaje = $c->total > 0 ? ($c->entregados_count / $c->total) * 100 : 0; @endphp
            <tr>
                <td style="font-weight: bold;">{{ $c->nombre }}</td>
                <td>{{ $c->created_at->format('d/m/Y') }}</td>
                <td style="text-align: right;">{{ number_format($c->total) }}</td>
                <td style="text-align: right; color: #059669;">{{ number_format($c->entregados_count) }}</td>
                <td style="text-align: right; font-weight: 900; color: #be123c;">{{ number_format($c->pendientes_count) }}</td>
                <td style="text-align: right;">
                    <strong>{{ round($porcentaje) }}%</strong>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="sign">Firma Responsable Operativo</div>
        <div class="sign">Firma Gerencia / Auditoría</div>
    </div>

    <div class="no-print" style="position: fixed; bottom: 30px; right: 30px;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.3);">
            Imprimir Reporte ahora
        </button>
    </div>

    <script>
        window.onload = function() {
            // Esperar un poco para que todo cargue bien antes de imprimir
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>
