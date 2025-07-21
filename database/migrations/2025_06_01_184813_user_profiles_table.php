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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['producer', 'distributor', 'buyer', 'admin']);
            $table->string('business_name');
            $table->string('rif_ci');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('description');
            $table->enum('verification_status', ['pending', 'verified', 'rejected']);
            $table->json('verification_documents');
            $table->json('social_media');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
