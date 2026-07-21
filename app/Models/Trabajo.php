<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    protected $fillable = [
        'trabajador_id',
        'contrato_id',
        'fecha',
        'tipo',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observacion',
        'pagado',
        'pago_id'
    ];

    protected $casts = [
        'pagado' => 'boolean',
        'fecha' => 'date',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
