<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\TransaccionMineral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaccionMineralTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaccion_mineral_crud_and_calculations()
    {
        // 1. Arrange: Create user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a Bocamina
        $bocamina = Bocamina::create([
            'nombre' => 'Veta Central',
            'descripcion' => 'Veta de zinc principal'
        ]);

        // 2. Act & Assert: Store a 'compra' (Egreso)
        $compraData = [
            'fecha' => '2026-07-30',
            'tipo' => 'compra',
            'presentacion' => 'saco',
            'cliente_proveedor' => 'Proveedor Juan',
            'peso_bruto' => 10.00,
            'humedad_porcentaje' => 10.00,
            'peso_neto_seco' => 9.00,
            'ley' => '55% Zn',
            'precio_unidad' => 1000.00,
            'monto_total' => 9000.00,
            'bocamina_id' => '', // Test optional/empty bocamina
            'observacion' => 'Compra de prueba'
        ];

        $response = $this->post(route('transacciones-minerales.store'), $compraData);
        $response->assertRedirect(route('transacciones-minerales.index'));
        $this->assertDatabaseHas('transacciones_minerales', [
            'cliente_proveedor' => 'Proveedor Juan',
            'tipo' => 'compra',
            'monto_total' => 9000.00,
            'bocamina_id' => null
        ]);

        // 3. Act & Assert: Store a 'venta' (Ingreso)
        $ventaData = [
            'fecha' => '2026-07-30',
            'tipo' => 'venta',
            'presentacion' => 'concentrado',
            'cliente_proveedor' => 'Comprador Vinto',
            'peso_bruto' => 20.00,
            'humedad_porcentaje' => 5.00,
            'peso_neto_seco' => 19.00,
            'ley' => '60% Zn',
            'precio_unidad' => 1500.00,
            'monto_total' => 28500.00,
            'bocamina_id' => $bocamina->id,
            'observacion' => 'Venta de prueba'
        ];

        $response = $this->post(route('transacciones-minerales.store'), $ventaData);
        $response->assertRedirect(route('transacciones-minerales.index'));
        $this->assertDatabaseHas('transacciones_minerales', [
            'cliente_proveedor' => 'Comprador Vinto',
            'tipo' => 'venta',
            'monto_total' => 28500.00
        ]);

        // 4. Act & Assert: Verify index totals
        $response = $this->get(route('transacciones-minerales.index'));
        $response->assertStatus(200);
        $response->assertViewHas('total_ingresos', 28500.00);
        $response->assertViewHas('total_egresos', 9000.00);
        $response->assertViewHas('balance', 19500.00);

        // 5. Act & Assert: Update a transaction
        $transaccion = TransaccionMineral::first();
        $updateData = array_merge($compraData, [
            'cliente_proveedor' => 'Proveedor Juan Modificado',
            'monto_total' => 9500.00
        ]);

        $response = $this->put(route('transacciones-minerales.update', $transaccion->id), $updateData);
        $response->assertRedirect(route('transacciones-minerales.index'));
        $this->assertDatabaseHas('transacciones_minerales', [
            'id' => $transaccion->id,
            'cliente_proveedor' => 'Proveedor Juan Modificado',
            'monto_total' => 9500.00
        ]);

        // 6. Act & Assert: Delete a transaction
        $response = $this->delete(route('transacciones-minerales.destroy', $transaccion->id));
        $response->assertRedirect(route('transacciones-minerales.index'));
        $this->assertDatabaseMissing('transacciones_minerales', [
            'id' => $transaccion->id
        ]);
    }
}
