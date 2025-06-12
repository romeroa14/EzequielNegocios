<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('person_id')->constrained('people')->onDelete('restrict');
            $table->enum('type', ['entrada', 'salida', 'ajuste', 'merma']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('previous_stock', 10, 2);
            $table->decimal('current_stock', 10, 2);
            $table->string('reference_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
}; 