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
            // Agregar user_id nullable para la relación opcional con users
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            
            // Solo agregar campos nuevos, no modificar existentes
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->nullable()->after('email_verified_at');
            $table->rememberToken()->after('password');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Agregar campo role a users para admin
            $table->enum('role', ['admin', 'producer', 'technician', 'support'])
                  ->default('producer')
                  ->after('is_active');
        });
        
        // Solo ejecutar migración de datos si existen datos previos
        if (DB::table('people')->exists()) {
            $this->migrateExistingData();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'password', 'remember_token']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
    
    private function migrateExistingData(): void
    {
        // Migrar emails de users a people para los registros que no tienen email
        DB::table('people')
            ->whereNull('email')
            ->orWhere('email', '')
            ->update([
                'email' => DB::raw('(SELECT email FROM users WHERE users.id = people.user_id)'),
                'password' => DB::raw('(SELECT password FROM users WHERE users.id = people.user_id)'),
            ]);
            
        // Actualizar el rol del usuario admin principal
        DB::table('users')
            ->where('email', 'alfredoromerox15@gmail.com')
            ->update(['role' => 'admin']);
    }
};
