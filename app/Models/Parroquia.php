<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parroquia extends Model
{
    protected $fillable = ['municipio_id', 'nombre', 'codigo'];

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }
}