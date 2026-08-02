<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $fillable = [
        'trabajador_id',
        'bocamina_id',
        'tipo_contrato_id',
        'tarifa_acordada',
        'estado',
        'observaciones'
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }

    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }
}
