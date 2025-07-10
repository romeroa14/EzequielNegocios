<?php

namespace Database\Seeders;

use App\Models\ProductPresentation;
use Illuminate\Database\Seeder;

class ProductPresentationSeeder extends Seeder
{
    public function run(): void
    {
        $presentations = [
            // Presentaciones base
            [
                'name' => 'Kilogramos',
                'description' => 'Medida de peso en kilogramos',
                'unit_type' => 'kg',
                'is_active' => true,
            ],
            [
                'name' => 'Toneladas',
                'description' => 'Medida de peso en toneladas',
                'unit_type' => 'ton',
                'is_active' => true,
            ],
            [
                'name' => 'Paquete',
                'description' => 'Presentación en paquete',
                'unit_type' => 'paquete',
                'is_active' => true,
            ],
            [
                'name' => 'Saco',
                'description' => 'Presentación en saco',
                'unit_type' => 'saco',
                'is_active' => true,
            ],
            [
                'name' => 'Caja',
                'description' => 'Presentación en caja',
                'unit_type' => 'caja',
                'is_active' => true,
            ],
            [
                'name' => 'Unidad',
                'description' => 'Venta por unidad',
                'unit_type' => 'unidad',
                'is_active' => true,
            ],
        ];

        foreach ($presentations as $presentation) {
            ProductPresentation::create($presentation);
        }
    }
} 