<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::orderBy('nombre')->get();
        return view('admin.docentes.index', compact('docentes'));
    }

    public function store(Request $request)
    {
        // Trim antes de validar para que "Juan Pérez" y "Juan Pérez " (con
        // espacio final) cuenten como el mismo nombre para la regla unique.
        $request->merge(['nombre' => trim((string) $request->input('nombre'))]);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('docentes', 'nombre')],
        ], [
            'nombre.unique' => 'Ya existe un docente registrado con ese nombre.',
        ]);

        Docente::create($data);

        return back()->with('exito', 'Docente creado.');
    }

    public function update(Request $request, Docente $docente)
    {
        $request->merge(['nombre' => trim((string) $request->input('nombre'))]);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('docentes', 'nombre')->ignore($docente->id)],
        ], [
            'nombre.unique' => 'Ya existe un docente registrado con ese nombre.',
        ]);

        $docente->update($data);

        return back()->with('exito', 'Docente actualizado.');
    }

    public function destroy(Docente $docente)
    {
        // Evita el error 500 por violación de foreign key si el docente
        // ya tiene ausencias u observaciones registradas asociadas.
        if ($docente->registroAusencias()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar a "' . $docente->nombre . '" porque tiene ausencias registradas asociadas.'
            );
        }

        if ($docente->observaciones()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar a "' . $docente->nombre . '" porque tiene observaciones registradas asociadas.'
            );
        }

        $docente->delete();

        return back()->with('exito', 'Docente eliminado.');
    }
}