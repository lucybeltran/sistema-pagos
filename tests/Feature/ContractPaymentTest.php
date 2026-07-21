<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\Contrato;
use App\Models\Trabajo;
use App\Models\Anticipo;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_progress_and_payment_processing()
    {
        // 1. Arrange: Create user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create Bocamina
        $bocamina = Bocamina::create([
            'nombre' => 'Test Mine',
            'descripcion' => 'A test mine sector'
        ]);

        // Create Trabajador
        $trabajador = Trabajador::create([
            'ci' => '1234567',
            'nombre' => 'Miner John',
            'telefono' => '77777777',
            'bocamina_id' => $bocamina->id,
            'estado' => 'activo'
        ]);

        // Create Contrato (Monto total: Bs 10,000, Unit rate: Bs 100/meter)
        $contrato = Contrato::create([
            'codigo' => 'CON-TEST-01',
            'trabajador_id' => $trabajador->id,
            'bocamina_id' => $bocamina->id,
            'descripcion' => 'Tunnel excavation contract',
            'tipo_pago' => 'metro',
            'precio_unitario' => 100.00,
            'monto_total' => 10000.00,
            'fecha_inicio' => now()->toDateString(),
            'estado' => 'activo'
        ]);

        // Log work under this contract: 25 meters -> Bs. 2,500
        $trabajo = Trabajo::create([
            'trabajador_id' => $trabajador->id,
            'contrato_id' => $contrato->id,
            'fecha' => now()->toDateString(),
            'tipo' => 'metro',
            'cantidad' => 25.00,
            'precio_unitario' => 100.00,
            'subtotal' => 2500.00,
            'observacion' => '25 meters completed',
            'pagado' => false
        ]);

        // 2. Act: Verify Contract progress
        $this->assertEquals(2500.00, $contrato->avance_monto);
        $this->assertEquals(25.00, $contrato->avance_porcentaje); // 2500 / 10000 * 100

        // Create Advance: Bs 500
        $anticipo = Anticipo::create([
            'trabajador_id' => $trabajador->id,
            'fecha' => now()->toDateString(),
            'monto' => 500.00,
            'saldo' => 500.00,
            'pagado' => false
        ]);

        // Post request to store Pago (Bonuses: 100, Discounts: 50, Custom subtotal and Custom advance deduction)
        $response = $this->post(route('pagos.store'), [
            'trabajador_id' => $trabajador->id,
            'fecha' => now()->toDateString(),
            'subtotal' => 2500.00,
            'bonos' => 100.00,
            'descuentos' => 50.00,
            'tipo_cambio' => 6.96,
            'observacion' => 'Payment for test',
            'deducciones_anticipos' => [
                $anticipo->id => 500.00
            ]
        ]);

        // 3. Assert: Verify Payout
        $pago = Pago::first();
        $this->assertNotNull($pago);
        
        // Redirects to receipt view
        $response->assertRedirect(route('pagos.show', $pago->id));

        // Calculations:
        // Subtotal = 2,500
        // Bonuses = 100
        // Discounts = 50
        // Gross pay capacity = 2500 + 100 - 50 = 2550
        // Advance deduction = min(500, 2550) = 500
        // Net Pay = 2550 - 500 = 2050
        $this->assertEquals(2500.00, $pago->subtotal);
        $this->assertEquals(100.00, $pago->bonos);
        $this->assertEquals(50.00, $pago->descuentos);
        $this->assertEquals(500.00, $pago->anticipos_descontados);
        $this->assertEquals(2050.00, $pago->neto);

        // Verify relationships updated
        $trabajo->refresh();
        $this->assertTrue($trabajo->pagado);
        $this->assertEquals($pago->id, $trabajo->pago_id);

        $anticipo->refresh();
        $this->assertTrue($anticipo->pagado);
        $this->assertEquals(0.00, $anticipo->saldo);
        
        // Verify pivot connection
        $this->assertCount(1, $pago->anticipos);
        $this->assertEquals(500.00, $pago->anticipos->first()->pivot->monto_descontado);
    }

    public function test_saco_and_saquito_contract_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create([
            'nombre' => 'Mine Beta',
            'descripcion' => 'Beta sector'
        ]);

        $trabajador = Trabajador::create([
            'ci' => '9876543',
            'nombre' => 'Miner Pete',
            'telefono' => '66666666',
            'bocamina_id' => $bocamina->id,
            'estado' => 'activo'
        ]);

        // Create saco contract
        $responseSaco = $this->post(route('contratos.store'), [
            'codigo' => 'CON-SACO-01',
            'trabajador_id' => $trabajador->id,
            'bocamina_id' => $bocamina->id,
            'descripcion' => 'Saco mining contract',
            'tipo_pago' => 'saco',
            'precio_unitario' => 15.00,
            'avance_estimado_semanal' => 100,
            'monto_total' => 1500.00,
            'fecha_inicio' => now()->toDateString(),
            'estado' => 'activo'
        ]);

        $responseSaco->assertRedirect(route('contratos.index'));
        $this->assertDatabaseHas('contratos', [
            'codigo' => 'CON-SACO-01',
            'tipo_pago' => 'saco',
            'monto_total' => 1500.00
        ]);

        // Create custom contract (caja)
        $responseCaja = $this->post(route('contratos.store'), [
            'codigo' => 'CON-CAJA-01',
            'trabajador_id' => $trabajador->id,
            'bocamina_id' => $bocamina->id,
            'descripcion' => 'Caja mining contract',
            'tipo_pago' => 'caja',
            'precio_unitario' => 5.00,
            'avance_estimado_semanal' => 300,
            'monto_total' => 1500.00,
            'fecha_inicio' => now()->toDateString(),
            'estado' => 'activo'
        ]);

        $responseCaja->assertRedirect(route('contratos.index'));
        $this->assertDatabaseHas('contratos', [
            'codigo' => 'CON-CAJA-01',
            'tipo_pago' => 'caja',
            'monto_total' => 1500.00
        ]);
    }

    public function test_partial_payment_and_overpayment_logic()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create([
            'nombre' => 'Test Mine 2',
            'descripcion' => 'A test mine sector 2'
        ]);

        $trabajador = Trabajador::create([
            'ci' => '1234568',
            'nombre' => 'Miner Pete 2',
            'telefono' => '77777778',
            'bocamina_id' => $bocamina->id,
            'estado' => 'activo'
        ]);

        $contrato = Contrato::create([
            'codigo' => 'CON-TEST-02',
            'trabajador_id' => $trabajador->id,
            'bocamina_id' => $bocamina->id,
            'descripcion' => 'Tunnel excavation contract 2',
            'tipo_pago' => 'metro',
            'precio_unitario' => 100.00,
            'monto_total' => 10000.00,
            'fecha_inicio' => now()->toDateString(),
            'estado' => 'activo'
        ]);

        // 1. Log work: Bs. 1000 (10 meters at 100/m)
        $trabajo1 = Trabajo::create([
            'trabajador_id' => $trabajador->id,
            'contrato_id' => $contrato->id,
            'fecha' => now()->toDateString(),
            'tipo' => 'metro',
            'cantidad' => 10.00,
            'precio_unitario' => 100.00,
            'subtotal' => 1000.00,
            'observacion' => '10 meters',
            'pagado' => false
        ]);

        // Owner pays only Bs. 400.
        // Net due is subtotal (1000) + bonos (0) - desc (0) = 1000.
        // Actual paid is 400.
        // Remaining unpaid (saldo_pendiente) should be 600, saldo_liquidado = false.
        $response1 = $this->post(route('pagos.store'), [
            'trabajador_id' => $trabajador->id,
            'fecha' => now()->toDateString(),
            'subtotal' => 1000.00,
            'bonos' => 0.00,
            'descuentos' => 0.00,
            'monto_pagado' => 400.00,
            'tipo_cambio' => 6.96,
            'observacion' => 'Partial payment',
        ]);

        $pago1 = Pago::where('trabajador_id', $trabajador->id)->first();
        $this->assertNotNull($pago1);
        $this->assertEquals(1000.00, $pago1->subtotal);
        $this->assertEquals(400.00, $pago1->monto_pagado);
        $this->assertEquals(600.00, $pago1->saldo_pendiente);
        $this->assertFalse($pago1->saldo_liquidado);

        $trabajo1->refresh();
        $this->assertTrue($trabajo1->pagado);
        $this->assertEquals($pago1->id, $trabajo1->pago_id);

        // 2. Next week: AJAX endpoint to get worker data should fetch the Bs. 600 pending balance.
        $ajaxResponse = $this->get(route('pagos.trabajador-data', $trabajador->id));
        $ajaxResponse->assertStatus(200);
        $data = $ajaxResponse->json();
        
        $this->assertEquals(600.00, $data['total_saldos_pendientes']);
        $this->assertCount(1, $data['saldos_pendientes']);
        $this->assertEquals($pago1->id, $data['saldos_pendientes'][0]['id']);

        // Log new work: Bs. 1200 (12 meters at 100/m)
        $trabajo2 = Trabajo::create([
            'trabajador_id' => $trabajador->id,
            'contrato_id' => $contrato->id,
            'fecha' => now()->addDays(7)->toDateString(),
            'tipo' => 'metro',
            'cantidad' => 12.00,
            'precio_unitario' => 100.00,
            'subtotal' => 1200.00,
            'observacion' => '12 meters',
            'pagado' => false
        ]);

        // Owner processes a payment of Bs. 2000.
        // Net due is subtotal (1200) + previous balance (600) = 1800.
        // Owner pays 2000 (which is an overpayment of 200).
        // This should auto-create an Anticipo of 200.
        // The previous balance should be marked as liquidated (saldo_liquidado = true).
        $response2 = $this->post(route('pagos.store'), [
            'trabajador_id' => $trabajador->id,
            'fecha' => now()->addDays(7)->toDateString(),
            'subtotal' => 1200.00,
            'bonos' => 0.00,
            'descuentos' => 0.00,
            'monto_pagado' => 2000.00,
            'tipo_cambio' => 6.96,
            'observacion' => 'Overpayment',
        ]);

        $pago2 = Pago::where('trabajador_id', $trabajador->id)->where('id', '!=', $pago1->id)->first();
        $this->assertNotNull($pago2);
        
        // Net due: 1200 (subtotal) + 600 (prev) = 1800
        $this->assertEquals(1200.00, $pago2->subtotal);
        $this->assertEquals(1800.00, $pago2->neto);
        $this->assertEquals(2000.00, $pago2->monto_pagado);
        $this->assertEquals(0.00, $pago2->saldo_pendiente);
        $this->assertTrue($pago2->saldo_liquidado);

        // Verify previous payment balance is now liquidated
        $pago1->refresh();
        $this->assertTrue($pago1->saldo_liquidado);

        // Verify new advance is created automatically for the difference (2000 - 1800 = 200)
        $this->assertDatabaseHas('anticipos', [
            'trabajador_id' => $trabajador->id,
            'monto' => 200.00,
            'saldo' => 200.00,
            'pagado' => false
        ]);
    }
}
