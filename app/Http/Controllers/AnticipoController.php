<?php

namespace App\Http\Controllers;

use App\Models\Anticipo;
use App\Models\Trabajador;
use App\Models\Bocamina;
use Illuminate\Http\Request;

class AnticipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Anticipo::with('trabajador.bocamina');

        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->filled('bocamina_id')) {
            $query->whereHas('trabajador', function($q) use ($request) {
                $q->where('bocamina_id', $request->bocamina_id);
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'pendiente') {
                $query->where('saldo', '>', 0);
            } elseif ($request->estado === 'pagado') {
                $query->where('saldo', '=', 0);
            }
        }

        $anticipos = $query->orderBy('fecha', 'desc')->get();
        $trabajadores = Trabajador::where('estado', 'activo')->get();
        $bocaminas = Bocamina::orderBy('nombre')->get();

        return view('anticipos.index', compact('anticipos', 'trabajadores', 'bocaminas'));
    }

    public function store(Request $request)
    {
        abort(403, 'No se permite registrar anticipos manualmente.');
    }

    public function update(Request $request, Anticipo $anticipo)
    {
        abort(403, 'No se permite modificar anticipos manualmente.');
    }

    public function destroy(Anticipo $anticipo)
    {
        abort(403, 'No se permite eliminar anticipos manualmente.');
    }

    public function recibo(Anticipo $anticipo)
    {
        $anticipo->load('trabajador.bocamina');
        return view('anticipos.recibo', compact('anticipo'));
    }
}

