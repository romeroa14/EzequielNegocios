<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetTableCount extends Command
{
    protected $signature = 'db:table-count {table}';
    protected $description = 'Get the number of records in a table';

    public function handle()
    {
        $table = $this->argument('table');
        try {
            $count = DB::table($table)->count();
            $this->line($count);
            return 0;
        } catch (\Exception $e) {
            $this->error("Error counting records in table {$table}: " . $e->getMessage());
            return 1;
        }
    }
} 