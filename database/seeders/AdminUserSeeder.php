<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::updateOrCreate(
            ['email' => 'alfredoromerox15@gmail.com'],
            [
                'name' => 'Alfredo Romero',
                'password' => Hash::make('12345'),
                'email_verified_at' => now(),
                'is_active' => true,
                'role' => 'admin',
            ]
        );

        // Usuario de ejemplo para productor
        User::updateOrCreate(
            ['email' => 'productor@example.com'],
            [
                'name' => 'Productor de Ejemplo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'role' => 'producer',
            ]
        );

        // Usuario técnico
        User::updateOrCreate(
            ['email' => 'tecnico@example.com'],
            [
                'name' => 'Técnico del Sistema',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'role' => 'technician',
            ]
        );

        // Usuario de soporte
        User::updateOrCreate(
            ['email' => 'soporte@example.com'],
            [
                'name' => 'Soporte al Cliente',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'role' => 'support',
            ]
        );
    }
} 