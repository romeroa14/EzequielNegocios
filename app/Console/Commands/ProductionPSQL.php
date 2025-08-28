<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProductionPSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psql:prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connect directly to production PostgreSQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔗 Conectando directamente a PostgreSQL de producción...');
        
        // Configuración de producción
        $host = 'ep-soft-snow-a5f00zvi.aws-us-east-2.pg.laravel.cloud';
        $port = '5432';
        $database = 'main';
        $username = 'laravel';
        $password = 'npg_Gr42CaFlQUKs';
        
        // Construir comando psql
        $command = "PGPASSWORD={$password} psql -h {$host} -p {$port} -U {$username} -d {$database}";
        
        $this->warn('⚠️  Conectando a PRODUCCIÓN - Ten cuidado!');
        $this->line('');
        $this->line('Comandos útiles:');
        $this->line('  \\dt - Listar tablas');
        $this->line('  \\d table_name - Ver estructura de tabla');
        $this->line('  \\q - Salir');
        $this->line('');
        
        // Ejecutar psql directamente
        passthru($command);
        
        return 0;
    }
}
