<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\Contrato;
use App\Models\Trabajo;
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
        $admin = User::create([
            'name' => 'Administrador Minero',
            'email' => 'admin@mina.com',
            'password' => Hash::make('admin123'),
        ]);

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

        // 3. Trabajadores
        $juan = Trabajador::create([
            'ci' => '5938201-LP',
            'nombre' => 'Juan Pérez Mamani',
            'telefono' => '71234567',
            'bocamina_id' => $sanjose->id,
            'estado' => 'activo',
        ]);

        $pedro = Trabajador::create([
            'ci' => '4829301-OR',
            'nombre' => 'Pedro Quispe Mamani',
            'telefono' => '72198765',
            'bocamina_id' => $rosario->id,
            'estado' => 'activo',
        ]);

        $luis = Trabajador::create([
            'ci' => '6910293-PT',
            'nombre' => 'Luis Alberto Flores',
            'telefono' => '73204918',
            'bocamina_id' => $sanjose->id,
            'estado' => 'activo',
        ]);

        $mario = Trabajador::create([
            'ci' => '3928103-LP',
            'nombre' => 'Mario Choque Condori',
            'telefono' => '70129384',
            'bocamina_id' => $santamaria->id,
            'estado' => 'inactivo',
        ]);

        // 4. Contratos
        $contratoJuan = Contrato::create([
            'codigo' => 'CON-SJOSE-01',
            'trabajador_id' => $juan->id,
            'bocamina_id' => $sanjose->id,
            'descripcion' => 'Avance de 50 metros en veta de plata',
            'tipo_pago' => 'metro',
            'precio_unitario' => 500.00,
            'monto_total' => 25000.00,
            'fecha_inicio' => Carbon::today()->subDays(30)->toDateString(),
            'estado' => 'activo',
        ]);

        $contratoPedro = Contrato::create([
            'codigo' => 'CON-ROS-02',
            'trabajador_id' => $pedro->id,
            'bocamina_id' => $rosario->id,
            'descripcion' => 'Carga de 100 volquetas de mineral bruto',
            'tipo_pago' => 'volqueta',
            'precio_unitario' => 150.00,
            'monto_total' => 15000.00,
            'fecha_inicio' => Carbon::today()->subDays(20)->toDateString(),
            'estado' => 'activo',
        ]);

        // 5. Anticipos
        // Juan has an unpaid advance
        $anticipoJuan = Anticipo::create([
            'trabajador_id' => $juan->id,
            'fecha' => Carbon::today()->subDays(10)->toDateString(),
            'monto' => 1000.00,
            'saldo' => 1000.00,
            'pagado' => false,
        ]);

        // Pedro had an advance that was fully paid
        $anticipoPedro = Anticipo::create([
            'trabajador_id' => $pedro->id,
            'fecha' => Carbon::today()->subDays(15)->toDateString(),
            'monto' => 500.00,
            'saldo' => 0.00,
            'pagado' => true,
        ]);

        // 6. Trabajos (Paid and Unpaid)
        // Juan's unpaid job (Ready to pay in wizard)
        Trabajo::create([
            'trabajador_id' => $juan->id,
            'contrato_id' => $contratoJuan->id,
            'fecha' => Carbon::today()->subDays(2)->toDateString(),
            'tipo' => 'metro',
            'cantidad' => 10.00,
            'precio_unitario' => 500.00,
            'subtotal' => 5000.00,
            'observacion' => 'Avance de la galería principal',
            'pagado' => false,
        ]);

        // Juan's other unpaid job (No contract, custom type)
        Trabajo::create([
            'trabajador_id' => $juan->id,
            'contrato_id' => null,
            'fecha' => Carbon::today()->subDays(1)->toDateString(),
            'tipo' => 'tonelada',
            'cantidad' => 5.00,
            'precio_unitario' => 200.00,
            'subtotal' => 1000.00,
            'observacion' => 'Extracción extra de desmonte',
            'pagado' => false,
        ]);

        // Pedro's unpaid job (Ready to pay)
        Trabajo::create([
            'trabajador_id' => $pedro->id,
            'contrato_id' => $contratoPedro->id,
            'fecha' => Carbon::today()->subDays(3)->toDateString(),
            'tipo' => 'volqueta',
            'cantidad' => 15.00,
            'precio_unitario' => 150.00,
            'subtotal' => 2250.00,
            'observacion' => 'Cargado de volquetas sección A',
            'pagado' => false,
        ]);

        // Pedro's paid job
        $trabajoPedroPagado = Trabajo::create([
            'trabajador_id' => $pedro->id,
            'contrato_id' => $contratoPedro->id,
            'fecha' => Carbon::today()->subDays(12)->toDateString(),
            'tipo' => 'volqueta',
            'cantidad' => 20.00,
            'precio_unitario' => 150.00,
            'subtotal' => 3000.00,
            'observacion' => 'Cargado de volquetas sección B',
            'pagado' => true,
        ]);

        // 7. Pagos (Historical Pago for Pedro)
        $pagoPedro = Pago::create([
            'trabajador_id' => $pedro->id,
            'fecha' => Carbon::today()->subDays(10)->toDateString(),
            'subtotal' => 3000.00,
            'bonos' => 200.00,
            'descuentos' => 50.00,
            'anticipos_descontados' => 500.00,
            'neto' => 2650.00,
            'observacion' => 'Liquidación quincena anterior',
        ]);

        // Link Pedro's paid job to his payment
        $trabajoPedroPagado->pago_id = $pagoPedro->id;
        $trabajoPedroPagado->save();

        // Connect the advance deduction to the payment
        $pagoPedro->anticipos()->attach($anticipoPedro->id, [
            'monto_descontado' => 500.00,
            'created_at' => Carbon::today()->subDays(10),
            'updated_at' => Carbon::today()->subDays(10),
        ]);
    }
}
