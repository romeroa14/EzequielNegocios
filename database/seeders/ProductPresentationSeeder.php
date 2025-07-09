<?php

namespace Database\Seeders;

use App\Models\ProductPresentation;
use Illuminate\Database\Seeder;

class ProductPresentationSeeder extends Seeder
{
    public function run(): void
    {
        $presentations = [
            // Presentaciones por peso
            [
                'name' => 'Paquete 1 kg',
                'description' => 'Presentación estándar de 1 kilogramo',
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 500 g',
                'description' => 'Presentación mediana de 500 gramos',
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 250 g',
                'description' => 'Presentación pequeña de 250 gramos',
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 2.5 kg',
                'description' => 'Presentación familiar de 2.5 kilogramos',
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 5 kg',
                'description' => 'Presentación económica de 5 kilogramos',
                'is_active' => true,
            ],
            // Presentaciones por volumen
            [
                'name' => 'Botella 1 L',
                'description' => 'Presentación estándar de 1 litro',
                'is_active' => true,
            ],
            [
                'name' => 'Botella 2 L',
                'description' => 'Presentación familiar de 2 litros',
                'is_active' => true,
            ],
            [
                'name' => 'Botella 500 ml',
                'description' => 'Presentación mediana de 500 mililitros',
                'is_active' => true,
            ],
            [
                'name' => 'Botella 250 ml',
                'description' => 'Presentación personal de 250 mililitros',
                'is_active' => true,
            ],
            // Presentaciones por unidad
            [
                'name' => 'Pack 6 unidades',
                'description' => 'Paquete de 6 unidades',
                'is_active' => true,
            ],
            [
                'name' => 'Pack 12 unidades',
                'description' => 'Paquete de 12 unidades',
                'is_active' => true,
            ],
            [
                'name' => 'Unidad',
                'description' => 'Venta por unidad individual',
                'is_active' => true,
            ],
            // Presentaciones específicas
            [
                'name' => 'Lata 350 g',
                'description' => 'Presentación en lata de 350 gramos',
                'is_active' => true,
            ],
            [
                'name' => 'Caja 400 g',
                'description' => 'Presentación en caja de 400 gramos',
                'is_active' => true,
            ],
            [
                'name' => 'Doypack 1 kg',
                'description' => 'Empaque flexible con zipper de 1 kilogramo',
                'is_active' => true,
            ],
        ];

        foreach ($presentations as $presentation) {
            ProductPresentation::create($presentation);
        }
    }
} 