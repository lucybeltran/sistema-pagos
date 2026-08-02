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
        'presentacion_otro',
        'cliente_proveedor',
        'peso_bruto',
        'humedad_porcentaje',
        'peso_neto_seco',
        'ley',
        'precio_unidad',
        'monto_total',
        'bocamina_id',
        'observacion',
        'lote_id',
        'cantidad',
        'cantidad_disponible',
        'peso_disponible',
        'destino',
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso_bruto' => 'decimal:2',
        'humedad_porcentaje' => 'decimal:2',
        'peso_neto_seco' => 'decimal:2',
        'precio_unidad' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'cantidad' => 'decimal:2',
        'cantidad_disponible' => 'decimal:2',
        'peso_disponible' => 'decimal:2',
    ];

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }

    public function lote()
    {
        return $this->belongsTo(TransaccionMineral::class, 'lote_id');
    }

    public function ventas()
    {
        return $this->hasMany(TransaccionMineral::class, 'lote_id');
    }

    public function analisis()
    {
        return $this->hasMany(LoteAnalisis::class, 'transaccion_mineral_id');
    }
}
