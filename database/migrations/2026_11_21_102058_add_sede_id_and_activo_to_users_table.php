<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable porque un administrador global podría no pertenecer a una sede específica
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropColumn(['sede_id', 'activo']);
        });
    }
};