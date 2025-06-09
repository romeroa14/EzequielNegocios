<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Crear un usuario administrador';

    public function handle()
    {
        $name = $this->ask('Nombre del administrador');
        $email = $this->ask('Email del administrador');
        $password = $this->secret('Contraseña');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
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

        $this->info('Usuario administrador creado exitosamente');
    }
} 