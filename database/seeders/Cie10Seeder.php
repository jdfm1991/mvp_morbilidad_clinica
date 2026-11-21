<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Cie10Seeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('cie10_ops.csv');

        if (!File::exists($path)) {
            $this->command->error("El archivo {$path} no existe. Descarga el CSV del CIE-10 y colócalo en la raíz del proyecto.");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // Asume que la primera fila son los encabezados

        $batch = [];
        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Mapeo asumiendo un CSV con: codigo, descripcion, capitulo
            // Ajusta los índices [0], [1], [2] según el formato de tu CSV real
            $batch[] = [
                'codigo' => trim($row[0]),
                'descripcion' => trim($row[1]),
                'capitulo' => trim($row[2] ?? null),
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) === 500) {
                DB::table('cie10')->insert($batch);
                $batch = [];
                $count += 500;
                $this->command->info("Insertados {$count} registros...");
            }
        }

        if (count($batch) > 0) {
            DB::table('cie10')->insert($batch);
            $count += count($batch);
        }

        fclose($file);
        $this->command->info("¡Catálogo CIE-10 cargado exitosamente! Total: {$count} registros.");
    }
}