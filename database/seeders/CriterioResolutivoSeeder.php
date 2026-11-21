<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CriterioResolutivo;

class CriterioResolutivoSeeder extends Seeder
{
    public function run(): void
    {
        $criterios = [
            ['nombre' => 'Ambulatorio / Alta directa', 'codigo' => 'AMB'],
            ['nombre' => 'Observación', 'codigo' => 'OBS'],
            ['nombre' => 'Hospitalización', 'codigo' => 'HOSP'],
            ['nombre' => 'Cirugía', 'codigo' => 'CIR'],
            ['nombre' => 'Referencia / Contrareferencia', 'codigo' => 'REF'],
            ['nombre' => 'Fallecimiento', 'codigo' => 'FALL'],
        ];

        foreach ($criterios as $c) {
            CriterioResolutivo::updateOrCreate(['codigo' => $c['codigo']], $c);
        }
    }
}