<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixProductsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-data {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix products data to comply with universal/normal product constraints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN - No changes will be made');
        }

        $this->info('🔧 Arreglando datos de productos...');
        $this->line('');

        // 1. Verificar estado actual
        $this->showCurrentStatus();

        // 2. Obtener productor universal por defecto
        $universalProducer = User::where('role', 'producer')
            ->where('is_universal', true)
            ->where('is_active', true)
            ->first();

        if (!$universalProducer) {
            $this->error('❌ No se encontró ningún productor universal activo');
            return 1;
        }

        $this->info("✅ Productor universal encontrado: {$universalProducer->name} (ID: {$universalProducer->id})");

        // 3. Obtener vendedor por defecto
        $defaultSeller = Person::where('role', 'seller')
            ->where('is_active', true)
            ->first();

        if (!$defaultSeller) {
            $this->error('❌ No se encontró ningún vendedor activo');
            return 1;
        }

        $this->info("✅ Vendedor por defecto encontrado: {$defaultSeller->full_name} (ID: {$defaultSeller->id})");

        $this->line('');

        // 4. Arreglar productos universales
        $this->fixUniversalProducts($universalProducer, $isDryRun);

        // 5. Arreglar productos normales
        $this->fixNormalProducts($defaultSeller, $isDryRun);

        // 6. Mostrar estado final
        $this->line('');
        $this->showCurrentStatus();

        if (!$isDryRun) {
            $this->info('✅ Datos de productos arreglados correctamente');
        } else {
            $this->info('✅ Revisión completada (dry run)');
        }

        return 0;
    }

    private function showCurrentStatus()
    {
        $this->info('📊 Estado actual de productos:');
        
        $stats = DB::select("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN is_universal = true THEN 1 END) as universal,
                COUNT(CASE WHEN is_universal = false THEN 1 END) as normal,
                COUNT(CASE WHEN is_universal = true AND creator_user_id IS NOT NULL AND person_id IS NULL THEN 1 END) as correct_universal,
                COUNT(CASE WHEN is_universal = false AND person_id IS NOT NULL AND creator_user_id IS NULL THEN 1 END) as correct_normal,
                COUNT(CASE WHEN is_universal = true AND (creator_user_id IS NULL OR person_id IS NOT NULL) THEN 1 END) as broken_universal,
                COUNT(CASE WHEN is_universal = false AND (person_id IS NULL OR creator_user_id IS NOT NULL) THEN 1 END) as broken_normal
            FROM products
        ")[0];

        $this->line("  Total: {$stats->total}");
        $this->line("  Universales: {$stats->universal} (correctos: {$stats->correct_universal}, rotos: {$stats->broken_universal})");
        $this->line("  Normales: {$stats->normal} (correctos: {$stats->correct_normal}, rotos: {$stats->broken_normal})");
        $this->line('');
    }

    private function fixUniversalProducts($universalProducer, $isDryRun)
    {
        $this->info('🔧 Arreglando productos universales...');

        $brokenUniversal = Product::where('is_universal', true)
            ->where(function ($query) {
                $query->whereNull('creator_user_id')
                      ->orWhereNotNull('person_id');
            })
            ->get();

        if ($brokenUniversal->isEmpty()) {
            $this->line('  ✅ No hay productos universales rotos');
            return;
        }

        $this->line("  📋 Encontrados {$brokenUniversal->count()} productos universales rotos:");

        foreach ($brokenUniversal as $product) {
            $this->line("    - ID {$product->id}: {$product->name}");
            $this->line("      Actual: creator_user_id={$product->creator_user_id}, person_id={$product->person_id}");
            
            if (!$isDryRun) {
                $product->update([
                    'creator_user_id' => $universalProducer->id,
                    'person_id' => null
                ]);
                $this->line("      ✅ Arreglado: creator_user_id={$universalProducer->id}, person_id=null");
            } else {
                $this->line("      🔄 Se cambiaría a: creator_user_id={$universalProducer->id}, person_id=null");
            }
        }
    }

    private function fixNormalProducts($defaultSeller, $isDryRun)
    {
        $this->info('🔧 Arreglando productos normales...');

        $brokenNormal = Product::where('is_universal', false)
            ->where(function ($query) {
                $query->whereNull('person_id')
                      ->orWhereNotNull('creator_user_id');
            })
            ->get();

        if ($brokenNormal->isEmpty()) {
            $this->line('  ✅ No hay productos normales rotos');
            return;
        }

        $this->line("  📋 Encontrados {$brokenNormal->count()} productos normales rotos:");

        foreach ($brokenNormal as $product) {
            $this->line("    - ID {$product->id}: {$product->name}");
            $this->line("      Actual: creator_user_id={$product->creator_user_id}, person_id={$product->person_id}");
            
            if (!$isDryRun) {
                $product->update([
                    'creator_user_id' => null,
                    'person_id' => $defaultSeller->id
                ]);
                $this->line("      ✅ Arreglado: creator_user_id=null, person_id={$defaultSeller->id}");
            } else {
                $this->line("      🔄 Se cambiaría a: creator_user_id=null, person_id={$defaultSeller->id}");
            }
        }
    }
}
