<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->restrictOnDelete();
            
            // Identificación y código interno (nullable pero con restricciones)
            $table->string('identificacion', 20)->nullable();
            $table->string('codigo_interno', 20)->nullable();
            
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento');
            $table->char('sexo', 1);
            $table->string('telefono', 20)->nullable();
            
            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();
            $table->foreignId('parroquia_id')->nullable()->constrained('parroquias')->nullOnDelete();
            $table->text('direccion')->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Restricción CHECK: Al menos identificación o código interno debe existir
        DB::statement('ALTER TABLE pacientes ADD CONSTRAINT check_identificacion_o_codigo CHECK (identificacion IS NOT NULL OR codigo_interno IS NOT NULL)');
        // Restricción CHECK: Sexo debe ser M o F
        DB::statement("ALTER TABLE pacientes ADD CONSTRAINT check_sexo CHECK (sexo IN ('M', 'F'))");
        
        // Índices únicos parciales (PostgreSQL): Permiten múltiples NULLs pero únicos si hay valor
        DB::statement('CREATE UNIQUE INDEX pacientes_identificacion_unique ON pacientes (identificacion) WHERE identificacion IS NOT NULL');
        DB::statement('CREATE UNIQUE INDEX pacientes_codigo_interno_unique ON pacientes (codigo_interno) WHERE codigo_interno IS NOT NULL');
        
        // Índices para búsquedas
        Schema::table('pacientes', function (Blueprint $table) {
            $table->index('apellidos');
            $table->index('nombres');
            $table->index('fecha_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};