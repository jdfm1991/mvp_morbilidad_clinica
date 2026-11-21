<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SedeSeeder::class,
            CriterioResolutivoSeeder::class,
            TipoAtencionSeeder::class,
            GrupoEtarioSeeder::class,
            Cie10Seeder::class,
        ]);
    }
}
