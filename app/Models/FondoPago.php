<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FondoPago extends Model
{
    protected $table = 'fondos_pagos';

    protected $fillable = ['fecha', 'monto', 'observacion'];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];
}
