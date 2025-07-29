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
        Schema::create('product_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('unit_price', 10, 2);
            $table->enum('quality_grade', ['premium', 'standard', 'economic']);
            $table->date('harvest_date');
            $table->json('images')->nullable();
            $table->foreignId('product_presentation_id')->constrained('product_presentations')->onDelete('cascade');
            $table->decimal('presentation_quantity', 10, 2)->default(1.00);
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('municipality_id')->constrained('municipalities')->onDelete('cascade');
            $table->foreignId('parish_id')->constrained('parishes')->onDelete('cascade');
            $table->enum('status', ['active', 'pending', 'sold_out', 'inactive'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_listings');
    }
};
