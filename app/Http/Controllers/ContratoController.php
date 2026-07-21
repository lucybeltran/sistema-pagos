<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Trabajador;
use App\Models\Bocamina;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrato::with(['trabajador', 'bocamina']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('codigo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
        }

        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->filled('bocamina_id')) {
            $query->where('bocamina_id', $request->bocamina_id);
        }

        if ($request->filled('tipo_pago')) {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $contratos = $query->get();
        $trabajadores = Trabajador::where('estado', 'activo')->get();
        $bocaminas = Bocamina::all();
        
        $presets = ['metro', 'volqueta', 'tonelada', 'saco', 'monto_fijo'];
        $dbTypes = Contrato::distinct()->pluck('tipo_pago')->toArray();
        $tiposPago = array_values(array_unique(array_merge($presets, $dbTypes)));

        return view('contratos.index', compact('contratos', 'trabajadores', 'bocaminas', 'tiposPago'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'nullable|string|max:255|unique:contratos,codigo',
            'trabajador_id' => 'required|exists:trabajadores,id',
            'bocamina_id' => 'required|exists:bocaminas,id',
            'descripcion' => 'required|string',
            'tipo_pago' => 'required|string|max:50',
            'precio_unitario' => 'nullable|numeric|min:0',
            'avance_estimado_semanal' => 'nullable|numeric|min:0',
            'monto_total' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activo,finalizado,cancelado',
        ]);

        $data = $request->all();
        if (empty($data['codigo'])) {
            $data['codigo'] = 'CON-' . strtoupper(uniqid());
        }

        Contrato::create($data);

        return redirect()->route('contratos.index')->with('success', 'Contrato registrado exitosamente.');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['trabajador', 'bocamina', 'trabajos' => function($q) {
            $q->orderBy('fecha', 'desc');
        }]);
        return view('contratos.show', compact('contrato'));
    }

    public function update(Request $request, Contrato $contrato)
    {
        $request->validate([
            'codigo' => 'required|string|max:255|unique:contratos,codigo,' . $contrato->id,
            'trabajador_id' => 'required|exists:trabajadores,id',
            'bocamina_id' => 'required|exists:bocaminas,id',
            'descripcion' => 'required|string',
            'tipo_pago' => 'required|string|max:50',
            'precio_unitario' => 'nullable|numeric|min:0',
            'avance_estimado_semanal' => 'nullable|numeric|min:0',
            'monto_total' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activo,finalizado,cancelado',
        ]);

        $contrato->update($request->all());

        return redirect()->route('contratos.index')->with('success', 'Contrato actualizado exitosamente.');
    }

    public function destroy(Contrato $contrato)
    {
        // Block delete if they have logged works
        if ($contrato->trabajos()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar el contrato porque ya tiene trabajos registrados asociados.']);
        }

        $contrato->delete();

        return redirect()->route('contratos.index')->with('success', 'Contrato eliminado exitosamente.');
    }
}
