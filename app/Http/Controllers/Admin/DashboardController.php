<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\RegistroAusencia;
use App\Models\Seccion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- Mes seleccionado (por ahora "mes actual" por defecto, con selector) ---
        $mesParam = $request->input('mes', now()->format('Y-m'));
        try {
            $mesSeleccionado = Carbon::createFromFormat('Y-m', $mesParam)->startOfMonth();
        } catch (\Exception $e) {
            $mesSeleccionado = now()->startOfMonth();
        }

        $inicioMes = $mesSeleccionado->copy()->startOfMonth();
        $finMes = $mesSeleccionado->copy()->endOfMonth();

        // --- KPI: total de ausencias del mes ---
        $totalAusenciasMes = RegistroAusencia::whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('cantidad');

        // --- KPI: estudiante con más ausencias del mes ---
        $estudianteTop = RegistroAusencia::whereBetween('fecha', [$inicioMes, $finMes])
            ->select('estudiante_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('estudiante_id')
            ->orderByDesc('total')
            ->with('estudiante.seccion')
            ->first();

        // --- KPI: sección más afectada del mes ---
        $seccionTop = RegistroAusencia::whereBetween('fecha', [$inicioMes, $finMes])
            ->select('seccion_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('seccion_id')
            ->orderByDesc('total')
            ->with('seccion')
            ->first();

        // --- KPI: docentes activos que no han reportado nada en el mes ---
        $docentesReportaron = RegistroAusencia::whereBetween('fecha', [$inicioMes, $finMes])
            ->distinct()
            ->pluck('docente_id');

        $docentesPendientes = Docente::where('activo', true)
            ->whereNotIn('id', $docentesReportaron)
            ->orderBy('nombre')
            ->get();

        // --- Ranking de estudiantes con más ausencias del mes (top 15) ---
        $topEstudiantes = RegistroAusencia::whereBetween('fecha', [$inicioMes, $finMes])
            ->select('estudiante_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('estudiante_id')
            ->orderByDesc('total')
            ->take(15)
            ->with('estudiante.seccion')
            ->get();

        // --- Filtro por sección: alumnos de la sección elegida con sus ausencias del mes ---
        // (Solo se usa para el primer render / si llegan por URL con seccion_id; los cambios
        // posteriores del select los maneja ausenciasPorSeccionAjax vía fetch, sin recargar.)
        $seccionFiltroId = $request->input('seccion_id');
        $alumnosSeccion = collect();
        if ($seccionFiltroId) {
            $alumnosSeccion = RegistroAusencia::whereBetween('fecha', [$inicioMes, $finMes])
                ->where('seccion_id', $seccionFiltroId)
                ->select('estudiante_id', DB::raw('SUM(cantidad) as total'))
                ->groupBy('estudiante_id')
                ->orderByDesc('total')
                ->with('estudiante')
                ->get();
        }

        $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();

        // --- Opciones del selector de mes: últimos 12 meses ---
        $mesesDisponibles = collect(range(0, 11))->map(function ($i) {
            $fecha = now()->subMonths($i);
            return [
                'valor' => $fecha->format('Y-m'),
                'etiqueta' => ucfirst($fecha->translatedFormat('F Y')),
            ];
        });

        return view('admin.dashboard', compact(
            'totalAusenciasMes',
            'estudianteTop',
            'seccionTop',
            'docentesPendientes',
            'topEstudiantes',
            'mesesDisponibles',
            'mesSeleccionado',
            'secciones',
            'seccionFiltroId',
            'alumnosSeccion'
        ));
    }

    /**
     * AJAX: alumnos de una sección con sus ausencias del mes.
     * Usado por el <select> de "Ausencias por sección" para actualizar
     * la tabla sin recargar la página ni mover el scroll.
     */
    public function ausenciasPorSeccionAjax(Request $request)
    {
        $seccionId = $request->input('seccion_id');

        if (!$seccionId) {
            return response()->json(['seccion' => null, 'alumnos' => []]);
        }

        [$inicio, $fin] = $this->rangoDelMes($request->input('mes'));

        $seccion = Seccion::find($seccionId);

        $alumnos = RegistroAusencia::whereBetween('fecha', [$inicio, $fin])
            ->where('seccion_id', $seccionId)
            ->select('estudiante_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('estudiante_id')
            ->orderByDesc('total')
            ->with('estudiante')
            ->get();

        return response()->json([
            'seccion' => $seccion->nombre ?? null,
            'alumnos' => $alumnos->map(fn ($f) => [
                'id' => $f->estudiante_id,
                'nombre' => $f->estudiante->nombre_completo ?? '—',
                'total' => $f->total,
            ]),
        ]);
    }

    /**
     * AJAX: autocompletado de estudiantes por nombre.
     */
    public function buscarEstudiantes(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $estudiantes = Estudiante::where('nombre_completo', 'like', "%{$q}%")
            ->with('seccion')
            ->orderBy('nombre_completo')
            ->limit(8)
            ->get();

        return response()->json($estudiantes->map(fn ($e) => [
            'id' => $e->id,
            'nombre' => $e->nombre_completo,
            'seccion' => $e->seccion->nombre ?? '—',
        ]));
    }

    /**
     * AJAX: detalle de un estudiante para el modal (info + ausencias del mes).
     */
    public function detalleEstudiante(Request $request, Estudiante $estudiante)
    {
        [$inicio, $fin] = $this->rangoDelMes($request->input('mes'));

        $registros = RegistroAusencia::where('estudiante_id', $estudiante->id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->with(['materia', 'docente'])
            ->orderByDesc('fecha')
            ->get();

        $totalHistorico = RegistroAusencia::where('estudiante_id', $estudiante->id)->sum('cantidad');

        return response()->json([
            'nombre' => $estudiante->nombre_completo,
            'seccion' => $estudiante->seccion->nombre ?? '—',
            'total_mes' => $registros->sum('cantidad'),
            'total_historico' => $totalHistorico,
            'registros' => $registros->map(fn ($r) => [
                'fecha' => $r->fecha->format('d/m/Y'),
                'materia' => $r->materia->nombre ?? '—',
                'docente' => $r->docente->nombre ?? '—',
                'cantidad' => $r->cantidad,
            ]),
        ]);
    }

    /**
     * AJAX: autocompletado de docentes por nombre.
     */
    public function buscarDocentes(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $docentes = Docente::where('nombre', 'like', "%{$q}%")
            ->orderBy('nombre')
            ->limit(8)
            ->get(['id', 'nombre']);

        return response()->json($docentes);
    }

    /**
     * AJAX: detalle de un docente para el modal (cuántas ausencias reportó y a quién).
     */
    public function detalleDocente(Request $request, Docente $docente)
    {
        [$inicio, $fin] = $this->rangoDelMes($request->input('mes'));

        $registros = RegistroAusencia::where('docente_id', $docente->id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->with(['estudiante.seccion', 'materia'])
            ->orderByDesc('fecha')
            ->get();

        return response()->json([
            'nombre' => $docente->nombre,
            'total_mes' => $registros->sum('cantidad'),
            'registros' => $registros->map(fn ($r) => [
                'fecha' => $r->fecha->format('d/m/Y'),
                'estudiante' => $r->estudiante->nombre_completo ?? '—',
                'seccion' => $r->estudiante->seccion->nombre ?? '—',
                'materia' => $r->materia->nombre ?? '—',
                'cantidad' => $r->cantidad,
            ]),
        ]);
    }

    private function rangoDelMes(?string $mesParam): array
    {
        try {
            $mes = Carbon::createFromFormat('Y-m', $mesParam ?? now()->format('Y-m'))->startOfMonth();
        } catch (\Exception $e) {
            $mes = now()->startOfMonth();
        }

        return [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()];
    }
}