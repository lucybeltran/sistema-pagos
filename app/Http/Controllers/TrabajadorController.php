<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Bocamina;
use App\Models\TipoContrato;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Trabajador::with(['bocamina', 'tipoContrato']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('ci', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('bocamina_id')) {
            $query->where('bocamina_id', $request->bocamina_id);
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $trabajadores = $query->get();
        $bocaminas = Bocamina::all();
        $contratos = TipoContrato::where('estado', 'activo')->orderBy('nombre')->get(); // Contract types catalog

        return view('trabajadores.index', compact('trabajadores', 'bocaminas', 'contratos'));
    }

    public function store(Request $request)
    {
        if ($request->filled('nombre')) {
            $request->merge([
                'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, "UTF-8")
            ]);
        }

        // Process "otro" role
        if ($request->rol === 'otro' && $request->filled('rol_otro')) {
            $request->merge([
                'rol' => trim($request->rol_otro)
            ]);
        }

        // Auto-generate code if empty
        if (!$request->filled('codigo')) {
            $prefix = ($request->rol === 'contratista') ? 'CON' : 'PER';
            do {
                $randomDigits = mt_rand(1000, 9999);
                $generatedCode = $prefix . '-' . $randomDigits;
            } while (Trabajador::where('codigo', $generatedCode)->exists());
            $request->merge(['codigo' => $generatedCode]);
        }

        $rules = [
            'codigo' => 'nullable|string|max:255|unique:trabajadores,codigo',
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-ZÁÉÍÓÚÑ][a-zzáéíóúñA-ZÁÉÍÓÚÑ\']*(?:\s+[A-ZÁÉÍÓÚÑ][a-zzáéíóúñA-ZÁÉÍÓÚÑ\']*)*$/u'
            ],
            'ci' => 'nullable|string|max:255|unique:trabajadores,ci',
            'telefono' => 'nullable|numeric|digits:8',
            'bocamina_id' => 'required|exists:bocaminas,id',
            'rol' => 'required|string|max:255',
            'rol_otro' => 'required_if:rol,otro|nullable|string|max:255',
            'tipo_contrato_id' => 'required', // can be numeric ID or "otro"
            'tipo_contrato_otro' => 'required_if:tipo_contrato_id,otro|nullable|string|max:255',
            'fecha_contrato' => 'nullable|date',
            'tarifa_acordada' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'observaciones' => 'nullable|string|max:1000',
        ];

        $data = $request->validate($rules, [
            'nombre.regex' => 'Cada nombre y apellido debe comenzar con mayúscula (Ej. Juan Carlos Pérez).',
            'telefono.digits' => 'El teléfono debe tener exactamente 8 números.',
            'telefono.numeric' => 'El teléfono solo debe contener números.',
            'tipo_contrato_otro.required_if' => 'Debe escribir el nombre del tipo de contrato personalizado.',
            'rol_otro.required_if' => 'Debe escribir el nombre del cargo o función personalizado.',
        ]);

        // Process "otro" contract type
        if ($data['tipo_contrato_id'] === 'otro') {
            $nombreContrato = trim($data['tipo_contrato_otro']);
            $tipoContrato = TipoContrato::firstOrCreate(
                ['nombre' => $nombreContrato],
                ['estado' => 'activo']
            );
            $data['tipo_contrato_id'] = $tipoContrato->id;
        }

        Trabajador::create($data);

        return redirect()->route('trabajadores.index')->with('success', 'Personal registrado exitosamente.');
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        if ($request->filled('nombre')) {
            $request->merge([
                'nombre' => mb_convert_case(trim($request->nombre), MB_CASE_TITLE, "UTF-8")
            ]);
        }

        // Process "otro" role
        if ($request->rol === 'otro' && $request->filled('rol_otro')) {
            $request->merge([
                'rol' => trim($request->rol_otro)
            ]);
        }

        // Auto-generate code if empty (if they cleared it or it wasn't set)
        if (!$request->filled('codigo')) {
            $prefix = ($request->rol === 'contratista') ? 'CON' : 'PER';
            do {
                $randomDigits = mt_rand(1000, 9999);
                $generatedCode = $prefix . '-' . $randomDigits;
            } while (Trabajador::where('codigo', $generatedCode)->exists());
            $request->merge(['codigo' => $generatedCode]);
        }

        $rules = [
            'codigo' => 'nullable|string|max:255|unique:trabajadores,codigo,' . $trabajador->id,
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-ZÁÉÍÓÚÑ][a-zzáéíóúñA-ZÁÉÍÓÚÑ\']*(?:\s+[A-ZÁÉÍÓÚÑ][a-zzáéíóúñA-ZÁÉÍÓÚÑ\']*)*$/u'
            ],
            'ci' => 'nullable|string|max:255|unique:trabajadores,ci,' . $trabajador->id,
            'telefono' => 'nullable|numeric|digits:8',
            'bocamina_id' => 'required|exists:bocaminas,id',
            'rol' => 'required|string|max:255',
            'rol_otro' => 'required_if:rol,otro|nullable|string|max:255',
            'tipo_contrato_id' => 'required', // can be ID or "otro"
            'tipo_contrato_otro' => 'required_if:tipo_contrato_id,otro|nullable|string|max:255',
            'fecha_contrato' => 'nullable|date',
            'tarifa_acordada' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'observaciones' => 'nullable|string|max:1000',
        ];

        $data = $request->validate($rules, [
            'nombre.regex' => 'Cada nombre y apellido debe comenzar con mayúscula (Ej. Juan Carlos Pérez).',
            'telefono.digits' => 'El teléfono debe tener exactamente 8 números.',
            'telefono.numeric' => 'El teléfono solo debe contener números.',
            'tipo_contrato_otro.required_if' => 'Debe escribir el nombre del tipo de contrato personalizado.',
            'rol_otro.required_if' => 'Debe escribir el nombre del cargo o función personalizado.',
        ]);

        // Process "otro" contract type
        if ($data['tipo_contrato_id'] === 'otro') {
            $nombreContrato = trim($data['tipo_contrato_otro']);
            $tipoContrato = TipoContrato::firstOrCreate(
                ['nombre' => $nombreContrato],
                ['estado' => 'activo']
            );
            $data['tipo_contrato_id'] = $tipoContrato->id;
        }

        $trabajador->update($data);

        return redirect()->route('trabajadores.index')->with('success', 'Personal actualizado exitosamente.');
    }

    public function destroy(Trabajador $trabajador)
    {
        // Block delete if they have payments or advances linked
        if ($trabajador->anticipos()->exists() || $trabajador->pagos()->exists()) {
            return back()->withErrors(['error' => 'No se puede eliminar el trabajador porque tiene anticipos o pagos asociados. Puede desactivarlo en su lugar.']);
        }

        $trabajador->delete();

        return redirect()->route('trabajadores.index')->with('success', 'Personal eliminado exitosamente.');
    }
}
