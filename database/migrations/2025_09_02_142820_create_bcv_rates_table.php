<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bcv_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3)->notNull();
            $table->decimal('rate', 20, 8)->notNull();
            $table->timestamp('fetched_at')->notNull();
            $table->string('source', 50)->default('BCV');
            $table->timestamps();
            
            $table->index(['currency_code', 'fetched_at']);
            $table->comment('Tasas de cambio del BCV');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bcv_rates');
    }
};
