<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $fillable = ['ci', 'nombre', 'telefono', 'bocamina_id', 'estado'];

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }

    public function trabajos()
    {
        return $this->hasMany(Trabajo::class);
    }

    public function anticipos()
    {
        return $this->hasMany(Anticipo::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
