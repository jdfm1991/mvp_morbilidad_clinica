<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GeografiaSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('geografia_venezuela.sql');

        if (!File::exists($path)) {
            $this->command->error("El archivo {$path} no existe en la raíz del proyecto.");
            return;
        }

        $sql = File::get($path);

        // 1. ESTADOS
        // Busca tuplas tipo: (1, 'Distrito Capital', 'VE-A')
        preg_match_all('/\(\s*(\d+)\s*,\s*\'([^\']*)\'\s*,\s*\'([^\']*)\'\s*\)/', $sql, $matches, PREG_SET_ORDER);
        
        // Filtrar solo las que pertenecen a la sección de estados
        // (asumiendo que los IDs de estados son los primeros en el archivo)
        $estados = [];
        foreach (array_slice($matches, 0, 25) as $match) { // Venezuela tiene 25 estados
            $estados[] = [
                'id' => (int) $match[1],
                'nombre' => $match[2],
                'codigo' => $match[3],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($estados)) {
            DB::table('estados')->insert($estados);
            $this->command->info("Insertados " . count($estados) . " estados.");
        }

        // 2. MUNICIPIOS
        preg_match('/INSERT INTO `municipios`.*?VALUES\s*(.*?);/is', $sql, $munMatches);
        if (isset($munMatches[1])) {
            preg_match_all('/\(\s*(\d+)\s*,\s*(\d+)\s*,\s*\'([^\']*)\'\s*\)/', $munMatches[1], $munRows, PREG_SET_ORDER);
            
            $municipios = [];
            foreach ($munRows as $row) {
                $municipios[] = [
                    'id' => (int) $row[1],
                    'estado_id' => (int) $row[2],
                    'nombre' => $row[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            foreach (array_chunk($municipios, 500) as $chunk) {
                DB::table('municipios')->insert($chunk);
            }
            $this->command->info("Insertados " . count($municipios) . " municipios.");
        }

        // 3. PARROQUIAS
        preg_match('/INSERT INTO `parroquias`.*?VALUES\s*(.*?);/is', $sql, $parrMatches);
        if (isset($parrMatches[1])) {
            preg_match_all('/\(\s*(\d+)\s*,\s*(\d+)\s*,\s*\'([^\']*)\'\s*\)/', $parrMatches[1], $parrRows, PREG_SET_ORDER);
            
            $parroquias = [];
            foreach ($parrRows as $row) {
                $parroquias[] = [
                    'id' => (int) $row[1],
                    'municipio_id' => (int) $row[2],
                    'nombre' => $row[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            foreach (array_chunk($parroquias, 500) as $chunk) {
                DB::table('parroquias')->insert($chunk);
            }
            $this->command->info("Insertados " . count($parroquias) . " parroquias.");
        }

        // Ajustar secuencias de PostgreSQL
        DB::statement("SELECT setval('estados_id_seq', (SELECT MAX(id) FROM estados))");
        DB::statement("SELECT setval('municipios_id_seq', (SELECT MAX(id) FROM municipios))");
        DB::statement("SELECT setval('parroquias_id_seq', (SELECT MAX(id) FROM parroquias))");
        
        $this->command->info("¡Geografía cargada y secuencias ajustadas!");
    }
}