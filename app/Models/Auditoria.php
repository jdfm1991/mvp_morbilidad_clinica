<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    public $timestamps = false; // Solo usamos created_at manual
    protected $fillable = [
        'user_id', 'accion', 'modelo', 'modelo_id', 
        'datos_anteriores', 'datos_nuevos', 'ip', 'created_at'
    ];
    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
    ];
}