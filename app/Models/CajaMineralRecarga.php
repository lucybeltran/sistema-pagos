<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaMineralRecarga extends Model
{
    use HasFactory;

    protected $table = 'caja_minerales_recargas';

    protected $fillable = [
        'fecha',
        'monto',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];
}
