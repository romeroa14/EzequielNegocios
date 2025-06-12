<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Solo eliminar si existe la columna user_id
            if (Schema::hasColumn('people', 'user_id')) {
                // Verificar si existe el constraint antes de eliminarlo
                $foreignKeys = DB::select("
                    SELECT constraint_name 
                    FROM information_schema.table_constraints 
                    WHERE table_name = 'people' 
                    AND constraint_type = 'FOREIGN KEY' 
                    AND constraint_name LIKE '%user_id%'
                ");
                
                if (!empty($foreignKeys)) {
                    $table->dropForeign(['user_id']);
                }
                
                $table->dropColumn('user_id');
            }
            
            // Actualizar el enum de roles para solo incluir roles del frontend
            if (Schema::hasColumn('people', 'role')) {
                $table->dropColumn('role');
                $table->enum('role', ['buyer', 'seller'])->default('buyer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Restaurar la columna user_id si no existe
            if (!Schema::hasColumn('people', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
            
            // Restaurar el enum original
            $table->dropColumn('role');
            $table->enum('role', [
                'buyer',
                'seller',
                'technician',
                'support',
                'admin',
                'company'
            ])->default('buyer');
        });
    }
};
