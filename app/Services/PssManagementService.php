<?php

namespace App\Services;

use App\Models\PssCiudad;
use App\Models\PssEspecialidad;
use App\Models\PssGrupo;
use App\Models\PssClinica;
use App\Models\PssMedico;
use App\Models\PssCentro;
use App\Models\PssAuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PssManagementService
{
    /**
     * Normalizar teléfono al formato (809/829/849)-XXX-XXXX
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (!$phone) return null;

        // Limpiar todo lo que no sea número
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Si tiene 10 dígitos, formatear
        if (strlen($clean) === 10) {
            return sprintf("(%s) %s-%s", substr($clean, 0, 3), substr($clean, 3, 3), substr($clean, 6));
        }
        
        // Si tiene 11 dígitos y empieza con 1, formatear ignorando el 1
        if (strlen($clean) === 11 && $clean[0] === '1') {
            return sprintf("(%s) %s-%s", substr($clean, 1, 3), substr($clean, 4, 3), substr($clean, 7));
        }

        return $phone; 
    }

    /**
     * Normalizar nombres a Title Case de forma robusta
     */
    public function normalizeName(?string $name): ?string
    {
        if (!$name) return null;
        
        // Eliminar prefijos comunes si estorban, pero generalmente en PSS se mantienen
        // Limpiar espacios y capitalizar
        $name = trim(preg_replace('/\s+/', ' ', $name));
        return Str::title($name);
    }

    /**
     * Resolver o crear un registro de catálogo
     */
    public function resolveCatalog(string $modelClass, ?string $name, array $extra = []): ?int
    {
        $normalized = $this->normalizeName($name);
        if (!$normalized) return null;

        $record = $modelClass::where('nombre', $normalized)->first();
        
        if (!$record) {
            $data = ['nombre' => $normalized];
            if (!empty($extra)) $data = array_merge($data, $extra);
            $record = $modelClass::create($data);
        }

        return $record->id;
    }

    /**
     * Importar un Médico con lógica de duplicados y auditoría
     */
    public function importMedico(array $data, ?int $importId = null): array
    {
        try {
            $nombre = $this->normalizeName($data['nombre'] ?? '');
            if (!$nombre) throw new \Exception("El nombre del médico es obligatorio.");

            // Resolver Catálogos
            $ciudadId = $this->resolveCatalog(PssCiudad::class, $data['ciudad'] ?? null);
            $especialidadId = $this->resolveCatalog(PssEspecialidad::class, $data['especialidad'] ?? null);
            $clinicaId = $this->resolveCatalog(PssClinica::class, $data['clinica'] ?? null, ['ciudad_id' => $ciudadId]);

            // Verificar Duplicado
            $existing = PssMedico::where('nombre', $nombre)
                ->where('ciudad_id', $ciudadId)
                ->first();

            if ($existing) {
                return ['status' => 'duplicate', 'model' => $existing];
            }

            $medico = PssMedico::create([
                'nombre' => $nombre,
                'telefono_1' => $this->normalizePhone($data['telefono_1'] ?? null),
                'telefono_2' => $this->normalizePhone($data['telefono_2'] ?? null),
                'ciudad_id' => $ciudadId,
                'especialidad_id' => $especialidadId,
                'clinica_id' => $clinicaId,
                'estado' => 'activo',
                'origen_importacion' => $importId ? "Excel #$importId" : 'Manual',
                'fecha_importacion' => now(),
            ]);

            $this->logAction($medico, 'create', null, null, "Importación masiva");

            return ['status' => 'success', 'model' => $medico];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Importar un Centro con lógica de duplicados y auditoría
     */
    public function importCentro(array $data, ?int $importId = null): array
    {
        try {
            $nombre = $this->normalizeName($data['nombre'] ?? '');
            if (!$nombre) throw new \Exception("El nombre del centro es obligatorio.");

            $ciudadId = $this->resolveCatalog(PssCiudad::class, $data['ciudad'] ?? null);
            $grupoId = $this->resolveCatalog(PssGrupo::class, $data['grupo'] ?? null);

            $existing = PssCentro::where('nombre', $nombre)
                ->where('ciudad_id', $ciudadId)
                ->first();

            if ($existing) {
                return ['status' => 'duplicate', 'model' => $existing];
            }

            $centro = PssCentro::create([
                'nombre' => $nombre,
                'telefono_1' => $this->normalizePhone($data['telefono_1'] ?? null),
                'telefono_2' => $this->normalizePhone($data['telefono_2'] ?? null),
                'ciudad_id' => $ciudadId,
                'grupo_id' => $grupoId,
                'estado' => 'activo',
                'origen_importacion' => $importId ? "Excel #$importId" : 'Manual',
                'fecha_importacion' => now(),
            ]);

            $this->logAction($centro, 'create', null, null, "Importación masiva");

            return ['status' => 'success', 'model' => $centro];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Registrar auditoría
     */
    public function logAction($model, string $action, ?string $field = null, $old = null, $new = null)
    {
        PssAuditLog::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'user_id' => auth()->id(),
            'accion' => $action,
            'campo' => $field,
            'valor_anterior' => is_array($old) ? json_encode($old) : $old,
            'valor_nuevo' => is_array($new) ? json_encode($new) : $new,
            'ip_address' => request()->ip()
        ]);
    }
}
