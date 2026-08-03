<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use Illuminate\Http\Request;

class SeccionController extends Controller
{
    public function index()
    {
        $secciones = Seccion::orderBy('nivel')->orderBy('nombre')->get();
        return view('admin.secciones.index', compact('secciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:secciones,nombre',
            'nivel' => 'required|integer|min:7|max:11',
        ]);

        Seccion::create($data);

        return back()->with('exito', 'Sección creada.');
    }

    public function update(Request $request, Seccion $seccion)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:secciones,nombre,' . $seccion->id,
            'nivel' => 'required|integer|min:7|max:11',
        ]);

        $seccion->update($data);

        return back()->with('exito', 'Sección actualizada.');
    }

    public function destroy(Seccion $seccion)
    {
        try {
            $seccion->delete();
            return back()->with('exito', 'Sección eliminada.');
        } catch (\Illuminate\Database\QueryException $e) {
            // 23000 = violación de integridad referencial (llave foránea).
            // Cualquier otro código es un error real de BD y debe propagarse,
            // no disfrazarse de "tiene estudiantes asignados".
            if ($e->getCode() === '23000') {
                return back()->with('error', 'No se puede eliminar esta sección porque tiene estudiantes asignados. Reasigna o elimina primero a esos estudiantes.');
            }

            throw $e;
        }
    }
}