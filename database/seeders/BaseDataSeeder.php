<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BaseDataSeeder extends Seeder
{
    /**
     * Seed the application's database with essential base data.
     */
    public function run(): void
    {
        $this->call([
            CountryTableSeeder::class,
            StateTableSeeder::class,
            MunicipalityTableSeeder::class,
            ParishTableSeeder::class,
        ]);
    }
} 