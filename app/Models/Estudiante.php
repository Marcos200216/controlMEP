<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $fillable = ['nombre_completo', 'seccion_id', 'activo'];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    public function registroAusencias()
    {
        return $this->hasMany(RegistroAusencia::class);
    }

    public function observaciones()
    {
        return $this->hasMany(Observacion::class);
    }
}