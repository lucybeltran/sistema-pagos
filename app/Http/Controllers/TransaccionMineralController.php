<?php

namespace App\Http\Controllers;

use App\Models\TransaccionMineral;
use App\Models\Bocamina;
use Illuminate\Http\Request;

class TransaccionMineralController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaccionMineral::with('bocamina');

        // Apply filters
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('presentacion')) {
            $query->where('presentacion', $request->presentacion);
        }

        if ($request->filled('bocamina_id')) {
            $query->where('bocamina_id', $request->bocamina_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $transacciones = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();

        // Calculate totals dynamically based on filters
        $total_ingresos = $transacciones->where('tipo', 'venta')->sum('monto_total');
        $total_egresos = $transacciones->where('tipo', 'compra')->sum('monto_total');
        $balance = $total_ingresos - $total_egresos;

        $bocaminas = Bocamina::orderBy('nombre')->get();

        return view('transacciones.index', compact(
            'transacciones',
            'total_ingresos',
            'total_egresos',
            'balance',
            'bocaminas'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:compra,venta',
            'presentacion' => 'required|string',
            'cliente_proveedor' => 'required|string|max:255',
            'peso_bruto' => 'nullable|numeric|min:0',
            'humedad_porcentaje' => 'nullable|numeric|min:0|max:100',
            'peso_neto_seco' => 'nullable|numeric|min:0',
            'ley' => 'nullable|string|max:100',
            'precio_unidad' => 'nullable|numeric|min:0',
            'monto_total' => 'required|numeric|min:0',
            'bocamina_id' => 'nullable|exists:bocaminas,id',
            'observacion' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['bocamina_id'])) {
            $data['bocamina_id'] = null;
        }

        TransaccionMineral::create($data);

        return redirect()->route('transacciones-minerales.index')->with('success', 'Transacción registrada con éxito.');
    }

    public function update(Request $request, TransaccionMineral $transacciones_minerale)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:compra,venta',
            'presentacion' => 'required|string',
            'cliente_proveedor' => 'required|string|max:255',
            'peso_bruto' => 'nullable|numeric|min:0',
            'humedad_porcentaje' => 'nullable|numeric|min:0|max:100',
            'peso_neto_seco' => 'nullable|numeric|min:0',
            'ley' => 'nullable|string|max:100',
            'precio_unidad' => 'nullable|numeric|min:0',
            'monto_total' => 'required|numeric|min:0',
            'bocamina_id' => 'nullable|exists:bocaminas,id',
            'observacion' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['bocamina_id'])) {
            $data['bocamina_id'] = null;
        }

        $transacciones_minerale->update($data);

        return redirect()->route('transacciones-minerales.index')->with('success', 'Transacción actualizada con éxito.');
    }

    public function destroy(TransaccionMineral $transacciones_minerale)
    {
        $transacciones_minerale->delete();

        return redirect()->route('transacciones-minerales.index')->with('success', 'Transacción eliminada con éxito.');
    }
}
