<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Frutas',
                'description' => 'Productos frutales frescos de temporada',
                'icon' => 'fruits',
                'is_active' => true
            ],
            [
                'name' => 'Hortalizas',
                'description' => 'Verduras y hortalizas para consumo directo',
                'icon' => 'vegetables',
                'is_active' => true
            ],
            [
                'name' => 'Granos y Cereales',
                'description' => 'Granos básicos para alimentación',
                'icon' => 'grains',
                'is_active' => true
            ],
            [
                'name' => 'Tubérculos y Raíces',
                'description' => 'Cultivos de raíz comestible',
                'icon' => 'roots',
                'is_active' => true
            ],
            [
                'name' => 'Proteínas Agrícolas',
                'description' => 'Productos con alto contenido proteico',
                'icon' => 'proteins',
                'is_active' => true
            ],
            [
                'name' => 'Insumos Agrícolas',
                'description' => 'Productos para la producción agrícola',
                'icon' => 'supplies',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
} 