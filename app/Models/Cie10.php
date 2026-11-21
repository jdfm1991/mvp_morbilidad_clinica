<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cie10 extends Model
{
    protected $table = 'cie10';
    protected $fillable = ['codigo', 'descripcion', 'categoria', 'capitulo', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function atencionesPrincipales(): HasMany
    {
        return $this->hasMany(Atencion::class, 'cie10_principal_id');
    }
}