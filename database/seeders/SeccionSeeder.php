<?php

namespace Database\Seeders;

use App\Models\Seccion;
use Illuminate\Database\Seeder;

class SeccionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(7, 11) as $nivel) {
            foreach (range(1, 5) as $numero) {
                Seccion::create([
                    'nombre' => "{$nivel}-{$numero}",
                    'nivel' => $nivel,
                ]);
            }
        }
    }
}