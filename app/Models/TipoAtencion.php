<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoAtencion extends Model
{
    protected $table = 'tipos_atencion';
    protected $fillable = ['nombre', 'codigo', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function atenciones(): HasMany
    {
        return $this->hasMany(Atencion::class);
    }
}