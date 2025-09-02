<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketPrice;
use App\Models\Product;
use App\Models\Person;
use Carbon\Carbon;

class MarketPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos productos para crear precios
        $products = Product::active()->take(10)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No hay productos activos para crear precios de mercado.');
            return;
        }

        // Obtener una persona para asignar como actualizador, o crear una si no existe
        $person = Person::first();
        
        if (!$person) {
            $person = Person::create([
                'first_name' => 'Admin',
                'last_name' => 'Sistema',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'identification_type' => 'V',
                'identification_number' => '12345678',
                'state_id' => 1,
                'municipality_id' => 1,
                'parish_id' => 1,
                'phone' => '04121234567',
                'address' => 'Dirección de prueba',
                'sector' => 'Sector de prueba',
                'role' => 'seller',
                'is_verified' => true
            ]);
            $this->command->info('Persona de prueba creada.');
        }

        // Crear precios para diferentes fechas (últimos 30 días)
        $dates = [
            now()->subDays(28), // Lunes hace 4 semanas
            now()->subDays(21), // Lunes hace 3 semanas
            now()->subDays(14), // Lunes hace 2 semanas
            now()->subDays(7),  // Lunes hace 1 semana
            now(),              // Hoy
        ];

        foreach ($dates as $date) {
            foreach ($products as $product) {
                // Generar precio aleatorio entre 1000 y 50000 bolívares
                $basePrice = rand(1000, 50000);
                
                // Agregar variación de precio según la fecha (simular inflación)
                $daysDiff = now()->diffInDays($date);
                $priceVariation = 1 + ($daysDiff * 0.02); // 2% de inflación diaria
                $price = round($basePrice * $priceVariation, 2);

                // Ocasionalmente crear precios en USD
                $currency = rand(1, 10) <= 2 ? 'USD' : 'VES';
                if ($currency === 'USD') {
                    $price = round($price / 35, 2); // Convertir a USD (tasa aproximada)
                }

                MarketPrice::create([
                    'product_id' => $product->id,
                    'price' => $price,
                    'currency' => $currency,
                    'price_date' => $date->format('Y-m-d'),
                    'notes' => $this->getRandomNote(),
                    'is_active' => true,
                    'updated_by' => $person->id,
                ]);
            }
        }

        $this->command->info('Precios de mercado creados exitosamente.');
    }

    private function getRandomNote(): ?string
    {
        $notes = [
            'Precio actualizado desde Coche',
            'Variación por temporada',
            'Precio estable',
            'Ligero incremento por demanda',
            'Precio reducido por oferta',
            'Actualización semanal',
            'Precio de referencia',
            'Variación por disponibilidad',
            null, // Sin notas
        ];

        return $notes[array_rand($notes)];
    }
}
