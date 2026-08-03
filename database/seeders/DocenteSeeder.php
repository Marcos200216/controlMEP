<?php

namespace Database\Seeders;

use App\Models\Docente;
use Illuminate\Database\Seeder;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Águeda Pérez Romero',
            'Ilse Angulo Rodríguez',
            'José Mario Morales Castrillo',
            'David García García',
            'Erick Morera Castro',
            'Anthony Jeancarlos López Moya',
            'Mario Ulate Morales',
            'Diego Camacho Espinoza',
            'Eilyn Duarte Abarca',
            'Sheila Rodríguez López',
            'Mónica Cob Alfaro',
            'Evelyn Vindas Juárez',
            'Edel Ruiz Villafuerte',
            'Gabriela Hidalgo Bolaños',
            'Linneth Valdez Jiménez',
            'Amelita Muñoz Rivera',
            'Maikol Valerin Barrantes',
            'Mónica Narváez Ugarte',
            'Adrián González Chavarría',
            'Cynthia Villagra Coronado',
            'Mariela Bonilla Rodríguez',
            'Johnder Villagra Guevara',
            'M° Carmen Toruño Ramírez',
            'Maureen Cascante Viales',
            'María José Meléndez Ortega',
            'Vinicio Ruiz Ortega',
            'Allan Contreras Morales',
            'Edwin Mora Cerdas',
            'Juanita Rodríguez Molina',
            'Dinia Mairena Vargas',
            'Eduard Chavarría Guido',
            'Daniel Juárez Rojas',
            'Eida Zúñiga Villareal',
            'Laura Arias Cabrera',
            'Oscar Cortés Mendoza',
            'Lucien Ruiz Pizarro',
            'Sandra Yoconda Córdoba Jiménez',
            'Victor Matamoros Espinoza',
            'Johanna Torres Acuña',
            'Cindy Angulo Abarca',
        ];

        foreach ($nombres as $nombre) {
            Docente::create([
                'nombre' => $nombre,
                'activo' => true,
            ]);
        }
    }
}