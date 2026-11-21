<?php

namespace App\Services;

use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

class PacienteService
{
    /**
     * Genera un código interno único para pacientes sin cédula.
     * Formato: RN-2026-00001 (Recién nacido) o SI-2026-00001 (Sin identificación)
     */
    public static function generarCodigoInterno(string $tipo = 'SI'): string
    {
        $anio = date('Y');
        // Buscar el último código generado este año para el tipo
        $ultimoCodigo = Paciente::where('codigo_interno', 'LIKE', "{$tipo}-{$anio}-%")
            ->orderBy('codigo_interno', 'desc')
            ->first();

        if ($ultimoCodigo) {
            // Extraer el número secuencial y sumarle 1
            $partes = explode('-', $ultimoCodigo->codigo_interno);
            $secuencial = (int) end($partes) + 1;
        } else {
            $secuencial = 1;
        }

        return sprintf('%s-%s-%05d', $tipo, $anio, $secuencial);
    }
}