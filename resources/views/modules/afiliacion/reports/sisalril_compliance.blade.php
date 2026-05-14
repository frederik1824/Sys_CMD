<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cumplimiento SISALRIL</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .header { border-bottom: 2px solid #1a1a1a; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 900; color: #000; letter-spacing: -1px; }
        .title { font-size: 18px; font-weight: 700; text-transform: uppercase; margin-top: 5px; color: #444; }
        .meta { font-size: 10px; color: #777; margin-top: 5px; }
        
        .kpi-container { margin-bottom: 40px; }
        .kpi-box { width: 30%; display: inline-block; padding: 15px; border: 1px solid #eee; border-radius: 10px; margin-right: 2%; vertical-align: top; }
        .kpi-label { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #888; margin-bottom: 5px; }
        .kpi-value { font-size: 22px; font-weight: 900; color: #1a1a1a; }
        .kpi-unit { font-size: 11px; font-weight: 700; color: #aaa; }
        
        .section-title { font-size: 12px; font-weight: 900; text-transform: uppercase; background: #f9f9f9; padding: 10px; border-left: 4px solid #1a1a1a; margin-bottom: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #888; text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        td { font-size: 10px; padding: 10px; border-bottom: 1px solid #f9f9f9; }
        
        .aging-bar { height: 10px; background: #f0f0f0; border-radius: 5px; overflow: hidden; margin-top: 5px; }
        .bar-fill { height: 100%; }
        
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #aaa; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        .signature { margin-top: 50px; }
        .sig-box { width: 200px; border-top: 1px solid #333; margin-top: 60px; font-size: 10px; text-align: center; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ARS CMD <span style="color: #4f46e5;">SYSTEM</span></div>
        <div class="title">Reporte de Cumplimiento SISALRIL: Afiliación Pensionados</div>
        <div class="meta">Generado el {{ now()->format('d/m/Y H:i:s') }} | Período: {{ strtoupper($periodo) }}</div>
    </div>

    <div class="kpi-container">
        <div class="kpi-box">
            <div class="kpi-label">TAT Promedio General</div>
            <div class="kpi-value">{{ number_format($tatPromedio, 1) }} <span class="kpi-unit">Horas</span></div>
        </div>
        <div class="kpi-box" style="background: #f8fafc;">
            <div class="kpi-label">Confirmación Pensionados</div>
            <div class="kpi-value">{{ number_format($tatPensionadosTSS, 1) }} <span class="kpi-unit">Días</span></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Solicitudes Pendientes</div>
            <div class="kpi-value">{{ number_format($pensionadosAging->total) }} <span class="kpi-unit">Casos</span></div>
        </div>
    </div>

    <div class="section-title">Envejecimiento de Solicitudes (Aging Report)</div>
    <table style="margin-bottom: 40px;">
        <thead>
            <tr>
                <th>Rango de Tiempo</th>
                <th>Estado</th>
                <th>Cantidad de Casos</th>
                <th>Distribución</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>0 - 30 Días</td>
                <td style="color: #10b981; font-weight: 700;">Óptimo</td>
                <td>{{ number_format($pensionadosAging->range_30) }}</td>
                <td>
                    <div class="aging-bar">
                        <div class="bar-fill" style="background: #10b981; width: {{ $pensionadosAging->total > 0 ? ($pensionadosAging->range_30 / $pensionadosAging->total) * 100 : 0 }}%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>31 - 60 Días</td>
                <td style="color: #f59e0b; font-weight: 700;">Observación</td>
                <td>{{ number_format($pensionadosAging->range_60) }}</td>
                <td>
                    <div class="aging-bar">
                        <div class="bar-fill" style="background: #f59e0b; width: {{ $pensionadosAging->total > 0 ? ($pensionadosAging->range_60 / $pensionadosAging->total) * 100 : 0 }}%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>61 - 90+ Días</td>
                <td style="color: #ef4444; font-weight: 700;">Crítico</td>
                <td>{{ number_format($pensionadosAging->range_90) }}</td>
                <td>
                    <div class="aging-bar">
                        <div class="bar-fill" style="background: #ef4444; width: {{ $pensionadosAging->total > 0 ? ($pensionadosAging->range_90 / $pensionadosAging->total) * 100 : 0 }}%"></div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Auditoría de Excepciones (Solicitudes sin Pago Detectado)</div>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Afiliado</th>
                <th>Identificación</th>
                <th>Fecha Solicitud</th>
                <th>Días Transcurridos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($excepcionesPago as $ex)
            <tr>
                <td>{{ $ex->codigo_solicitud }}</td>
                <td>{{ $ex->nombre_completo }}</td>
                <td>{{ $ex->cedula }}</td>
                <td>{{ $ex->created_at->format('d/m/Y') }}</td>
                <td style="color: #ef4444; font-weight: 700;">{{ now()->diffInDays($ex->created_at) }} Días</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <div class="sig-box">
            Firma Responsable de Afiliaciones<br>
            ARS CMD
        </div>
    </div>

    <div class="footer">
        Este documento es un reporte generado automáticamente por el Sistema de Control de Dispersión de ARS CMD.<br>
        La información aquí contenida está basada en los cruces de archivos oficiales de la TSS y SISALRIL.
    </div>
</body>
</html>
