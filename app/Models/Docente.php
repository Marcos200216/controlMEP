<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $fillable = ['nombre', 'activo'];

    public function registroAusencias()
    {
        return $this->hasMany(RegistroAusencia::class);
    }

    public function observaciones()
    {
        return $this->hasMany(Observacion::class);
    }
}