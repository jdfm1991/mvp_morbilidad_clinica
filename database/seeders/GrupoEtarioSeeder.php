<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoEtario;

class GrupoEtarioSeeder extends Seeder
{
    public function run(): void
    {
        $grupos = [
            ['nombre' => 'Menor de 1 año', 'edad_min' => 0, 'edad_max' => 0],
            ['nombre' => '1 a 4 años', 'edad_min' => 1, 'edad_max' => 4],
            ['nombre' => '5 a 9 años', 'edad_min' => 5, 'edad_max' => 9],
            ['nombre' => '10 a 14 años', 'edad_min' => 10, 'edad_max' => 14],
            ['nombre' => '15 a 19 años', 'edad_min' => 15, 'edad_max' => 19],
            ['nombre' => '20 a 24 años', 'edad_min' => 20, 'edad_max' => 24],
            ['nombre' => '25 a 44 años', 'edad_min' => 25, 'edad_max' => 44],
            ['nombre' => '45 a 64 años', 'edad_min' => 45, 'edad_max' => 64],
            ['nombre' => '65 años o más', 'edad_min' => 65, 'edad_max' => null],
        ];

        foreach ($grupos as $g) {
            GrupoEtario::updateOrCreate(['nombre' => $g['nombre']], $g);
        }
    }
}