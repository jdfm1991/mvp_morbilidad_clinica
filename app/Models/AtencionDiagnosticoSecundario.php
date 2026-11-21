<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtencionDiagnosticoSecundario extends Model
{
    protected $table = 'atencion_diagnosticos_secundarios';
    protected $fillable = ['atencion_id', 'cie10_id'];

    public function atencion(): BelongsTo { return $this->belongsTo(Atencion::class); }
    public function cie10(): BelongsTo { return $this->belongsTo(Cie10::class); }
}