<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProduccionTraspasosExport implements WithMultipleSheets
{
    use Exportable;

    protected $fecha_desde;
    protected $fecha_hasta;
    protected $supervisor_id;
    protected $rankingAgentes;
    protected $detallesAfiliados;

    public function __construct($fecha_desde, $fecha_hasta, $supervisor_id, $rankingAgentes, $detallesAfiliados)
    {
        $this->fecha_desde = $fecha_desde;
        $this->fecha_hasta = $fecha_hasta;
        $this->supervisor_id = $supervisor_id;
        $this->rankingAgentes = $rankingAgentes;
        $this->detallesAfiliados = $detallesAfiliados;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new ProduccionTraspasosRankingSheet($this->rankingAgentes),
            new ProduccionTraspasosDetalleSheet($this->detallesAfiliados),
        ];
    }
}
