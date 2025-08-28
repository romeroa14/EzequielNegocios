<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class CleanGoogleOAuthPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:clean-passwords {--dry-run : Solo mostrar qué usuarios serían afectados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar contraseñas de usuarios de Google OAuth existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Limpiando contraseñas de usuarios de Google OAuth...');
        $this->line('');

        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se realizarán cambios');
            $this->line('');
        }

        // Obtener usuarios de Google OAuth con contraseñas
        $googleUsers = Person::whereNotNull('google_id')
            ->whereNotNull('password')
            ->get();

        $this->info("Total de usuarios de Google OAuth con contraseñas: {$googleUsers->count()}");
        $this->line('');

        if ($googleUsers->isEmpty()) {
            $this->info('✅ No hay usuarios de Google OAuth con contraseñas para limpiar');
            return 0;
        }

        $cleanedCount = 0;

        foreach ($googleUsers as $person) {
            $this->line("Usuario: {$person->first_name} {$person->last_name} ({$person->email})");
            $this->info("  Google ID: {$person->google_id}");
            
            if (!$dryRun) {
                $person->update(['password' => null]);
                $this->info("  ✅ Contraseña limpiada");
            } else {
                $this->info("  🔄 Se limpiaría la contraseña");
            }
            
            $cleanedCount++;
            $this->line('');
        }

        $this->info('📊 Resumen:');
        $this->line("  Usuarios procesados: {$cleanedCount}");
        
        if ($dryRun && $cleanedCount > 0) {
            $this->line('');
            $this->warn("Para aplicar los cambios, ejecuta sin --dry-run:");
            $this->line("  php artisan google:clean-passwords");
        }

        if (!$dryRun) {
            $this->info('✅ Contraseñas limpiadas exitosamente');
            $this->line('');
            $this->info('💡 Ahora estos usuarios solo pueden acceder usando Google OAuth');
        }

        return 0;
    }
}
