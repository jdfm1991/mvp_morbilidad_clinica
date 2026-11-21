<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoAtencion;

class TipoAtencionSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Consulta Externa', 'codigo' => 'CONS_EXT'],
            ['nombre' => 'Emergencia', 'codigo' => 'EMERG'],
            ['nombre' => 'Hospitalización', 'codigo' => 'HOSP'],
            ['nombre' => 'Control / Seguimiento', 'codigo' => 'CONTROL'],
        ];

        foreach ($tipos as $t) {
            TipoAtencion::updateOrCreate(['codigo' => $t['codigo']], $t);
        }
    }
}