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

        $this->call([
            CountryTableSeeder::class,
            StateTableSeeder::class,
            MunicipalityTableSeeder::class,
            ParishTableSeeder::class,
            ProductCategorySeeder::class,
            ProductSubcategorySeeder::class,
            ProductPresentationSeeder::class,
            ProductLineSeeder::class,
            BrandSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
