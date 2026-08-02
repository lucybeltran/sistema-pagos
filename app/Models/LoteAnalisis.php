<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteAnalisis extends Model
{
    protected $table = 'lote_analisis';

    protected $fillable = [
        'transaccion_mineral_id',
        'mineral',
        'ley',
    ];

    protected $casts = [
        'ley' => 'decimal:2',
    ];

    public function transaccion()
    {
        return $this->belongsTo(TransaccionMineral::class, 'transaccion_mineral_id');
    }
}
