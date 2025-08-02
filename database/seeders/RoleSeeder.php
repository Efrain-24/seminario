<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Acceso completo al sistema',
                'permissions' => [
                    'user_create', 'user_read', 'user_update', 'user_delete',
                    'role_create', 'role_read', 'role_update', 'role_delete',
                    'dashboard_access', 'reports_access'
                ],
                'is_active' => true
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Gestión de usuarios y acceso limitado',
                'permissions' => [
                    'user_create', 'user_read', 'user_update',
                    'role_read', 'dashboard_access', 'reports_access'
                ],
                'is_active' => true
            ],
            [
                'name' => 'usuario',
                'display_name' => 'Usuario',
                'description' => 'Acceso básico al sistema',
                'permissions' => [
                    'dashboard_access'
                ],
                'is_active' => true
            ]
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
