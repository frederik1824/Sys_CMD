<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 100px 50px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; border-bottom: 1px solid #eee; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 30px; border-top: 1px solid #eee; text-align: center; font-size: 10px; color: #777; }
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        h1 { color: #1e293b; font-size: 24px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #3b82f6; font-size: 18px; margin-top: 30px; border-left: 4px solid #3b82f6; padding-left: 10px; }
        .badge { display: inline-block; padding: 4px 8px; background: #eff6ff; color: #3b82f6; border-radius: 4px; font-weight: bold; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 10px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        .priority-red { color: #e11d48; font-weight: bold; }
        .priority-green { color: #059669; font-weight: bold; }
        .page-break { page-break-after: always; }
        .intro-box { background: #f8fafc; padding: 20px; border-radius: 10px; margin-bottom: 30px; font-style: italic; font-size: 13px; }
    </style>
</head>
<body>
    <header>
        <div style="float: left; font-weight: bold; color: #3b82f6;">{{ $company }}</div>
        <div style="float: right; font-size: 10px; color: #94a3b8;">{{ $date }} | Versión {{ $version }}</div>
    </header>

    <footer>
        Página <span class="pagenum"></span> - Manual Maestro de Afiliaciones ARS CMD
    </footer>

    <main>
        <h1>{{ $title }}</h1>
        
        <div class="intro-box">
            El módulo de Solicitud de Afiliación es el pilar tecnológico para la gestión de nuevos ingresos. 
            Este documento establece los protocolos obligatorios para garantizar la integridad y seguridad de la información institucional.
        </div>

        <h2>1. Roles y Atribuciones</h2>
        <table>
            <thead>
                <tr>
                    <th>Nivel</th>
                    <th>Rol Funcional</th>
                    <th>Responsabilidades Clave</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>01</strong></td>
                    <td>Solicitante / Promotor</td>
                    <td>Carga inicial, captura de documentos y envío a revisión.</td>
                </tr>
                <tr>
                    <td><strong>02</strong></td>
                    <td>Auditor / Validador</td>
                    <td>Validación técnica y documental. Control de calidad.</td>
                </tr>
                <tr>
                    <td><strong>03</strong></td>
                    <td>Aprobador Final</td>
                    <td>Autorización definitiva para el sistema central.</td>
                </tr>
            </tbody>
        </table>

        <h2>2. Ciclo de Vida de la Solicitud</h2>
        <p>Toda solicitud debe progresar a través de los siguientes estados:</p>
        <ul>
            <li><strong>🟠 PENDIENTE</strong>: Documentación incompleta o pendiente de envío.</li>
            <li><strong>🔵 EN REVISIÓN</strong>: Bajo análisis de Auditoría.</li>
            <li><strong>🔴 DEVUELTA</strong>: <span class="priority-red">Acción requerida.</span> Detectado error en datos o imágenes.</li>
            <li><strong>🟢 APROBADA</strong>: Expediente validado y listo para producción.</li>
            <li><strong>✅ COMPLETADA</strong>: Proceso finalizado exitosamente.</li>
        </ul>

        <div class="page-break"></div>

        <h2>3. Estándar de Calidad de Documentos</h2>
        <p>Para evitar devoluciones de Auditoría, los documentos deben cumplir con:</p>
        <table>
            <tr>
                <td width="30%"><strong>Nitidez</strong></td>
                <td>Texto 100% legible, sin borrones ni desenfoques.</td>
            </tr>
            <tr>
                <td><strong>Iluminación</strong></td>
                <td>Luz natural preferiblemente. Evitar reflejos de flash sobre el plástico.</td>
            </tr>
            <tr>
                <td><strong>Encuadre</strong></td>
                <td>La cédula debe ocupar la mayor parte de la imagen, sin dedos ni objetos de fondo.</td>
            </tr>
        </table>

        <h2>4. Reglas de Negocio Críticas</h2>
        <ol>
            <li><strong>Cero Duplicados</strong>: El sistema bloquea automáticamente cédulas ya registradas.</li>
            <li><strong>Tiempo de Respuesta (SLA)</strong>: Las devoluciones deben corregirse en un máximo de 48 horas.</li>
            <li><strong>Formulario Firmado</strong>: Es obligatorio que el formulario físico coincida al 100% con la carga digital.</li>
        </ol>

        <h2 style="margin-top: 50px;">5. Preguntas Frecuentes</h2>
        <p><strong>Q: ¿Puedo anular una solicitud aprobada?</strong><br>
        R: No directamente. Requiere una solicitud formal a Soporte IT por motivos legales.</p>
        
        <p><strong>Q: ¿Qué hago si el sistema rechaza una cédula válida?</strong><br>
        R: Contacta al Supervisor para verificar si el afiliado ya existe en otra base de datos de la Suite.</p>

        <div style="margin-top: 100px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
            <p style="font-size: 10px; color: #94a3b8;">
                Generado automáticamente por el Sistema CMD - Módulo de Inteligencia Operativa<br>
                &copy; {{ date('Y') }} ARS CMD. Todos los derechos reservados.
            </p>
        </div>
    </main>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "{PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
