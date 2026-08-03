<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['nombre'];

    public function registroAusencias()
    {
        return $this->hasMany(RegistroAusencia::class);
    }

    public function observaciones()
    {
        return $this->hasMany(Observacion::class);
    }
}