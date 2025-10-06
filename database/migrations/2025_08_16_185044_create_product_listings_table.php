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
        // Crear tabla de mercados antes para permitir la FK en product_listings
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->enum('category', ['wholesale', 'retail'])->default('wholesale');
            $table->string('photo')->nullable();
            // Ubicación normalizada
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->nullOnDelete();
            $table->foreignId('parish_id')->nullable()->constrained('parishes')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('product_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('unit_price', 10, 2);
            $table->enum('currency_type', ['USD', 'VES'])->default('USD')->comment('Moneda en la que está expresado el precio');
            $table->enum('quality_grade', ['premium', 'standard', 'economic']);
            $table->boolean('is_harvesting')->default(false)->comment('Indica si el producto está en cosecha');
            $table->date('harvest_date')->nullable();
            $table->json('images')->nullable();
            $table->foreignId('product_presentation_id')->constrained('product_presentations')->onDelete('cascade');
            $table->decimal('presentation_quantity', 10, 2)->default(1.00);
            // Tipo de venta: puerta de finca o mercado mayorista
            $table->enum('selling_location_type', ['farm_gate', 'wholesale_market'])->default('farm_gate');
            // Relación opcional con mercados mayoristas
            $table->foreignId('market_id')->nullable()->constrained('markets')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->nullOnDelete();
            $table->foreignId('parish_id')->nullable()->constrained('parishes')->nullOnDelete();
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
        Schema::dropIfExists('markets');
    }
};
