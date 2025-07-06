<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductSubcategorySeeder;

class SeedProductData extends Command
{
    protected $signature = 'seed:product-data {--force : Force the operation to run in production}';
    protected $description = 'Seed product categories and subcategories';

    public function handle()
    {
        if (!$this->option('force') && app()->environment('production')) {
            $this->error('This command cannot be run in production without --force flag.');
            return 1;
        }

        $this->info('Seeding product data...');

        // Seed categories
        $this->call('db:seed', [
            '--class' => ProductCategorySeeder::class,
            '--force' => true
        ]);

        // Seed subcategories
        $this->call('db:seed', [
            '--class' => ProductSubcategorySeeder::class,
            '--force' => true
        ]);

        $this->info('Product data seeded successfully!');
        return 0;
    }
} 