<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Atencion extends Model
{
    protected $table = 'atenciones';
    protected $fillable = [
        'sede_id',
        'paciente_id',
        'user_admision_id',
        'user_medico_id',
        'fecha_hora_ingreso',
        'fecha_hora_atencion',
        'fecha_hora_egreso',
        'motivo_consulta',
        'presion_arterial_sistolica',
        'presion_arterial_diastolica',
        'temperatura',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'saturacion_oxigeno',
        'peso',
        'talla',
        'tipo_atencion_id',
        'estado',
        'cie10_principal_id',
        'clasificacion_caso',
        'criterio_resolutivo_id',
        'observaciones'
    ];
    protected $casts = [
        'fecha_hora_ingreso' => 'datetime',
        'fecha_hora_atencion' => 'datetime',
        'fecha_hora_egreso' => 'datetime',
    ];

    use LogsActivity;


    // Relaciones
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
    public function admision(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_admision_id');
    }
    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_medico_id');
    }
    public function tipoAtencion(): BelongsTo
    {
        return $this->belongsTo(TipoAtencion::class);
    }
    public function cie10Principal(): BelongsTo
    {
        return $this->belongsTo(Cie10::class, 'cie10_principal_id');
    }
    public function criterioResolutivo(): BelongsTo
    {
        return $this->belongsTo(CriterioResolutivo::class);
    }
    public function diagnosticosSecundarios(): HasMany
    {
        return $this->hasMany(AtencionDiagnosticoSecundario::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Solo registra los campos que cambian, sin ensuciar la base de datos
            ->logOnlyDirty()
            // No falla si no hay cambios
            ->dontSubmitEmptyLogs()
            // Opcional: puedes especificar qué campos registrar exactamente
            ->logFillable();
    }
}
