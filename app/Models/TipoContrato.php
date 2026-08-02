<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    protected $table = 'tipos_contrato';

    protected $fillable = [
        'nombre',
        'estado'
    ];

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'tipo_contrato_id');
    }
}
