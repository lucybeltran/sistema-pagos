<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Trabajador;
use App\Models\Contrato;
use Illuminate\Http\Request;

class TrabajoController extends Controller
{
    public function index(Request $request)
    {
        $query = Trabajo::with(['trabajador', 'contrato', 'pago']);

        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('pagado')) {
            $query->where('pagado', $request->pagado === 'si');
        }

        $trabajos = $query->orderBy('fecha', 'desc')->get();
        
        $trabajadores = Trabajador::where('estado', 'activo')->get();
        // Load active contracts to allow associating jobs
        $contratos = Contrato::where('estado', 'activo')->get();

        return view('trabajos.index', compact('trabajos', 'trabajadores', 'contratos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'contrato_id' => 'nullable|exists:contratos,id',
            'fecha' => 'required|date',
            'tipo' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
        ]);

        $subtotal = $request->cantidad * $request->precio_unitario;

        // If a contract is selected, verify it belongs to the worker and it's active
        if ($request->filled('contrato_id')) {
            $contrato = Contrato::find($request->contrato_id);
            if ($contrato->trabajador_id != $request->trabajador_id) {
                return back()->withErrors(['contrato_id' => 'El contrato seleccionado no pertenece al trabajador especificado.'])->withInput();
            }
            if ($contrato->estado !== 'activo') {
                return back()->withErrors(['contrato_id' => 'El contrato seleccionado no se encuentra activo.'])->withInput();
            }
        }

        Trabajo::create([
            'trabajador_id' => $request->trabajador_id,
            'contrato_id' => $request->contrato_id,
            'fecha' => $request->fecha,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'subtotal' => $subtotal,
            'observacion' => $request->observacion,
            'pagado' => false,
            'pago_id' => null,
        ]);

        return redirect()->route('trabajos.index')->with('success', 'Trabajo registrado exitosamente.');
    }

    public function update(Request $request, Trabajo $trabajo)
    {
        if ($trabajo->pagado) {
            return back()->withErrors(['error' => 'No se puede modificar un trabajo que ya ha sido pagado.']);
        }

        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'contrato_id' => 'nullable|exists:contratos,id',
            'fecha' => 'required|date',
            'tipo' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
        ]);

        // If a contract is selected, verify it belongs to the worker and it's active
        if ($request->filled('contrato_id')) {
            $contrato = Contrato::find($request->contrato_id);
            if ($contrato->trabajador_id != $request->trabajador_id) {
                return back()->withErrors(['contrato_id' => 'El contrato seleccionado no pertenece al trabajador especificado.'])->withInput();
            }
        }

        $subtotal = $request->cantidad * $request->precio_unitario;

        $trabajo->update([
            'trabajador_id' => $request->trabajador_id,
            'contrato_id' => $request->contrato_id,
            'fecha' => $request->fecha,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'subtotal' => $subtotal,
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('trabajos.index')->with('success', 'Trabajo actualizado exitosamente.');
    }

    public function destroy(Trabajo $trabajo)
    {
        if ($trabajo->pagado) {
            return back()->withErrors(['error' => 'No se puede eliminar un trabajo que ya ha sido pagado.']);
        }

        $trabajo->delete();

        return redirect()->route('trabajos.index')->with('success', 'Trabajo eliminado exitosamente.');
    }
}
