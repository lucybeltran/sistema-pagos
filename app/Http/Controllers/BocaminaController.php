<?php

namespace App\Http\Controllers;

use App\Models\Bocamina;
use Illuminate\Http\Request;

class BocaminaController extends Controller
{
    public function index()
    {
        $bocaminas = Bocamina::withCount('trabajadores')->get();
        return view('bocaminas.index', compact('bocaminas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:bocaminas,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Bocamina::create($data);

        return redirect()->route('bocaminas.index')->with('success', 'Bocamina registrada exitosamente.');
    }

    public function update(Request $request, Bocamina $bocamina)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:bocaminas,nombre,' . $bocamina->id,
            'descripcion' => 'nullable|string',
        ]);

        $bocamina->update($data);

        return redirect()->route('bocaminas.index')->with('success', 'Bocamina actualizada exitosamente.');
    }

    public function destroy(Bocamina $bocamina)
    {
        // Check if there are workers assigned
        if ($bocamina->trabajadores()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar la bocamina porque tiene trabajadores asignados.']);
        }

        $bocamina->delete();

        return redirect()->route('bocaminas.index')->with('success', 'Bocamina eliminada exitosamente.');
    }
}
