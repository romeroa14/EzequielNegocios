<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowTables extends Command
{
    protected $signature = 'db:tables';
    protected $description = 'Show all tables in the database';

    public function handle()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $this->info('Tables in database:');
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                $count = DB::table($tableName)->count();
                $this->line("- {$tableName} ({$count} records)");
            }
            return 0;
        } catch (\Exception $e) {
            $this->error("Error getting tables: " . $e->getMessage());
            return 1;
        }
    }
} 