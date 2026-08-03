<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\RegistroAusencia;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        [$desde, $hasta] = $this->calcularRangoFechas($request);

        $query = $this->aplicarFiltros(RegistroAusencia::query(), $request, $desde, $hasta);

        // Ranking de estudiantes con más ausencias (según los filtros aplicados)
        $ranking = (clone $query)
            ->select('estudiante_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('estudiante_id')
            ->orderByDesc('total')
            ->with('estudiante.seccion')
            ->limit(50)
            ->get();

        // Vista por sección (para heatmap/tabla)
        $porSeccion = (clone $query)
            ->select('seccion_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('seccion_id')
            ->orderByDesc('total')
            ->with('seccion')
            ->get();

        // Vista por nivel (para gráfica doughnut)
        $porNivel = (clone $query)
            ->join('secciones', 'secciones.id', '=', 'registro_ausencias.seccion_id')
            ->select('secciones.nivel', DB::raw('SUM(registro_ausencias.cantidad) as total'))
            ->groupBy('secciones.nivel')
            ->orderBy('secciones.nivel')
            ->get();

        // Tendencia diaria (para gráfica de línea)
        $tendenciaDiaria = (clone $query)
            ->select('fecha', DB::raw('SUM(cantidad) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Alertas de ausentismo crónico (umbral configurable)
        // Query independiente (no limitada a los 50 del ranking) para no perder
        // estudiantes que superan el umbral pero quedan fuera del top 50
        $umbral = (int) $request->input('umbral', 5);

        $cronicos = (clone $query)
            ->select('estudiante_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('estudiante_id')
            ->having('total', '>=', $umbral)
            ->orderByDesc('total')
            ->with('estudiante.seccion')
            ->get();

        $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();
        $materias = Materia::orderBy('nombre')->get();
        $docentes = Docente::orderBy('nombre')->get();

        return view('admin.reportes.index', compact(
            'ranking',
            'porSeccion',
            'porNivel',
            'tendenciaDiaria',
            'cronicos',
            'umbral',
            'secciones',
            'materias',
            'docentes',
            'desde',
            'hasta'
        ));
    }

    public function exportarExcel(Request $request)
    {
        [$desde, $hasta] = $this->calcularRangoFechas($request);

        $query = $this->aplicarFiltros(
            RegistroAusencia::with(['estudiante', 'docente', 'materia', 'seccion']),
            $request,
            $desde,
            $hasta
        );

        $registros = $query->orderBy('fecha')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AusenciasExport($registros),
            'reporte-ausencias-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Calcula el rango de fechas a usar: toma lo que venga en el request,
     * lo completa con el mes actual si falta, y corrige el orden si el
     * usuario invirtió "desde" y "hasta" (avisando vía sesión flash).
     */
    private function calcularRangoFechas(Request $request): array
    {
        $desde = $request->filled('desde')
            ? $request->desde
            : Carbon::now()->startOfMonth()->toDateString();

        $hasta = $request->filled('hasta')
            ? $request->hasta
            : Carbon::now()->endOfMonth()->toDateString();

        if ($desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];

            session()->flash(
                'reporte_aviso',
                'El rango de fechas estaba invertido ("Desde" era posterior a "Hasta"), así que se intercambiaron automáticamente.'
            );
        }

        return [$desde, $hasta];
    }

    /**
     * Aplica los filtros comunes de sección, materia, docente, nivel y rango de fechas.
     */
    private function aplicarFiltros($query, Request $request, string $desde, string $hasta)
    {
        if ($request->filled('seccion_id')) {
            $query->where('seccion_id', $request->seccion_id);
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        if ($request->filled('docente_id')) {
            $query->where('docente_id', $request->docente_id);
        }

        if ($request->filled('nivel')) {
            // Subquery en vez de whereHas(), para no mezclar dos estilos distintos
            // (whereHas por un lado, join directo por otro en porNivel) y evitar
            // conflictos de alias si esta misma query se clona y se le agrega
            // otro join a "secciones" más adelante (como hace porNivel).
            $query->whereIn('seccion_id', function ($sub) use ($request) {
                $sub->select('id')
                    ->from('secciones')
                    ->where('nivel', $request->nivel);
            });
        }

        $query->whereDate('fecha', '>=', $desde);
        $query->whereDate('fecha', '<=', $hasta);

        return $query;
    }
}