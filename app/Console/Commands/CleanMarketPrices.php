<?php

namespace App\Console\Commands;

use App\Models\MarketPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanMarketPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:clean-prices {--dry-run : Mostrar qué se haría sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia los precios de mercado duplicados, dejando solo uno activo por producto';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Limpiando precios de mercado duplicados...');

        if ($this->option('dry-run')) {
            $this->info('🔍 Modo DRY-RUN: Solo mostrando qué se haría');
        }

        // Obtener productos con múltiples precios activos
        $duplicates = DB::table('market_prices')
            ->select('product_id', DB::raw('COUNT(*) as price_count'))
            ->where('is_active', true)
            ->groupBy('product_id')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No hay productos con precios duplicados');
            return;
        }

        $this->warn("📊 Se encontraron " . count($duplicates) . " productos con precios duplicados:");

        foreach ($duplicates as $duplicate) {
            $product = \App\Models\Product::find($duplicate->product_id);
            $productName = $product ? $product->name : "Producto ID: {$duplicate->product_id}";
            
            $this->line("  • {$productName}: {$duplicate->price_count} precios activos");
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 Para ejecutar la limpieza, ejecuta: php artisan market:clean-prices');
            return;
        }

        if (!$this->confirm('¿Deseas continuar con la limpieza?')) {
            $this->info('❌ Operación cancelada');
            return;
        }

        $bar = $this->output->createProgressBar(count($duplicates));
        $bar->start();

        foreach ($duplicates as $duplicate) {
            // Obtener todos los precios activos del producto
            $prices = MarketPrice::where('product_id', $duplicate->product_id)
                ->where('is_active', true)
                ->orderBy('price_date', 'desc')
                ->get();

            // Mantener solo el más reciente activo
            $latestPrice = $prices->first();
            $oldPrices = $prices->slice(1);

            // Desactivar precios antiguos
            foreach ($oldPrices as $oldPrice) {
                $oldPrice->update(['is_active' => false]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Verificar resultado
        $remainingDuplicates = DB::table('market_prices')
            ->select('product_id', DB::raw('COUNT(*) as count'))
            ->where('is_active', true)
            ->groupBy('product_id')
            ->having('count', '>', 1)
            ->count();

        if ($remainingDuplicates === 0) {
            $this->info('✅ Limpieza completada exitosamente');
            $this->info('📊 Ahora cada producto tiene solo un precio activo');
        } else {
            $this->warn("⚠️  Aún quedan {$remainingDuplicates} productos con precios duplicados");
        }
    }
}
