<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\TipoContrato;
use App\Models\Anticipo;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_registration_with_direct_contract_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create(['nombre' => 'Test Mine']);
        $tipoContrato = TipoContrato::create(['nombre' => 'Por saco', 'estado' => 'activo']);

        // Register worker with direct contract attributes
        $response = $this->post(route('trabajadores.store'), [
            'nombre' => 'Juan Perez',
            'ci' => '1234567-LP',
            'telefono' => '71234567',
            'rol' => 'contratista',
            'bocamina_id' => $bocamina->id,
            'tipo_contrato_id' => $tipoContrato->id,
            'tarifa_acordada' => 12.50,
            'estado' => 'activo',
            'observaciones' => 'Test worker'
        ]);

        $response->assertRedirect(route('trabajadores.index'));
        $this->assertDatabaseHas('trabajadores', [
            'nombre' => 'Juan Perez',
            'ci' => '1234567-LP',
            'telefono' => '71234567',
            'rol' => 'contratista',
            'bocamina_id' => $bocamina->id,
            'tipo_contrato_id' => $tipoContrato->id,
            'tarifa_acordada' => 12.50,
        ]);
    }

    public function test_worker_registration_with_custom_other_contract_type()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create(['nombre' => 'Test Mine']);

        // Register worker selecting "Otro" contract type, supplying custom name
        $response = $this->post(route('trabajadores.store'), [
            'nombre' => 'Juan Perez',
            'ci' => '1234567-LP',
            'telefono' => '71234567',
            'rol' => 'contratista',
            'bocamina_id' => $bocamina->id,
            'tipo_contrato_id' => 'otro',
            'tipo_contrato_otro' => 'Por volqueta gigante',
            'tarifa_acordada' => 250.00,
            'estado' => 'activo',
            'observaciones' => 'Test custom contract worker'
        ]);

        $response->assertRedirect(route('trabajadores.index'));
        
        // Assert custom TipoContrato was automatically created in catalog
        $this->assertDatabaseHas('tipos_contrato', [
            'nombre' => 'Por volqueta gigante',
            'estado' => 'activo'
        ]);

        $newContractType = TipoContrato::where('nombre', 'Por volqueta gigante')->first();

        // Assert worker was saved with this new contract type id
        $this->assertDatabaseHas('trabajadores', [
            'nombre' => 'Juan Perez',
            'tipo_contrato_id' => $newContractType->id,
            'tarifa_acordada' => 250.00
        ]);
    }

    public function test_payment_processing_with_rates_and_advance_deductions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create(['nombre' => 'Mine A', 'descripcion' => 'Mine A description']);
        $tipoContrato = TipoContrato::create(['nombre' => 'Por saco', 'estado' => 'activo']);
        
        $trabajador = Trabajador::create([
            'nombre' => 'Juan Perez',
            'ci' => '1234567-LP',
            'telefono' => '71234567',
            'rol' => 'contratista',
            'bocamina_id' => $bocamina->id,
            'tipo_contrato_id' => $tipoContrato->id,
            'tarifa_acordada' => 15.00,
            'estado' => 'activo'
        ]);

        // Create an advance of Bs. 100
        $anticipo = Anticipo::create([
            'trabajador_id' => $trabajador->id,
            'fecha' => now()->toDateString(),
            'monto' => 100.00,
            'saldo' => 100.00,
            'pagado' => false
        ]);

        // Process payment: worked 50 sacos at Bs. 15 each -> subtotal = 750
        // Deducts Bs. 100 from advance -> neto = 650
        $response = $this->post(route('pagos.store'), [
            'trabajador_id' => $trabajador->id,
            'fecha' => now()->toDateString(),
            'tarifa_pago' => 15.00,
            'cantidad_trabajada' => 50.00,
            'tipo_contrato_nombre' => 'Por saco',
            'bonos' => 0.00,
            'descuentos' => 0.00,
            'monto_pagado' => 650.00,
            'tipo_cambio' => 6.96,
            'observacion' => 'Weekly payment',
            'deducciones_anticipos' => [
                $anticipo->id => 100.00
            ]
        ]);

        $pago = Pago::first();
        $this->assertNotNull($pago);
        $response->assertRedirect(route('pagos.show', $pago->id));

        $this->assertEquals(750.00, $pago->subtotal);
        $this->assertEquals(100.00, $pago->anticipos_descontados);
        $this->assertEquals(650.00, $pago->neto);
        $this->assertEquals(650.00, $pago->monto_pagado);

        $anticipo->refresh();
        $this->assertTrue($anticipo->pagado);
        $this->assertEquals(0.00, $anticipo->saldo);
    }
}
