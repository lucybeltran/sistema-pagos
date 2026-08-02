<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Trabajador;
use App\Models\Bocamina;
use App\Models\TipoContrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrato::with(['trabajador', 'bocamina', 'tipoContrato']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('trabajador', function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('bocamina_id')) {
            $query->where('bocamina_id', $request->bocamina_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $contratos = $query->get();
        $trabajadores = Trabajador::where('estado', 'activo')->orderBy('nombre')->get();
        $bocaminas = Bocamina::all();
        $tiposContrato = TipoContrato::where('estado', 'activo')->orderBy('nombre')->get();

        return view('contratos.index', compact('contratos', 'trabajadores', 'bocaminas', 'tiposContrato'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'bocamina_id' => 'required|exists:bocaminas,id',
            'tipo_contrato_id' => 'required|exists:tipos_contrato,id',
            'tarifa_acordada' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function() use ($data) {
            // If the new assignment is active, deactivate any previous active contract for the same worker
            if ($data['estado'] === 'activo') {
                Contrato::where('trabajador_id', $data['trabajador_id'])
                        ->where('estado', 'activo')
                        ->update(['estado' => 'inactivo']);
            }
            Contrato::create($data);
        });

        return redirect()->route('contratos.index')->with('success', 'Contrato laboral asignado exitosamente.');
    }

    public function update(Request $request, Contrato $contrato)
    {
        $data = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'bocamina_id' => 'required|exists:bocaminas,id',
            'tipo_contrato_id' => 'required|exists:tipos_contrato,id',
            'tarifa_acordada' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function() use ($contrato, $data) {
            // If updating to active, deactivate all other active contracts for this worker
            if ($data['estado'] === 'activo') {
                Contrato::where('trabajador_id', $data['trabajador_id'])
                        ->where('id', '!=', $contrato->id)
                        ->where('estado', 'activo')
                        ->update(['estado' => 'inactivo']);
            }
            $contrato->update($data);
        });

        return redirect()->route('contratos.index')->with('success', 'Contrato laboral actualizado exitosamente.');
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index')->with('success', 'Contrato laboral eliminado exitosamente.');
    }
}
