<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaccionMineral extends Model
{
    protected $table = 'transacciones_minerales';

    protected $fillable = [
        'fecha',
        'tipo',
        'presentacion',
        'cliente_proveedor',
        'peso_bruto',
        'humedad_porcentaje',
        'peso_neto_seco',
        'ley',
        'precio_unidad',
        'monto_total',
        'bocamina_id',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso_bruto' => 'decimal:2',
        'humedad_porcentaje' => 'decimal:2',
        'peso_neto_seco' => 'decimal:2',
        'precio_unidad' => 'decimal:2',
        'monto_total' => 'decimal:2',
    ];

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }
}
