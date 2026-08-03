<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use App\Models\Seccion;
use App\Models\Estudiante;
use App\Models\RegistroAusencia;
use App\Models\Observacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AusenciaController extends Controller
{
    const SESSION_KEY = 'reporte_pendiente';

    // Un mismo estudiante puede tener ausencias de más de un docente/materia
    // en el mismo reporte (ej. un docente que da dos materias a la sección).
    // Por eso el borrador se anida por esta clave, no solo por estudiante.
    private function claveContexto(int $docenteId, int $materiaId): string
    {
        return $docenteId . '_' . $materiaId;
    }

    // Pantalla única: selects + modal de estudiante + calendario + observaciones + agregados
    public function index()
    {
        $docentes = Docente::where('activo', true)->orderBy('nombre')->get();
        $materias = Materia::orderBy('nombre')->get();
        $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();

        $estudiantes = Estudiante::where('activo', true)
            ->orderBy('nombre_completo')
            ->get(['id', 'nombre_completo', 'seccion_id']);

        // Cada vez que se entra/recarga esta pantalla, se descarta cualquier
        // reporte a medio hacer. El docente empieza siempre de cero.
        session()->forget(self::SESSION_KEY);
        $agregadosIniciales = [];

        return view('ausencias.index', compact(
            'docentes', 'materias', 'secciones', 'estudiantes', 'agregadosIniciales'
        ));
    }

    // AJAX: al elegir un estudiante en el modal, trae sus ausencias guardadas y de borrador
    public function datosEstudiante(Request $request)
    {
        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'docente_id' => 'required|exists:docentes,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $guardadas = RegistroAusencia::where('estudiante_id', $data['estudiante_id'])
            ->where('docente_id', $data['docente_id'])
            ->where('materia_id', $data['materia_id'])
            ->get()
            ->mapWithKeys(fn ($r) => [$r->fecha->format('Y-m-d') => $r->cantidad]);

        $clave = $this->claveContexto($data['docente_id'], $data['materia_id']);
        $borrador = session(self::SESSION_KEY, []);
        $borradorEstudiante = $borrador['estudiantes'][$data['estudiante_id']]['contextos'][$clave]['ausencias'] ?? [];

        return response()->json([
            'guardadas' => $guardadas,
            'borrador' => $borradorEstudiante,
        ]);
    }

    // AJAX: marcar/actualizar cantidad de ausencias de un día (queda en sesión, no en BD)
    public function guardarDia(Request $request)
    {
        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'docente_id' => 'required|exists:docentes,id',
            'materia_id' => 'required|exists:materias,id',
            'fecha' => 'required|date',
            'cantidad' => 'required|integer|min:1|max:5',
        ]);

        $clave = $this->claveContexto($data['docente_id'], $data['materia_id']);
        $borrador = session(self::SESSION_KEY, ['estudiantes' => []]);

        $borrador['estudiantes'][$data['estudiante_id']]['contextos'][$clave]['docente_id'] = $data['docente_id'];
        $borrador['estudiantes'][$data['estudiante_id']]['contextos'][$clave]['materia_id'] = $data['materia_id'];
        $borrador['estudiantes'][$data['estudiante_id']]['contextos'][$clave]['ausencias'][$data['fecha']] = $data['cantidad'];

        session([self::SESSION_KEY => $borrador]);

        return response()->json(['ok' => true]);
    }

    // AJAX: eliminar el registro de un día del borrador
    public function eliminarDia(Request $request)
    {
        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'docente_id' => 'required|exists:docentes,id',
            'materia_id' => 'required|exists:materias,id',
            'fecha' => 'required|date',
        ]);

        $clave = $this->claveContexto($data['docente_id'], $data['materia_id']);
        $borrador = session(self::SESSION_KEY);
        abort_if(!$borrador, 419, 'Sesión expirada.');

        unset($borrador['estudiantes'][$data['estudiante_id']]['contextos'][$clave]['ausencias'][$data['fecha']]);
        session([self::SESSION_KEY => $borrador]);

        return response()->json(['ok' => true]);
    }

    // Botón "Guardar datos": fija observación + contexto (docente/materia/sección) por estudiante
    public function guardarDatos(Request $request)
    {
        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'docente_id' => 'required|exists:docentes,id',
            'materia_id' => 'required|exists:materias,id',
            'seccion_id' => 'required|exists:secciones,id',
            'observacion' => 'nullable|string',
        ]);

        $estudiante = Estudiante::findOrFail($data['estudiante_id']);
        $clave = $this->claveContexto($data['docente_id'], $data['materia_id']);

        $borrador = session(self::SESSION_KEY, ['estudiantes' => []]);

        $borrador['estudiantes'][$estudiante->id]['nombre'] = $estudiante->nombre_completo;
        $borrador['estudiantes'][$estudiante->id]['contextos'][$clave]['docente_id'] = $data['docente_id'];
        $borrador['estudiantes'][$estudiante->id]['contextos'][$clave]['materia_id'] = $data['materia_id'];
        $borrador['estudiantes'][$estudiante->id]['contextos'][$clave]['seccion_id'] = $data['seccion_id'];
        $borrador['estudiantes'][$estudiante->id]['contextos'][$clave]['observacion'] = $data['observacion'] ?? null;

        session([self::SESSION_KEY => $borrador]);

        return response()->json([
            'id' => $estudiante->id,
            'nombre' => $estudiante->nombre_completo,
        ]);
    }

    // AJAX: quitar un estudiante ya agregado (botón "✕" del chip) — se quita con TODOS sus contextos
    public function quitarEstudiante(Request $request)
    {
        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
        ]);

        $borrador = session(self::SESSION_KEY);
        abort_if(!$borrador, 419, 'Sesión expirada.');

        unset($borrador['estudiantes'][$data['estudiante_id']]);
        session([self::SESSION_KEY => $borrador]);

        return response()->json(['ok' => true]);
    }

    // Botón "Enviar reporte": guarda todo el borrador, cada contexto (estudiante+docente+materia) por separado
    public function enviarReporte()
    {
        $borrador = session(self::SESSION_KEY);
        abort_if(!$borrador || empty($borrador['estudiantes']), 419, 'No hay estudiantes agregados.');

        $incompletos = [];

        DB::transaction(function () use ($borrador, &$incompletos) {
            foreach ($borrador['estudiantes'] as $estudianteId => $datosEstudiante) {
                $contextos = $datosEstudiante['contextos'] ?? [];

                foreach ($contextos as $contexto) {
                    $docenteId = $contexto['docente_id'] ?? null;
                    $materiaId = $contexto['materia_id'] ?? null;
                    $seccionId = $contexto['seccion_id'] ?? null;

                    if (!$docenteId || !$materiaId || !$seccionId) {
                        // Quedaron días marcados en el calendario para este estudiante
                        // sin pasar por "Guardar datos": no hay sección ni contexto
                        // completo, así que no se puede insertar el registro.
                        $incompletos[] = $datosEstudiante['nombre'] ?? "Estudiante #$estudianteId";
                        continue;
                    }

                    foreach (($contexto['ausencias'] ?? []) as $fecha => $cantidad) {
                        RegistroAusencia::updateOrCreate(
                            [
                                'estudiante_id' => $estudianteId,
                                'docente_id' => $docenteId,
                                'materia_id' => $materiaId,
                                'fecha' => $fecha,
                            ],
                            [
                                'seccion_id' => $seccionId,
                                'cantidad' => $cantidad,
                            ]
                        );
                    }

                    if (!empty($contexto['observacion'])) {
                        Observacion::create([
                            'estudiante_id' => $estudianteId,
                            'docente_id' => $docenteId,
                            'materia_id' => $materiaId,
                            'texto' => $contexto['observacion'],
                            'fecha_envio' => now(),
                        ]);
                    }
                }
            }
        });

        session()->forget(self::SESSION_KEY);

        $mensaje = 'Reporte enviado correctamente.';
        if (!empty($incompletos)) {
            $mensaje .= ' Nota: quedaron días marcados sin confirmar con "Guardar datos" para: '
                . implode(', ', array_unique($incompletos)) . '. No se incluyeron en el reporte.';
        }

        return redirect()->route('ausencias.index')->with('exito', $mensaje);
    }
}