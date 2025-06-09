<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Alfredo Romero',
            'email' => 'alfredoromerox15@gmail.com',
            'password' => Hash::make('12345'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'type' => 'admin',
            'business_name' => 'Administrador del Sistema',
            'rif_ci' => 'ADMIN',
            'address' => 'N/A',
            'city' => 'N/A',
            'state' => 'N/A',
            'postal_code' => 'N/A',
            'description' => 'Usuario administrador del sistema',
            'verification_status' => 'verified',
            'verification_documents' => json_encode([]),
            'social_media' => json_encode([])
        ]);
    }
} 