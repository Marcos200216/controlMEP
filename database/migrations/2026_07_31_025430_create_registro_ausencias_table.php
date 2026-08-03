<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registro_ausencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->restrictOnDelete();
            $table->foreignId('docente_id')->constrained('docentes')->restrictOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->restrictOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->restrictOnDelete();
            $table->date('fecha');
            $table->unsignedTinyInteger('cantidad'); // 1 a 5
            $table->timestamps();

            $table->unique(['estudiante_id', 'docente_id', 'materia_id', 'fecha'], 'reg_ausencias_unico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_ausencias');
    }
};