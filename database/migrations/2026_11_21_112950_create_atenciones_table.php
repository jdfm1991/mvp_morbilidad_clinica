<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->restrictOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            
            // Usuarios: admisión es obligatorio, médico se asigna luego
            $table->foreignId('user_admision_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('user_medico_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Tiempos
            $table->timestamp('fecha_hora_ingreso');
            $table->timestamp('fecha_hora_atencion')->nullable();
            $table->timestamp('fecha_hora_egreso')->nullable();
            
            // Datos clínicos iniciales
            $table->text('motivo_consulta')->nullable();
            
            // Signos vitales (columnas separadas)
            $table->smallInteger('presion_arterial_sistolica')->nullable();
            $table->smallInteger('presion_arterial_diastolica')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->smallInteger('frecuencia_cardiaca')->nullable();
            $table->smallInteger('frecuencia_respiratoria')->nullable();
            $table->smallInteger('saturacion_oxigeno')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            
            // Clasificación
            $table->foreignId('tipo_atencion_id')->nullable()->constrained('tipos_atencion')->nullOnDelete();
            $table->string('estado', 20)->default('registrado');
            
            // Diagnóstico y resolución
            $table->foreignId('cie10_principal_id')->nullable()->constrained('cie10')->nullOnDelete();
            $table->char('clasificacion_caso', 1)->nullable();
            $table->foreignId('criterio_resolutivo_id')->nullable()->constrained('criterios_resolutivos')->nullOnDelete();
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
        });

        // CHECK constraints
        DB::statement("ALTER TABLE atenciones ADD CONSTRAINT check_estado CHECK (estado IN ('registrado', 'en_espera', 'en_atencion', 'atendido', 'cancelado'))");
        DB::statement("ALTER TABLE atenciones ADD CONSTRAINT check_clasificacion CHECK (clasificacion_caso IN ('P', 'S', 'X') OR clasificacion_caso IS NULL)");

        // Índices críticos
        Schema::table('atenciones', function (Blueprint $table) {
            $table->index(['fecha_hora_ingreso', 'estado']);
            $table->index(['cie10_principal_id', 'fecha_hora_ingreso']);
            $table->index(['paciente_id', 'fecha_hora_ingreso']);
            $table->index(['sede_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atenciones');
    }
};