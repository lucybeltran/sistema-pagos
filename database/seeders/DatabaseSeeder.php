<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\TipoContrato;
use App\Models\Anticipo;
use App\Models\Pago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@mina.com'],
            [
                'name' => 'Administrador Minero',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Bocaminas
        $sanjose = Bocamina::create([
            'nombre' => 'Bocamina San José',
            'descripcion' => 'Sector norte, veta rica de plata y plomo.',
        ]);

        $rosario = Bocamina::create([
            'nombre' => 'Bocamina Rosario',
            'descripcion' => 'Galería central profunda, extracción de zinc.',
        ]);

        $santamaria = Bocamina::create([
            'nombre' => 'Bocamina Santa María',
            'descripcion' => 'Frente de exploración en la sección sur.',
        ]);

        // 3. Tipos de Contrato (Catalogue)
        $cSaco   = TipoContrato::create(['nombre' => 'Por saco', 'estado' => 'activo']);
        $cVolq   = TipoContrato::create(['nombre' => 'Por volqueta', 'estado' => 'activo']);
        $cMetro  = TipoContrato::create(['nombre' => 'Por metro avanzado', 'estado' => 'activo']);
        $cViaje  = TipoContrato::create(['nombre' => 'Por viaje', 'estado' => 'activo']);
        $cMensual = TipoContrato::create(['nombre' => 'Mensual', 'estado' => 'activo']);
        $cDiario  = TipoContrato::create(['nombre' => 'Diario', 'estado' => 'activo']);
        $cOtro    = TipoContrato::create(['nombre' => 'Otro', 'estado' => 'activo']);

        // 4. Trabajadores (Personal directly with labor data)
        $juan = Trabajador::create([
            'ci' => '5938201-LP',
            'nombre' => 'Juan Pérez Mamani',
            'telefono' => '71234567',
            'rol' => 'contratista',
            'bocamina_id' => $sanjose->id,
            'tipo_contrato_id' => $cMetro->id,
            'tarifa_acordada' => 500.00,
            'estado' => 'activo',
            'observaciones' => 'Personal antiguo'
        ]);

        $pedro = Trabajador::create([
            'ci' => '4829301-OR',
            'nombre' => 'Pedro Quispe Mamani',
            'telefono' => '72198765',
            'rol' => 'chofer',
            'bocamina_id' => $rosario->id,
            'tipo_contrato_id' => $cVolq->id,
            'tarifa_acordada' => 150.00,
            'estado' => 'activo',
            'observaciones' => 'Chofer titular'
        ]);

        $luis = Trabajador::create([
            'ci' => '6910293-PT',
            'nombre' => 'Luis Alberto Flores',
            'telefono' => '73204918',
            'rol' => 'ayudante',
            'bocamina_id' => $sanjose->id,
            'tipo_contrato_id' => $cSaco->id,
            'tarifa_acordada' => 12.50,
            'estado' => 'activo',
            'observaciones' => 'Ayudante de ensacado'
        ]);

        $mario = Trabajador::create([
            'ci' => '3928103-LP',
            'nombre' => 'Mario Choque Condori',
            'telefono' => '70129384',
            'rol' => 'sereno',
            'bocamina_id' => $santamaria->id,
            'tipo_contrato_id' => $cMensual->id,
            'tarifa_acordada' => 3500.00,
            'estado' => 'inactivo',
            'observaciones' => 'Ex-sereno'
        ]);

        // 5. Anticipos
        $anticipoJuan = Anticipo::create([
            'trabajador_id' => $juan->id,
            'fecha' => Carbon::today()->subDays(10)->toDateString(),
            'monto' => 1000.00,
            'saldo' => 1000.00,
            'pagado' => false,
        ]);

        $anticipoPedro = Anticipo::create([
            'trabajador_id' => $pedro->id,
            'fecha' => Carbon::today()->subDays(15)->toDateString(),
            'monto' => 500.00,
            'saldo' => 0.00,
            'pagado' => true,
        ]);

        // 6. Pagos
        $pagoPedro = Pago::create([
            'trabajador_id' => $pedro->id,
            'fecha' => Carbon::today()->subDays(10)->toDateString(),
            'tarifa_pago' => 150.00,
            'cantidad_trabajada' => 20.00,
            'tipo_contrato_nombre' => 'Por volqueta',
            'subtotal' => 3000.00,
            'bonos' => 200.00,
            'descuentos' => 50.00,
            'anticipos_descontados' => 500.00,
            'neto' => 2650.00,
            'monto_pagado' => 2650.00,
            'saldo_pendiente' => 0.00,
            'saldo_liquidado' => true,
            'tipo_cambio' => 6.96,
            'observacion' => 'Liquidación quincena anterior',
            'metodo_pago' => 'efectivo',
        ]);

        // Link advance deduction to the payment
        $pagoPedro->anticipos()->attach($anticipoPedro->id, [
            'monto_descontado' => 500.00,
            'created_at' => Carbon::today()->subDays(10),
            'updated_at' => Carbon::today()->subDays(10),
        ]);
    }
}
