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

        // 2. Act & Assert: Store a 'compra' (Egreso) representing a Lote
        $compraData = [
            'fecha' => '2026-07-30',
            'tipo' => 'compra',
            'presentacion' => 'Sacos',
            'cliente_proveedor' => 'Proveedor Juan',
            'cantidad' => 100.00,
            'peso_bruto' => 5000.00,
            'humedad_porcentaje' => 0.00,
            'peso_neto_seco' => 5000.00, // 5000 Kg
            'precio_unidad' => 2.00,
            'monto_total' => 10000.00,
            'bocamina_id' => $bocamina->id,
            'observacion' => 'Compra de lote de prueba',
            'analisis' => [
                ['mineral' => 'Zinc', 'ley' => 48.50],
                ['mineral' => 'Plata', 'ley' => 1.80],
            ]
        ];

        $response = $this->post(route('transacciones-minerales.store'), $compraData);
        $response->assertRedirect(route('transacciones-minerales.index'));
        
        $this->assertDatabaseHas('transacciones_minerales', [
            'cliente_proveedor' => 'Proveedor Juan',
            'tipo' => 'compra',
            'cantidad' => 100.00,
            'peso_neto_seco' => 5000.00,
            'cantidad_disponible' => 100.00, // Stock initialized
            'peso_disponible' => 5000.00,    // Stock initialized
            'monto_total' => 10000.00,
            'bocamina_id' => $bocamina->id
        ]);

        $this->assertDatabaseHas('lote_analisis', [
            'mineral' => 'Zinc',
            'ley' => 48.50
        ]);

        $lote = TransaccionMineral::where('tipo', 'compra')->first();

        // 3. Act & Assert: Store a 'venta' (Ingreso) checking stock decrement
        $ventaData = [
            'fecha' => '2026-07-31',
            'tipo' => 'venta',
            'lote_id' => $lote->id,
            'cliente_proveedor' => 'Comprador Vinto',
            'destino' => 'Fundición Vinto',
            'cantidad' => 40.00,
            'peso_neto_seco' => 2000.00, // Selling 2000 Kg
            'precio_unidad' => 2.50,
            'monto_total' => 5000.00,
            'observacion' => 'Venta despachada'
        ];

        $response = $this->post(route('transacciones-minerales.store'), $ventaData);
        $response->assertRedirect(route('transacciones-minerales.index'));

        $this->assertDatabaseHas('transacciones_minerales', [
            'cliente_proveedor' => 'Comprador Vinto',
            'tipo' => 'venta',
            'lote_id' => $lote->id,
            'cantidad' => 40.00,
            'peso_neto_seco' => 2000.00,
            'monto_total' => 5000.00
        ]);

        // Verify stock has been decremented on lote
        $lote->refresh();
        $this->assertEquals(60.00, $lote->cantidad_disponible);
        $this->assertEquals(3000.00, $lote->peso_disponible);

        // 4. Act & Assert: Verify index totals
        $response = $this->get(route('transacciones-minerales.index'));
        $response->assertStatus(200);
        $response->assertViewHas('total_ingresos', 5000.00);
        $response->assertViewHas('total_egresos', 10000.00);
        $response->assertViewHas('balance', -5000.00);

        // 5. Act & Assert: Update a sale transaction
        $venta = TransaccionMineral::where('tipo', 'venta')->first();
        $updateVentaData = array_merge($ventaData, [
            'cliente_proveedor' => 'Comprador Vinto Modificado',
            'cantidad' => 50.00,
            'peso_neto_seco' => 2500.00,
            'monto_total' => 62500.00, // 2500 * 2.50 = 6250.00 actually, but using payload
        ]);

        $response = $this->put(route('transacciones-minerales.update', $venta->id), $updateVentaData);
        $response->assertRedirect(route('transacciones-minerales.index'));

        // Verify new stock values on Lote
        $lote->refresh();
        $this->assertEquals(50.00, $lote->cantidad_disponible);
        $this->assertEquals(2500.00, $lote->peso_disponible);

        // 6. Act & Assert: Delete a sale transaction (restores stock)
        $response = $this->delete(route('transacciones-minerales.destroy', $venta->id));
        $response->assertRedirect(route('transacciones-minerales.index'));

        $lote->refresh();
        $this->assertEquals(100.00, $lote->cantidad_disponible);
        $this->assertEquals(5000.00, $lote->peso_disponible);
    }
}
