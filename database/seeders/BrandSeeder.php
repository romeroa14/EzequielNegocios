<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // Get product lines
        $harinaLine = ProductLine::where('name', 'Harinas y Derivados')->first();
        $aceitesLine = ProductLine::where('name', 'Aceites y Grasas')->first();
        $lacteosLine = ProductLine::where('name', 'Lácteos y Derivados')->first();
        $condimentosLine = ProductLine::where('name', 'Condimentos y Especias')->first();
        $bebidasLine = ProductLine::where('name', 'Bebidas y Refrescos')->first();
        $salsasLine = ProductLine::where('name', 'Salsas y Aderezos')->first();
        $snacksLine = ProductLine::where('name', 'Snacks y Golosinas')->first();
        $pastasLine = ProductLine::where('name', 'Pastas y Fideos')->first();

        $brands = [
            [
                'name' => 'P.A.N.',
                'description' => 'Marca líder en harinas de maíz precocida',
                'is_active' => true,
                'product_line_id' => $harinaLine->id,
            ],
            [
                'name' => 'Juana',
                'description' => 'Marca tradicional de harinas y productos derivados',
                'is_active' => true,
                'product_line_id' => $harinaLine->id,
            ],
            [
                'name' => 'Polar',
                'description' => 'Empresa líder en alimentos y bebidas',
                'is_active' => true,
                'product_line_id' => $bebidasLine->id,
            ],
            [
                'name' => 'La Casa',
                'description' => 'Marca especializada en condimentos y especias',
                'is_active' => true,
                'product_line_id' => $condimentosLine->id,
            ],
            [
                'name' => 'Del Valle',
                'description' => 'Marca reconocida de jugos y néctares',
                'is_active' => true,
                'product_line_id' => $bebidasLine->id,
            ],
            [
                'name' => 'Heinz',
                'description' => 'Marca internacional de salsas y condimentos',
                'is_active' => true,
                'product_line_id' => $salsasLine->id,
            ],
            [
                'name' => 'Nestlé',
                'description' => 'Compañía multinacional de alimentos y bebidas',
                'is_active' => true,
                'product_line_id' => $snacksLine->id,
            ],
            [
                'name' => 'Kraft',
                'description' => 'Marca global de alimentos procesados',
                'is_active' => true,
                'product_line_id' => $snacksLine->id,
            ],
            [
                'name' => 'La Favorita',
                'description' => 'Marca de aceites y productos derivados',
                'is_active' => true,
                'product_line_id' => $aceitesLine->id,
            ],
            [
                'name' => 'Barilla',
                'description' => 'Marca líder en pastas y salsas',
                'is_active' => true,
                'product_line_id' => $pastasLine->id,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
} 