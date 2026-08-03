<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroAusencia extends Model
{
    protected $table = 'registro_ausencias';

    protected $fillable = [
        'estudiante_id', 'docente_id', 'materia_id', 'seccion_id', 'fecha', 'cantidad',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }
}