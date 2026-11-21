<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $fillable = [
        'sede_id', 'identificacion', 'codigo_interno', 'nombres', 'apellidos',
        'fecha_nacimiento', 'sexo', 'telefono', 'estado_id', 'municipio_id',
        'parroquia_id', 'direccion', 'activo'
    ];
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function estado(): BelongsTo { return $this->belongsTo(Estado::class); }
    public function municipio(): BelongsTo { return $this->belongsTo(Municipio::class); }
    public function parroquia(): BelongsTo { return $this->belongsTo(Parroquia::class); }
    public function atenciones(): HasMany { return $this->hasMany(Atencion::class); }

    // Accessor para calcular edad automáticamente
    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento->age;
    }
}