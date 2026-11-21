<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definir Permisos (los que usaremos en Filament)
        $permisos = [
            'ver_pacientes', 'crear_pacientes', 'editar_pacientes',
            'ver_atenciones', 'crear_atenciones', 'editar_atenciones',
            'ver_catalogos', 'editar_catalogos',
            'ver_indicadores',
            'ver_auditoria',
            'gestionar_usuarios'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 2. Crear Roles y asignarles permisos
        $admin = Role::firstOrCreate(['name' => 'administrador']);
        $admin->syncPermissions(Permission::all()); // El admin tiene todos los permisos

        $admision = Role::firstOrCreate(['name' => 'admision']);
        $admision->syncPermissions([
            'ver_pacientes', 'crear_pacientes', 'editar_pacientes',
            'ver_atenciones', 'crear_atenciones'
        ]);

        $medico = Role::firstOrCreate(['name' => 'medico']);
        $medico->syncPermissions([
            'ver_pacientes',
            'ver_atenciones', 'editar_atenciones'
        ]);

        $epidemiologia = Role::firstOrCreate(['name' => 'epidemiologia']);
        $epidemiologia->syncPermissions([
            'ver_pacientes',
            'ver_atenciones',
            'ver_indicadores',
            'ver_catalogos'
        ]);
    }
}