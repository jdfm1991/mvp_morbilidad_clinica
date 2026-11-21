<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEtario extends Model
{
    protected $table = 'grupos_etarios';
    protected $fillable = ['nombre', 'edad_min', 'edad_max', 'activo'];
    protected $casts = ['activo' => 'boolean'];
}