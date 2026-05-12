<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function exportManualAfiliacion()
    {
        // En un entorno real, podríamos parsear el .md, 
        // pero para máxima calidad visual en PDF, usaremos una vista Blade diseñada.
        $data = [
            'title' => 'GUÍA OPERATIVA: SOLICITUD DE AFILIACIÓN (CRM)',
            'version' => '2.0',
            'date' => now()->format('d/m/Y'),
            'company' => 'ARS CMD'
        ];

        $pdf = Pdf::loadView('admin.docs.manual-afiliacion-pdf', $data);
        
        return $pdf->download('manual_solicitud_afiliacion_v2.pdf');
    }
}
