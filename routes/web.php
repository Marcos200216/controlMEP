<?php

use App\Http\Controllers\AusenciaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocenteController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\SeccionController;
use App\Http\Controllers\Admin\EstudianteController;
use App\Http\Controllers\Admin\ReporteController;
use Illuminate\Support\Facades\Route;

// Pantalla de entrada: "Soy docente" / "Panel administrativo"
Route::get('/', function () {
    return view('welcome');
})->name('inicio');

// ---------------------------------------------------------
// Flujo del docente (sin login) — pantalla única
// ---------------------------------------------------------
Route::prefix('ausencias')->name('ausencias.')->group(function () {
    Route::get('/', [AusenciaController::class, 'index'])->name('index');
    Route::get('/datos-estudiante', [AusenciaController::class, 'datosEstudiante'])->name('datosEstudiante');
    Route::post('/dia', [AusenciaController::class, 'guardarDia'])->name('dia.guardar');
    Route::delete('/dia', [AusenciaController::class, 'eliminarDia'])->name('dia.eliminar');
    Route::post('/guardar-datos', [AusenciaController::class, 'guardarDatos'])->name('guardarDatos');
    Route::delete('/quitar-estudiante', [AusenciaController::class, 'quitarEstudiante'])->name('quitarEstudiante');
    Route::post('/enviar', [AusenciaController::class, 'enviarReporte'])->name('enviar');
});

// ---------------------------------------------------------
// Panel administrativo (con login)
// ---------------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {

    // Login (sin auth todavía)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    // Todo lo demás requiere sesión iniciada
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/ausencias-seccion', [DashboardController::class, 'ausenciasPorSeccionAjax'])->name('dashboard.ausenciasPorSeccion');
Route::get('/dashboard/buscar-estudiantes', [DashboardController::class, 'buscarEstudiantes'])->name('dashboard.buscarEstudiantes');
Route::get('/dashboard/estudiante/{estudiante}', [DashboardController::class, 'detalleEstudiante'])->name('dashboard.detalleEstudiante');
Route::get('/dashboard/buscar-docentes', [DashboardController::class, 'buscarDocentes'])->name('dashboard.buscarDocentes');
Route::get('/dashboard/docente/{docente}', [DashboardController::class, 'detalleDocente'])->name('dashboard.detalleDocente');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('docentes', DocenteController::class);
        Route::resource('materias', MateriaController::class);
        Route::resource('secciones', SeccionController::class);
        Route::resource('estudiantes', EstudianteController::class);

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar', [ReporteController::class, 'exportarExcel'])->name('reportes.exportar');
    });
});