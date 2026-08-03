<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        $materias = [
            'Español',
            'Matemáticas',
            'Estudios Sociales',
            'Ciencias',
            'Cívica',
            'Inglés',
            'Francés',
            'Religión',
            'Educación Física',
            'Hogar',
            'Industriales',
            'Música',
            'Artes Plásticas',
            'Psicología',
            'Filosofía',
            'Formación Tecnológica',
            'Tecnologías',
        ];

        foreach ($materias as $nombre) {
            Materia::create(['nombre' => $nombre]);
        }
    }
}