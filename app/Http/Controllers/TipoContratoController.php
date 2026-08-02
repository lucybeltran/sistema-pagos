<?php

namespace App\Http\Controllers;

use App\Models\TipoContrato;
use Illuminate\Http\Request;

class TipoContratoController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoContrato::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $contratos = $query->get();

        return view('contratos.tipos', compact('contratos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_contrato,nombre',
            'estado' => 'required|in:activo,inactivo',
        ]);

        TipoContrato::create($data);

        return redirect()->route('tipos-contrato.index')->with('success', 'Tipo de contrato creado exitosamente.');
    }

    public function update(Request $request, TipoContrato $tipos_contrato)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_contrato,nombre,' . $tipos_contrato->id,
            'estado' => 'required|in:activo,inactivo',
        ]);

        $tipos_contrato->update($data);

        return redirect()->route('tipos-contrato.index')->with('success', 'Tipo de contrato actualizado exitosamente.');
    }

    public function destroy(TipoContrato $tipos_contrato)
    {
        // Block delete if they are linked to worker assignments
        if ($tipos_contrato->contratos()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar porque este tipo de contrato está asignado a contratos activos de trabajadores. Puede desactivarlo en su lugar.']);
        }

        $tipos_contrato->delete();

        return redirect()->route('tipos-contrato.index')->with('success', 'Tipo de contrato eliminado con éxito.');
    }
}
