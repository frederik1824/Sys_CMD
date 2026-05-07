<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Dispersión - {{ $period->month_name }} {{ $period->year }}</title>
    <style>
        /* Configuración de Página */
        @page { 
            size: letter portrait; 
            margin: 0.5in; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            color: #0f172a; 
            font-size: 10px; 
            line-height: 1.3; 
            margin: 0;
            padding: 0;
        }

        /* Branding y Header */
        .header-table { width: 100%; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { height: 45px; }
        .badge { 
            background: #0f172a; 
            color: white; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 8px; 
            font-weight: 900; 
            text-transform: uppercase; 
            letter-spacing: 0.2em;
        }
        .period-title { font-size: 14px; font-weight: 900; color: #0f172a; margin-top: 10px; }
        .sub-title { font-size: 8px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.3em; }

        /* KPI Cards (Usando tabla para simular grid) */
        .kpi-table { width: 100%; margin-bottom: 25px; border-spacing: 10px; border-collapse: separate; margin-left: -10px; }
        .kpi-card { 
            background: #f8fafc; 
            border: 1px solid #e2e8f0; 
            border-radius: 15px; 
            padding: 15px; 
            text-align: left;
            width: 33.33%;
        }
        .kpi-label { font-size: 7px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 5px; }
        .kpi-value { font-size: 16px; font-weight: 900; color: #0f172a; }
        .kpi-status { font-size: 7px; font-weight: 900; margin-top: 5px; text-transform: uppercase; }

        /* Secciones y Tablas */
        .section-divider { 
            text-align: center; 
            margin: 20px 0 10px 0; 
            position: relative;
        }
        .section-divider span { 
            background: white; 
            padding: 0 15px; 
            font-size: 8px; 
            font-weight: 900; 
            text-transform: uppercase; 
            letter-spacing: 0.4em; 
            color: #94a3b8;
        }
        .section-line { position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: #f1f5f9; z-index: -1; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th { 
            text-align: left; 
            padding: 8px 10px; 
            font-size: 8px; 
            text-transform: uppercase; 
            color: #94a3b8; 
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table td { padding: 7px 10px; border-bottom: 1px solid #f8fafc; color: #334155; font-size: 9px; font-weight: bold; }
        .text-right { text-align: right; }
        .font-black { font-weight: 900; color: #0f172a; }
        .text-emerald { color: #059669; }
        .text-rose { color: #e11d48; }
        .bg-total { background-color: #f8fafc; border-left: 3px solid #0f172a !important; }

        /* Firmas */
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-box { 
            width: 45%; 
            border-top: 1px solid #cbd5e1; 
            padding-top: 8px; 
            text-align: center; 
            font-size: 8px; 
            font-weight: 900; 
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td>
                <img src="{{ public_path('img/Logo.png') }}" class="logo">
                <div style="margin-top: 10px;">
                    <h1 style="margin:0; font-size: 18px; font-weight: 900; letter-spacing: -0.03em;">CONTROL DE DISPERSIÓN</h1>
                    <p class="sub-title">ARS CMD - GESTIÓN OPERATIVA PDSS</p>
                </div>
            </td>
            <td align="right" valign="top">
                <span class="badge">Reporte Institucional</span>
                <div class="period-title">{{ $period->month_name }} {{ $period->year }}</div>
            </td>
        </tr>
    </table>

    {{-- KPIs --}}
    @php $stats = \App\Http\Controllers\Modules\Admin\DispersionController::getStats($period); @endphp
    <table class="kpi-table">
        <tr>
            <td class="kpi-card" style="background: #f0fdf4; border-color: #dcfce7;">
                <div class="kpi-label">Afiliados PDSS</div>
                <div class="kpi-value">{{ number_format($stats['total_afiliados']) }}</div>
                <div class="kpi-status" style="color: #16a34a;">● Auditoría Completada</div>
            </td>
            <td class="kpi-card" style="background: #f0f9ff; border-color: #e0f2fe;">
                <div class="kpi-label">Monto Dispersado</div>
                <div class="kpi-value">RD$ {{ number_format($stats['monto_total'], 2) }}</div>
                <div class="kpi-status" style="color: #0284c7;">● Flujo Consolidado</div>
            </td>
            <td class="kpi-card" style="background: #fff1f2; border-color: #ffe4e6;">
                <div class="kpi-label" style="color: #f43f5e;">Bajas Reportadas</div>
                <div class="kpi-value" style="color: #e11d48;">{{ number_format($stats['total_bajas']) }}</div>
                <div class="kpi-status" style="color: #f43f5e;">● Movimientos de Salida</div>
            </td>
        </tr>
    </table>

    {{-- Dispersión --}}
    <div class="section-divider">
        <div class="section-line"></div>
        <span>Detalle de Dispersión</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="40%">Indicador</th>
                <th width="20%" class="text-right">1er Corte</th>
                <th width="20%" class="text-right">2do Corte</th>
                <th width="20%" class="text-right" style="color: #0f172a;">Consolidado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indicators as $ind)
            @php 
                if (str_contains($ind->name, 'Pérdida de Empleo') || str_contains($ind->name, 'Separación o Divorcio')) continue;
                $c1 = $period->cortes->where('corte_number', 1)->first();
                $v1 = $c1 ? $c1->values->where('indicator_id', $ind->id)->first() : null;
                $val1 = ($ind->category === 'Montos' ? ($v1->amount ?? 0) : ($v1->quantity ?? 0));
                $c2 = $period->cortes->where('corte_number', 2)->first();
                $v2 = $c2 ? $c2->values->where('indicator_id', $ind->id)->first() : null;
                $val2 = ($ind->category === 'Montos' ? ($v2->amount ?? 0) : ($v2->quantity ?? 0));
                $isSnapshot = $ind->code === 'TOTAL_GENERAL_PDSS';
                $total = ($isSnapshot && $val2 > 0) ? $val2 : ($isSnapshot ? $val1 : ($val1 + $val2));
            @endphp
            <tr class="{{ $ind->is_total ? 'bg-total' : '' }}">
                <td class="{{ $ind->is_total ? 'font-black' : '' }}">{{ $ind->name }}</td>
                <td class="text-right">{{ $ind->category === 'Montos' ? number_format($val1, 2) : number_format($val1) }}</td>
                <td class="text-right">{{ $ind->category === 'Montos' ? number_format($val2, 2) : number_format($val2) }}</td>
                <td class="text-right font-black {{ $ind->is_total ? '' : 'text-emerald' }}">
                    {{ $ind->category === 'Montos' ? number_format($total, 2) : number_format($total) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Bajas --}}
    <div class="section-divider" style="margin-top: 15px;">
        <div class="section-line"></div>
        <span style="color: #f43f5e;">Control de Bajas</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="40%">Motivo de Baja</th>
                <th width="20%" class="text-right">1er Corte</th>
                <th width="20%" class="text-right">2do Corte</th>
                <th width="20%" class="text-right" style="color: #e11d48;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bajaTypes as $type)
            @php 
                $c1 = $period->cortes->where('corte_number', 1)->first();
                $b1 = $c1 ? $c1->bajaValues->where('baja_type_id', $type->id)->first() : null;
                $q1 = $b1->quantity ?? 0;
                $c2 = $period->cortes->where('corte_number', 2)->first();
                $b2 = $c2 ? $c2->bajaValues->where('baja_type_id', $type->id)->first() : null;
                $q2 = $b2->quantity ?? 0;
            @endphp
            <tr>
                <td>{{ $type->name }}</td>
                <td class="text-right">{{ number_format($q1) }}</td>
                <td class="text-right">{{ number_format($q2) }}</td>
                <td class="text-right font-black text-rose">{{ number_format($q1 + $q2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Firmas --}}
    <table class="signature-table">
        <tr>
            <td class="signature-box" style="padding-right: 20px;">
                Responsable de Operaciones<br>
                <span style="color: #94a3b8; font-size: 6px;">Departamento de Dispersión</span>
            </td>
            <td width="10%"></td>
            <td class="signature-box">
                Gerencia de Auditoría<br>
                <span style="color: #94a3b8; font-size: 6px;">ARS CMD Institutional</span>
            </td>
        </tr>
    </table>

    <div style="position: absolute; bottom: -20px; width: 100%; text-align: center; font-size: 7px; color: #cbd5e1; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em;">
        Documento Generado por SysCarnet Enterprise v2.0 - {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
