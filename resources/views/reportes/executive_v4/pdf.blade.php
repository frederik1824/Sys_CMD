<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Dashboard PDF</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #00346f; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #00346f; font-size: 24px; }
        .header p { margin: 5px 0 0 0; color: #666; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; color: #00346f; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; }
        .kpi-container { width: 100%; display: table; }
        .kpi-box { display: table-cell; width: 25%; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; }
        .kpi-title { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #0f172a; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; color: #334155; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Dashboard Ejecutivo Consolidado</h1>
        <p>ARS CMD - Inteligencia Operativa | Período: {{ strtoupper($period) }}</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- RESUMEN GENERAL -->
    <div class="section">
        <div class="section-title">1. Resumen General</div>
        <div class="kpi-container">
            <div class="kpi-box">
                <div class="kpi-title">Nuevos Afiliados</div>
                <div class="kpi-value">{{ number_format($data['resumen']['afiliados']['value']) }}</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-title">Traspasos</div>
                <div class="kpi-value">{{ number_format($data['resumen']['traspasos']['value']) }}</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-title">Sol. Afiliación</div>
                <div class="kpi-value">{{ number_format($data['resumen']['solicitudes']['value']) }}</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-title">Asistencia Hoy</div>
                <div class="kpi-value">{{ $data['resumen']['asistencia']['value'] }} / {{ $data['resumen']['asistencia']['total'] }}</div>
            </div>
        </div>
    </div>

    <!-- CARNETIZACIÓN & AFILIACIÓN -->
    <div class="section">
        <div class="section-title">2. Operaciones de Carnetización y Afiliación</div>
        <table>
            <tr>
                <th>Indicador</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Tasa de Entrega de Carnets</td>
                <td>{{ $data['carnetizacion']['tasa_entrega'] }}% ({{ number_format($data['carnetizacion']['completados']) }} de {{ number_format($data['carnetizacion']['total']) }})</td>
            </tr>
            <tr>
                <td>Carnets Pendientes Recepción</td>
                <td>{{ number_format($data['carnetizacion']['pendientes_recepcion']) }}</td>
            </tr>
            <tr>
                <td>Críticos SLA (>20 días)</td>
                <td>{{ number_format($data['carnetizacion']['criticos_sla']) }}</td>
            </tr>
            <tr>
                <td>Tasa de Aprobación Afiliaciones</td>
                <td>{{ $data['afiliacion']['tasa_aprobacion'] }}%</td>
            </tr>
            <tr>
                <td>Tiempo Promedio de Respuesta</td>
                <td>{{ $data['afiliacion']['sla_horas'] }}h</td>
            </tr>
        </table>
    </div>

    <!-- TRASPASOS & CALL CENTER -->
    <div class="section">
        <div class="section-title">3. Traspasos y Call Center</div>
        <table>
            <tr>
                <th>Indicador</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Hit Rate Traspasos (Efectivos)</td>
                <td>{{ $data['traspasos']['hit_rate'] }}%</td>
            </tr>
            <tr>
                <td>Traspasos Rechazados</td>
                <td>{{ number_format($data['traspasos']['rechazados']) }}</td>
            </tr>
            <tr>
                <td>Tasa de Conversión Call Center</td>
                <td>{{ $data['callcenter']['tasa_conversion'] }}%</td>
            </tr>
            <tr>
                <td>Gestiones Realizadas</td>
                <td>{{ number_format($data['callcenter']['gestionados']) }}</td>
            </tr>
        </table>
    </div>

    <!-- OTROS -->
    <div class="section">
        <div class="section-title">4. Prestadores y Dispersión</div>
        <table>
            <tr>
                <th>Indicador</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Centros de Salud Activos</td>
                <td>{{ number_format($data['pss']['centros_activos']) }}</td>
            </tr>
            <tr>
                <td>Médicos Activos</td>
                <td>{{ number_format($data['pss']['medicos_activos']) }}</td>
            </tr>
            <tr>
                <td>Monto Dispersado</td>
                <td>RD$ {{ number_format($data['dispersion']['monto_total'], 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- DESGLOSE DISPERSIÓN -->
    <div class="section">
        <div class="section-title">5. Desglose de Dispersión Mensual</div>
        <table>
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th style="text-align: center;">1er Corte (Afiliados)</th>
                    <th style="text-align: right;">1er Corte (Monto)</th>
                    <th style="text-align: center;">2do Corte (Afiliados)</th>
                    <th style="text-align: right;">2do Corte (Monto)</th>
                    <th style="text-align: right;">Total Mensual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['dispersion']['desglose'] as $row)
                <tr>
                    <td>{{ $row['mes'] }} {{ $row['anio'] }}</td>
                    <td style="text-align: center;">{{ number_format($row['corte_1_afiliados']) }}</td>
                    <td style="text-align: right;">RD$ {{ number_format($row['corte_1_monto'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($row['corte_2_afiliados']) }}</td>
                    <td style="text-align: right;">RD$ {{ number_format($row['corte_2_monto'], 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">RD$ {{ number_format($row['total_monto'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 50px; font-size: 10px; color: #999;">
        Documento confidencial - Generado por ARS CMD Sistema Core
    </div>
</body>
</html>
