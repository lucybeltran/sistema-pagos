<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\Anticipo;
use App\Models\Pago;
use App\Models\FondoPago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFundingAndWorkerRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_funding_management_and_ledger_calculations()
    {
        // 1. Arrange: Create user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a Bocamina and Worker
        $bocamina = Bocamina::create(['nombre' => 'Test Mine', 'descripcion' => 'Test']);
        $trabajador = Trabajador::create([
            'ci' => '1234567',
            'nombre' => 'Juan Perez',
            'telefono' => '77777777',
            'bocamina_id' => $bocamina->id,
            'rol' => 'chofer',
            'estado' => 'activo'
        ]);

        // 2. Act & Assert: Create reload fund (Recarga)
        $reloadResponse = $this->post(route('fondos-pagos.store'), [
            'fecha' => '2026-07-30',
            'monto' => 1000.00,
            'observacion' => 'Retiro Inicial'
        ]);
        $reloadResponse->assertRedirect(route('pagos.index'));
        $this->assertDatabaseHas('fondos_pagos', [
            'monto' => 1000.00,
            'observacion' => 'Retiro Inicial'
        ]);

        // 3. Act & Assert: Register an advance (Anticipo)
        // Handing worker an advance of Bs. 200
        $anticipo = Anticipo::create([
            'fecha' => '2026-07-30',
            'trabajador_id' => $trabajador->id,
            'monto' => 200.00,
            'saldo' => 200.00,
            'observacion' => 'Anticipo chofer'
        ]);

        // Register a payout (Pago) where net pay is Bs. 300
        $pago = Pago::create([
            'fecha' => '2026-07-30',
            'trabajador_id' => $trabajador->id,
            'subtotal' => 500.00,
            'bonos' => 0.00,
            'descuentos' => 0.00,
            'anticipos_descontados' => 200.00,
            'neto' => 300.00,
            'monto_pagado' => 300.00,
            'saldo_pendiente' => 0.00,
            'saldo_liquidado' => true
        ]);

        // 4. Act & Assert: Check ledger values on payments index page
        $response = $this->get(route('pagos.index'));
        $response->assertStatus(200);
        
        // total_recargado: 1000
        $response->assertViewHas('total_recargado', 1000.00);
        // total_gastado: 200 (anticipo) + 300 (pago) = 500
        $response->assertViewHas('total_gastado', 500.00);
        // saldo_caja: 1000 - 500 = 500
        $response->assertViewHas('saldo_caja', 500.00);

        // 5. Act & Assert: Delete reload
        $reload = FondoPago::first();
        $deleteResponse = $this->delete(route('fondos-pagos.destroy', $reload->id));
        $deleteResponse->assertRedirect(route('pagos.index'));
        $this->assertDatabaseMissing('fondos_pagos', ['id' => $reload->id]);
    }

    public function test_worker_roles_management()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create(['nombre' => 'Test Mine', 'descripcion' => 'Test']);

        // Create worker with role 'sereno'
        $workerData = [
            'ci' => '9999999',
            'nombre' => 'Pedro Watchman',
            'telefono' => '71111111',
            'bocamina_id' => $bocamina->id,
            'rol' => 'sereno',
            'estado' => 'activo'
        ];

        $response = $this->post(route('trabajadores.store'), $workerData);
        $response->assertRedirect(route('trabajadores.index'));
        
        $this->assertDatabaseHas('trabajadores', [
            'nombre' => 'Pedro Watchman',
            'rol' => 'sereno'
        ]);

        $worker = Trabajador::where('ci', '9999999')->first();

        // Update role to 'contratista'
        $updateData = array_merge($workerData, [
            'nombre' => 'Pedro Contractor',
            'rol' => 'contratista'
        ]);

        $response = $this->put(route('trabajadores.update', $worker->id), $updateData);
        $response->assertRedirect(route('trabajadores.index'));

        $this->assertDatabaseHas('trabajadores', [
            'id' => $worker->id,
            'nombre' => 'Pedro Contractor',
            'rol' => 'contratista'
        ]);
    }
}
