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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->enum('status', ['pending', 'confirmed', 'in_transit', 'delivered', 'cancelled']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded']);
            $table->string('payment_method');
            $table->json('delivery_address');
            $table->date('delivery_date');
            $table->string('notes');
            $table->timestamps();
        });

        Schema::create('order_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['order_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_person');
        Schema::dropIfExists('orders');
    }
};
