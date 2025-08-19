<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConnectProductionPSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:psql {--query= : SQL query to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect to production database using psql';

    /**
     * Production database configuration
     */
    private array $productionConfig = [
        'host' => 'ep-soft-snow-a5f00zvi.aws-us-east-2.pg.laravel.cloud',
        'port' => '5432',
        'database' => 'main',
        'username' => 'laravel',
        'password' => 'npg_Gr42CaFlQUKs',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = $this->option('query');
        
        if ($query) {
            $this->executeQuery($query);
        } else {
            $this->connectInteractive();
        }
        
        return 0;
    }

    /**
     * Conectar interactivamente a psql
     */
    private function connectInteractive()
    {
        $this->info('🔗 Conectando a la base de datos de producción...');
        $this->line('');
        $this->line('📋 Información de conexión:');
        $this->line("Host: {$this->productionConfig['host']}");
        $this->line("Database: {$this->productionConfig['database']}");
        $this->line("User: {$this->productionConfig['username']}");
        $this->line('');
        
        // Construir comando psql
        $command = "PGPASSWORD={$this->productionConfig['password']} psql -h {$this->productionConfig['host']} -p {$this->productionConfig['port']} -U {$this->productionConfig['username']} -d {$this->productionConfig['database']}";
        
        $this->warn('⚠️  Conectando a PRODUCCIÓN - Ten cuidado con los comandos que ejecutes!');
        
        if ($this->confirm('¿Continuar con la conexión?')) {
            $this->info('🔧 Abriendo psql...');
            $this->line('Comandos útiles:');
            $this->line('  \\dt - Listar tablas');
            $this->line('  \\d table_name - Ver estructura de tabla');
            $this->line('  \\q - Salir');
            $this->line('');
            
            passthru($command);
        } else {
            $this->info('Conexión cancelada');
        }
    }

    /**
     * Ejecutar query específica
     */
    private function executeQuery($query)
    {
        $this->info('🔍 Ejecutando query en producción...');
        $this->line("Query: {$query}");
        $this->line('');
        
        // Construir comando psql con query
        $command = "PGPASSWORD={$this->productionConfig['password']} psql -h {$this->productionConfig['host']} -p {$this->productionConfig['port']} -U {$this->productionConfig['username']} -d {$this->productionConfig['database']} -c \"{$query}\"";
        
        $this->warn('⚠️  Ejecutando query en PRODUCCIÓN!');
        
        if ($this->confirm('¿Estás seguro de ejecutar esta query?')) {
            $output = shell_exec($command . ' 2>&1');
            $this->line($output);
        } else {
            $this->info('Query cancelada');
        }
    }
}
