<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atencion_diagnosticos_secundarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atencion_id')->constrained('atenciones')->cascadeOnDelete();
            $table->foreignId('cie10_id')->constrained('cie10')->restrictOnDelete();
            $table->timestamps();
            
            $table->unique(['atencion_id', 'cie10_id'], 'unique_diag_secundario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atencion_diagnosticos_secundarios');
    }
};