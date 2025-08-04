<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestScheduler extends Command
{
    protected $signature = 'test:scheduler';
    protected $description = 'Comando de prueba para verificar que el scheduler funciona';

    public function handle()
    {
        Log::info('TestScheduler ejecutado correctamente en: ' . now());
        $this->info('Comando de prueba ejecutado correctamente');
        return Command::SUCCESS;
    }
}
