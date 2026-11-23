<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinica.com'], // Cambia esto por el correo real
            [
                'name' => 'Administrador del Sistema',
                'password' => Hash::make('Ceciamb2026.'), // Cambiar en producción
                'activo' => true,
            ]
        );

        // Asignar el rol de administrador
        $role = Role::where('name', 'administrador')->first();
        if ($role && !$admin->hasRole($role)) {
            $admin->assignRole($role);
        }
    }
}