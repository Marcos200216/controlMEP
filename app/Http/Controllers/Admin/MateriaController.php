<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::orderBy('nombre')->get();
        return view('admin.materias.index', compact('materias'));
    }

    public function store(Request $request)
    {
        $request->merge(['nombre' => trim((string) $request->input('nombre'))]);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('materias', 'nombre')],
        ], [
            'nombre.unique' => 'Ya existe una materia registrada con ese nombre.',
        ]);

        Materia::create($data);

        return back()->with('exito', 'Materia creada.');
    }

    public function update(Request $request, Materia $materia)
    {
        $request->merge(['nombre' => trim((string) $request->input('nombre'))]);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('materias', 'nombre')->ignore($materia->id)],
        ], [
            'nombre.unique' => 'Ya existe una materia registrada con ese nombre.',
        ]);

        $materia->update($data);

        return back()->with('exito', 'Materia actualizada.');
    }

    public function destroy(Materia $materia)
    {
        // Evita el error 500 por violación de foreign key si la materia
        // ya tiene ausencias u observaciones registradas asociadas.
        if ($materia->registroAusencias()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar "' . $materia->nombre . '" porque tiene ausencias registradas asociadas.'
            );
        }

        if ($materia->observaciones()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar "' . $materia->nombre . '" porque tiene observaciones registradas asociadas.'
            );
        }

        $materia->delete();

        return back()->with('exito', 'Materia eliminada.');
    }
}