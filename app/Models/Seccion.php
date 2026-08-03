<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';

    protected $fillable = ['nombre', 'nivel'];

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }

    public function registroAusencias()
    {
        return $this->hasMany(RegistroAusencia::class);
    }
}