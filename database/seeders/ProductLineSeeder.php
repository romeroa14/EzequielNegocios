<?php

namespace Database\Seeders;

use App\Models\ProductLine;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Database\Seeder;

class ProductLineSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categorías
        $granosCategory = ProductCategory::where('name', 'Granos y Cereales')->first();
        $frutasCategory = ProductCategory::where('name', 'Frutas')->first();
        $hortalizasCategory = ProductCategory::where('name', 'Hortalizas')->first();
        $proteicosCategory = ProductCategory::where('name', 'Proteínas Agrícolas')->first();
        $insumosCategory = ProductCategory::where('name', 'Insumos Agrícolas')->first();

        if (!$granosCategory || !$frutasCategory || !$hortalizasCategory || !$proteicosCategory || !$insumosCategory) {
            $this->command->error('Por favor ejecuta primero: php artisan db:seed --class=ProductCategorySeeder');
            return;
        }

        // Obtener subcategorías
        $granosBasicosSubcat = ProductSubcategory::where('name', 'Granos Básicos')->first();
        $frutasTropicalesSubcat = ProductSubcategory::where('name', 'Frutas Tropicales')->first();
        $verdurasHojaSubcat = ProductSubcategory::where('name', 'Verduras de Hoja')->first();
        $frutosSecosSubcat = ProductSubcategory::where('name', 'Frutos Secos')->first();
        $fertilizantesSubcat = ProductSubcategory::where('name', 'Fertilizantes')->first();

        if (!$granosBasicosSubcat || !$frutasTropicalesSubcat || !$verdurasHojaSubcat || !$frutosSecosSubcat || !$fertilizantesSubcat) {
            $this->command->error('Por favor ejecuta primero: php artisan db:seed --class=ProductSubcategorySeeder');
            return;
        }

        $lines = [
            [
                'product_category_id' => $granosCategory->id,
                'product_subcategory_id' => $granosBasicosSubcat->id,
                'name' => 'Harinas y Derivados',
                'description' => 'Productos de harina de maíz, trigo y otros cereales',
                'is_active' => true,
            ],
            [
                'product_category_id' => $proteicosCategory->id,
                'product_subcategory_id' => $frutosSecosSubcat->id,
                'name' => 'Aceites y Grasas',
                'description' => 'Aceites vegetales, mantequillas y margarinas',
                'is_active' => true,
            ],
            [
                'product_category_id' => $proteicosCategory->id,
                'product_subcategory_id' => $frutosSecosSubcat->id,
                'name' => 'Lácteos y Derivados',
                'description' => 'Leche, quesos, yogurt y otros derivados lácteos',
                'is_active' => true,
            ],
            [
                'product_category_id' => $insumosCategory->id,
                'product_subcategory_id' => $fertilizantesSubcat->id,
                'name' => 'Condimentos y Especias',
                'description' => 'Sal, pimienta, especias y sazonadores',
                'is_active' => true,
            ],
            [
                'product_category_id' => $hortalizasCategory->id,
                'product_subcategory_id' => $verdurasHojaSubcat->id,
                'name' => 'Enlatados y Conservas',
                'description' => 'Productos enlatados y en conserva',
                'is_active' => true,
            ],
            [
                'product_category_id' => $granosCategory->id,
                'product_subcategory_id' => $granosBasicosSubcat->id,
                'name' => 'Granos y Cereales',
                'description' => 'Arroz, frijoles, lentejas y otros granos',
                'is_active' => true,
            ],
            [
                'product_category_id' => $frutasCategory->id,
                'product_subcategory_id' => $frutasTropicalesSubcat->id,
                'name' => 'Snacks y Golosinas',
                'description' => 'Productos de confitería y bocadillos',
                'is_active' => true,
            ],
            [
                'product_category_id' => $frutasCategory->id,
                'product_subcategory_id' => $frutasTropicalesSubcat->id,
                'name' => 'Bebidas y Refrescos',
                'description' => 'Bebidas carbonatadas, jugos y néctares',
                'is_active' => true,
            ],
            [
                'product_category_id' => $granosCategory->id,
                'product_subcategory_id' => $granosBasicosSubcat->id,
                'name' => 'Pastas y Fideos',
                'description' => 'Pasta seca, fideos y productos relacionados',
                'is_active' => true,
            ],
            [
                'product_category_id' => $insumosCategory->id,
                'product_subcategory_id' => $fertilizantesSubcat->id,
                'name' => 'Salsas y Aderezos',
                'description' => 'Mayonesa, mostaza, ketchup y otras salsas',
                'is_active' => true,
            ],
        ];

        foreach ($lines as $line) {
            ProductLine::create($line);
        }
    }
} 