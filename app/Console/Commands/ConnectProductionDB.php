<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class ConnectProductionDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:production 
                            {action : The action to perform (status|migrate|seed|tinker|backup)}
                            {--table= : Specific table to work with}
                            {--limit=10 : Limit for queries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to production database and perform actions';

    /**
     * Production database configuration
     */
    private array $productionConfig = [
        'driver' => 'pgsql',
        'host' => 'ep-soft-snow-a5f00zvi.aws-us-east-2.pg.laravel.cloud',
        'port' => '5432',
        'database' => 'main',
        'username' => 'laravel',
        'password' => 'npg_Gr42CaFlQUKs',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        // Configurar conexión de producción
        $this->setupProductionConnection();
        
        try {
            // Probar conexión
            DB::connection('production')->getPdo();
            $this->info('✅ Conexión a producción establecida correctamente');
            
            switch ($action) {
                case 'status':
                    $this->showStatus();
                    break;
                case 'migrate':
                    $this->runMigrations();
                    break;
                case 'seed':
                    $this->runSeeders();
                    break;
                case 'tinker':
                    $this->runTinker();
                    break;
                case 'backup':
                    $this->createBackup();
                    break;
                default:
                    $this->error("Acción no válida: {$action}");
                    $this->info('Acciones disponibles: status, migrate, seed, tinker, backup');
                    return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error conectando a la base de datos de producción:');
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }

    /**
     * Configurar conexión de producción
     */
    private function setupProductionConnection()
    {
        Config::set('database.connections.production', $this->productionConfig);
        
        // Establecer como conexión por defecto temporalmente
        Config::set('database.default', 'production');
    }

    /**
     * Mostrar estado de la base de datos
     */
    private function showStatus()
    {
        $this->info('📊 Estado de la Base de Datos de Producción');
        $this->line('');

        // Información de conexión
        $this->info('🔗 Información de Conexión:');
        $this->line("Host: {$this->productionConfig['host']}");
        $this->line("Database: {$this->productionConfig['database']}");
        $this->line("User: {$this->productionConfig['username']}");
        $this->line('');

        // Tablas principales
        $this->info('📋 Tablas Principales:');
        $tables = ['users', 'products', 'product_categories', 'product_subcategories', 'product_lines', 'brands', 'product_presentations'];
        
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->line("  {$table}: {$count} registros");
            } catch (\Exception $e) {
                $this->line("  {$table}: ❌ Error - {$e->getMessage()}");
            }
        }

        // Migraciones
        $this->line('');
        $this->info('🔄 Estado de Migraciones:');
        try {
            $migrations = DB::table('migrations')->count();
            $this->line("  Migraciones ejecutadas: {$migrations}");
        } catch (\Exception $e) {
            $this->line("  ❌ Error verificando migraciones: {$e->getMessage()}");
        }
    }

    /**
     * Ejecutar migraciones en producción
     */
    private function runMigrations()
    {
        if (!$this->confirm('¿Estás seguro de que quieres ejecutar migraciones en PRODUCCIÓN?')) {
            $this->info('Operación cancelada');
            return;
        }

        $this->info('🔄 Ejecutando migraciones en producción...');
        
        try {
            $output = shell_exec('php artisan migrate --database=production --force 2>&1');
            $this->line($output);
            $this->info('✅ Migraciones ejecutadas correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error ejecutando migraciones: ' . $e->getMessage());
        }
    }

    /**
     * Ejecutar seeders en producción
     */
    private function runSeeders()
    {
        if (!$this->confirm('¿Estás seguro de que quieres ejecutar seeders en PRODUCCIÓN?')) {
            $this->info('Operación cancelada');
            return;
        }

        $this->info('🌱 Ejecutando seeders en producción...');
        
        try {
            $output = shell_exec('php artisan db:seed --database=production --force 2>&1');
            $this->line($output);
            $this->info('✅ Seeders ejecutados correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error ejecutando seeders: ' . $e->getMessage());
        }
    }

    /**
     * Ejecutar Tinker en producción
     */
    private function runTinker()
    {
        $this->warn('⚠️  Ejecutando Tinker en PRODUCCIÓN - Ten cuidado!');
        
        if (!$this->confirm('¿Estás seguro de que quieres abrir Tinker en PRODUCCIÓN?')) {
            $this->info('Operación cancelada');
            return;
        }

        $this->info('🔧 Abriendo Tinker en producción...');
        $this->line('Usa la conexión de producción: DB::connection("production")');
        
        // Ejecutar tinker con la conexión de producción
        passthru('php artisan tinker --database=production');
    }

    /**
     * Crear backup de la base de datos
     */
    private function createBackup()
    {
        $this->info('💾 Creando backup de la base de datos de producción...');
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_production_{$timestamp}.sql";
        
        try {
            $command = "PGPASSWORD={$this->productionConfig['password']} pg_dump -h {$this->productionConfig['host']} -p {$this->productionConfig['port']} -U {$this->productionConfig['username']} -d {$this->productionConfig['database']} > {$filename}";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info("✅ Backup creado exitosamente: {$filename}");
            } else {
                $this->error('❌ Error creando backup');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error creando backup: ' . $e->getMessage());
        }
    }
}
