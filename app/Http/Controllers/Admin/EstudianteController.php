<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Seccion;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
   public function index(Request $request)
{
    $query = Estudiante::with('seccion');

    if ($request->filled('seccion_id')) {
        $query->where('seccion_id', $request->seccion_id);
    }

    if ($request->filled('buscar')) {
        $query->where('nombre_completo', 'like', '%' . $request->buscar . '%');
    }

    $estudiantes = $query->orderBy('nombre_completo')->get();

    $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();

    return view('admin.estudiantes.index', compact('estudiantes', 'secciones'));
}
    public function create()
    {
        $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();
        return view('admin.estudiantes.create', compact('secciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'seccion_id' => 'required|exists:secciones,id',
        ]);

        Estudiante::create($data);

        return redirect()->route('admin.estudiantes.index')->with('exito', 'Estudiante creado.');
    }

    public function edit(Estudiante $estudiante)
    {
        $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();
        return view('admin.estudiantes.edit', compact('estudiante', 'secciones'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'seccion_id' => 'required|exists:secciones,id',
            'activo' => 'boolean',
        ]);

        $estudiante->update($data);

        return redirect()->route('admin.estudiantes.index')->with('exito', 'Estudiante actualizado.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();
        return back()->with('exito', 'Estudiante eliminado.');
    }
}