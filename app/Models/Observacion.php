<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    // Eloquent pluraliza "Observacion" como "observacions" (regla en inglés),
    // pero la tabla real se llama "observaciones". Se especifica explícito
    // para evitar el error "Base table or view not found".
    protected $table = 'observaciones';

    protected $fillable = [
        'estudiante_id', 'docente_id', 'materia_id', 'texto', 'fecha_envio',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
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
}