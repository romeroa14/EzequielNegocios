<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Person;
use App\Models\Product;
use App\Models\ProductListing;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;

class ProductListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test producers
        $producers = [];
        
        // Producer 1 - Juan Pérez
        $user1 = User::firstOrCreate(
            ['email' => 'juan.perez.producer@example.com'],
            [
                'name' => 'Juan Pérez',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        $producer1 = Person::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'role' => 'seller',
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'phone' => '+58 414-123-4567',
                'address' => 'Finca Los Mangos, Carretera Nacional',
                'is_active' => true,
            ]
        );
        
        // Producer 2 - María González
        $user2 = User::firstOrCreate(
            ['email' => 'maria.gonzalez.producer@example.com'],
            [
                'name' => 'María González',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        $producer2 = Person::firstOrCreate(
            ['user_id' => $user2->id],
            [
                'role' => 'seller',
                'first_name' => 'María',
                'last_name' => 'González',
                'phone' => '+58 426-987-6543',
                'address' => 'Hacienda San Rafael, Valle Verde',
                'is_active' => true,
            ]
        );

        // Producer 3 - Carlos Rodríguez
        $user3 = User::firstOrCreate(
            ['email' => 'carlos.rodriguez.producer@example.com'],
            [
                'name' => 'Carlos Rodríguez',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        $producer3 = Person::firstOrCreate(
            ['user_id' => $user3->id],
            [
                'role' => 'seller',
                'first_name' => 'Carlos',
                'last_name' => 'Rodríguez',
                'phone' => '+58 412-555-0123',
                'address' => 'Granja El Paraíso, Sector Rural',
                'is_active' => true,
            ]
        );

        $producers = [$producer1, $producer2, $producer3];

        // Get categories and subcategories
        $fruitCategory = ProductCategory::where('name', 'Frutas')->first();
        $vegetableCategory = ProductCategory::where('name', 'Hortalizas')->first();
        $grainCategory = ProductCategory::where('name', 'Granos y Cereales')->first();

        if (!$fruitCategory || !$vegetableCategory || !$grainCategory) {
            $this->command->error('Por favor ejecuta primero: php artisan db:seed --class=ProductCategorySeeder');
            return;
        }

        $tropicalFruits = ProductSubcategory::where('name', 'Frutas Tropicales')->first();
        $leafyVegetables = ProductSubcategory::where('name', 'Verduras de Hoja')->first();
        $cereals = ProductSubcategory::where('name', 'Granos Básicos')->first();

        // Create sample products
        $products = [
            // Fruits
            [
                'category_id' => $fruitCategory->id,
                'subcategory_id' => $tropicalFruits->id,
                'name' => 'Mango',
                'description' => 'Mango tropical dulce y jugoso',
                'sku_base' => 'MNG-001',
                'unit_type' => 'kg',
                'image' => 'products/mango.jpg',
                'seasonal_info' => json_encode(['temporada' => 'Marzo - Junio']),
                'is_active' => true,
            ],
            [
                'category_id' => $fruitCategory->id,
                'subcategory_id' => $tropicalFruits->id,
                'name' => 'Aguacate',
                'description' => 'Aguacate cremoso de primera calidad',
                'sku_base' => 'AGU-001',
                'unit_type' => 'kg',
                'image' => 'products/aguacate.jpg',
                'seasonal_info' => json_encode(['temporada' => 'Disponible todo el año']),
                'is_active' => true,
            ],
            [
                'category_id' => $fruitCategory->id,
                'subcategory_id' => $tropicalFruits->id,
                'name' => 'Papaya',
                'description' => 'Papaya dulce y madura',
                'sku_base' => 'PAP-001',
                'unit_type' => 'unidad',
                'image' => 'products/papaya.jpg',
                'seasonal_info' => json_encode(['temporada' => 'Todo el año']),
                'is_active' => true,
            ],
            // Vegetables
            [
                'category_id' => $vegetableCategory->id,
                'subcategory_id' => $leafyVegetables->id,
                'name' => 'Lechuga',
                'description' => 'Lechuga fresca y crujiente',
                'sku_base' => 'LEC-001',
                'unit_type' => 'unidad',
                'image' => 'products/lechuga.jpg',
                'seasonal_info' => json_encode(['temporada' => 'Disponible todo el año']),
                'is_active' => true,
            ],
            [
                'category_id' => $vegetableCategory->id,
                'subcategory_id' => $leafyVegetables->id,
                'name' => 'Espinaca',
                'description' => 'Espinaca tierna rica en hierro',
                'sku_base' => 'ESP-001',
                'unit_type' => 'kg',
                'image' => 'products/espinaca.jpg',
                'seasonal_info' => json_encode(['temporada' => 'Mejor en temporada fresca']),
                'is_active' => true,
            ],
            // Grains
            [
                'category_id' => $grainCategory->id,
                'subcategory_id' => $cereals->id,
                'name' => 'Maíz',
                'description' => 'Maíz amarillo de excelente calidad',
                'sku_base' => 'MAI-001',
                'unit_type' => 'saco',
                'image' => 'products/maiz.jpg',
                'seasonal_info' => json_encode(['temporada' => 'Junio - Agosto']),
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Create 2-3 listings per product with different producers
            $numListings = rand(1, 3);
            for ($i = 0; $i < $numListings; $i++) {
                $producer = $producers[array_rand($producers)];
                
                ProductListing::create([
                    'person_id' => $producer->id,
                    'product_id' => $product->id,
                    'title' => $product->name . ' Premium - ' . $producer->first_name,
                    'description' => 'Delicioso ' . strtolower($product->name) . ' cultivado con técnicas orgánicas en nuestra finca familiar. Producto fresco, recién cosechado y de la más alta calidad. Perfecto para distribuidores y consumidores que buscan productos naturales y saludables.',
                    'quantity_available' => rand(50, 500),
                    'unit_price' => round(rand(100, 1000) / 100, 2), // $1.00 - $10.00
                    'wholesale_price' => round(rand(50, 800) / 100, 2), // 50% - 80% of unit price
                    'min_quantity_order' => rand(5, 20),
                    'max_quantity_order' => rand(100, 300),
                    'quality_grade' => ['premium', 'standard', 'economic'][rand(0, 2)],
                    'harvest_date' => now()->subDays(rand(1, 7)),
                    'expiry_date' => now()->addDays(rand(7, 30)),
                    'images' => [], // We'll add placeholder images later
                    'location_city' => ['Valencia', 'Maracay', 'Caracas', 'Barquisimeto', 'Mérida'][rand(0, 4)],
                    'location_state' => ['Carabobo', 'Aragua', 'Distrito Capital', 'Lara', 'Mérida'][rand(0, 4)],
                    'pickup_available' => true,
                    'delivery_available' => rand(0, 1) == 1,
                    'delivery_radius_km' => rand(0, 1) == 1 ? rand(10, 50) : null,
                    'status' => 'active',
                    'featured_until' => now()->addDays(30),
                ]);
            }
        }

        $this->command->info('Se crearon ' . ProductListing::count() . ' listados de productos con ' . count($producers) . ' productores.');
    }
}
