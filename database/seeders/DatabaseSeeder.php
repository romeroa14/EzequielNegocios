<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AdminUserSeeder::class,
            ProductCategorySeeder::class,
            ProductSubcategorySeeder::class,
            CountryTableSeeder::class,
            StateTableSeeder::class,
            MunicipalityTableSeeder::class,
            ParishTableSeeder::class,
        ]);
    }
}
