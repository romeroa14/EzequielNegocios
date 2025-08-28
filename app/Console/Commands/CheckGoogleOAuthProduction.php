<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckGoogleOAuthProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:check-production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar configuración de Google OAuth en producción';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configuración de Google OAuth en producción...');
        $this->line('');

        // Verificar configuración de Google OAuth
        $this->info('📋 Configuración de Google OAuth:');
        $this->line('Client ID: ' . config('services.google.client_id'));
        $this->line('Client Secret: ' . (config('services.google.client_secret') ? '✅ Configurado' : '❌ No configurado'));
        $this->line('Redirect URI: ' . config('services.google.redirect'));
        $this->line('');

        // Verificar si existe la columna google_id en people
        $this->info('📊 Verificando columna google_id en tabla people...');
        
        $columnExists = DB::select("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'people' AND column_name = 'google_id'
        ");

        if (empty($columnExists)) {
            $this->error('❌ La columna google_id NO existe en la tabla people');
            $this->line('Ejecuta: php artisan migrate');
        } else {
            $this->info('✅ La columna google_id existe en la tabla people');
        }
        $this->line('');

        // Verificar usuarios con Google OAuth
        $this->info('👥 Verificando usuarios con Google OAuth...');
        
        $stats = DB::select("
            SELECT 
                COUNT(*) as total_people,
                COUNT(CASE WHEN google_id IS NOT NULL THEN 1 END) as people_with_google_id,
                COUNT(CASE WHEN google_id IS NULL THEN 1 END) as people_without_google_id
            FROM people
        ")[0];

        $this->line('Total de personas: ' . $stats->total_people);
        $this->line('Con Google OAuth: ' . $stats->people_with_google_id);
        $this->line('Sin Google OAuth: ' . $stats->people_without_google_id);
        $this->line('');

        // Mostrar usuarios con Google OAuth
        if ($stats->people_with_google_id > 0) {
            $this->info('📋 Usuarios con Google OAuth:');
            
            $users = DB::select("
                SELECT 
                    id,
                    first_name,
                    last_name,
                    email,
                    google_id,
                    is_verified,
                    role,
                    created_at
                FROM people 
                WHERE google_id IS NOT NULL
                ORDER BY created_at DESC
            ");

            $headers = ['ID', 'Nombre', 'Email', 'Google ID', 'Verificado', 'Rol', 'Creado'];
            $rows = [];

            foreach ($users as $user) {
                $rows[] = [
                    $user->id,
                    $user->first_name . ' ' . $user->last_name,
                    $user->email,
                    substr($user->google_id, 0, 20) . '...',
                    $user->is_verified ? '✅ Sí' : '❌ No',
                    $user->role,
                    $user->created_at
                ];
            }

            $this->table($headers, $rows);
        }

        $this->line('');
        $this->warn('⚠️  IMPORTANTE: Verifica que en Google Cloud Console tengas configurada la URL correcta:');
        $this->line('   https://ezequielnegocios-ezequielnegocios-ytjhfb.laravel.cloud/auth/google/callback');
        $this->line('');
        $this->warn('⚠️  NO: https://ezequielnegocios-ezequielnegocios-ytjhfb.laravel.cloud/auth/g');
        $this->line('');

        $this->info('✅ Verificación completada');

        return 0;
    }
}
