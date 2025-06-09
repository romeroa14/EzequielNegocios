<?php

namespace Database\Seeders;

use App\Models\ProductSubcategory;
use Illuminate\Database\Seeder;

class ProductSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            // Frutas
            [
                'category_id' => 1,
                'name' => 'Frutas Tropicales',
                'description' => 'Mango, piña, patilla, lechosa',
                'is_active' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Frutas Cítricas',
                'description' => 'Naranja, limón, mandarina, toronja',
                'is_active' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Frutas de Clima Templado',
                'description' => 'Manzana, pera, durazno',
                'is_active' => true
            ],
            
            // Hortalizas
            [
                'category_id' => 2,
                'name' => 'Verduras de Hoja',
                'description' => 'Lechuga, espinaca, acelga',
                'is_active' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Verduras de Fruto',
                'description' => 'Tomate, pimentón, berenjena',
                'is_active' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Bulbos y Tallos',
                'description' => 'Cebolla, ajo, puerro',
                'is_active' => true
            ],
            
            // Granos y Cereales
            [
                'category_id' => 3,
                'name' => 'Granos Básicos',
                'description' => 'Arroz, maíz, trigo',
                'is_active' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Leguminosas',
                'description' => 'Caraotas, frijoles, lentejas',
                'is_active' => true
            ],
            
            // Tubérculos
            [
                'category_id' => 4,
                'name' => 'Tubérculos Comunes',
                'description' => 'Papa, yuca, ñame',
                'is_active' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Raíces Andinas',
                'description' => 'Oca, mashua, maca',
                'is_active' => true
            ],
            
            // Proteínas
            [
                'category_id' => 5,
                'name' => 'Frutos Secos',
                'description' => 'Merey, nueces, almendras',
                'is_active' => true
            ],
            [
                'category_id' => 5,
                'name' => 'Semillas Oleaginosas',
                'description' => 'Ajonjolí, girasol, linaza',
                'is_active' => true
            ],
            
            // Insumos
            [
                'category_id' => 6,
                'name' => 'Fertilizantes',
                'description' => 'Abonos orgánicos y químicos',
                'is_active' => true
            ],
            [
                'category_id' => 6,
                'name' => 'Semillas Certificadas',
                'description' => 'Semillas de alta calidad genética',
                'is_active' => true
            ]
        ];

        foreach ($subcategories as $subcategory) {
            ProductSubcategory::create($subcategory);
        }
    }
} 