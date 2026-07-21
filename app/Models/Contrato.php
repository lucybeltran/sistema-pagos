<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $fillable = [
        'codigo',
        'trabajador_id',
        'bocamina_id',
        'descripcion',
        'tipo_pago',
        'precio_unitario',
        'avance_estimado_semanal',
        'monto_total',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }

    public function trabajos()
    {
        return $this->hasMany(Trabajo::class);
    }

    public function getAvanceMontoAttribute()
    {
        return $this->trabajos()->sum('subtotal');
    }

    public function getAvancePorcentajeAttribute()
    {
        if ($this->monto_total <= 0) {
            return 0;
        }
        $porcentaje = ($this->avance_monto / $this->monto_total) * 100;
        return min(round($porcentaje, 2), 100);
    }
}
