<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bocamina extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }
}
