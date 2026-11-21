<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cie10', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('descripcion', 255);
            $table->string('categoria', 10)->nullable()->index();
            $table->string('capitulo', 20)->nullable()->index();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índice para búsquedas rápidas de texto en PostgreSQL
            $table->index('descripcion'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cie10');
    }
};