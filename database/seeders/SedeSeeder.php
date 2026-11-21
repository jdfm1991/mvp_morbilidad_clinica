<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;

class SedeSeeder extends Seeder
{
    public function run(): void
    {
        Sede::create([
            'nombre' => 'Sede Principal (Muestra)',
            'codigo' => 'SEDE-001',
            'activa' => true,
        ]);
    }
}