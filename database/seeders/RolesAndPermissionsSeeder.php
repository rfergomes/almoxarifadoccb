<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissões do sistema
        $permissions = [
            'view-dashboard',
            'view-movements',
            'create-movements',
            'view-materials',
            'manage-materials',
            'view-beneficiaries',
            'manage-beneficiaries',
            'view-destinations',
            'manage-destinations',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Perfil Consulta (Somente visualização)
        $roleConsulta = Role::firstOrCreate(['name' => 'Consulta']);
        $roleConsulta->syncPermissions([
            'view-dashboard',
            'view-movements',
            'view-materials',
            'view-beneficiaries',
            'view-destinations',
        ]);

        // Perfil Almoxarife (Lança saídas, empréstimos, devoluções e gerencia cadastros operacionais)
        $roleAlmoxarife = Role::firstOrCreate(['name' => 'Almoxarife']);
        $roleAlmoxarife->syncPermissions([
            'view-dashboard',
            'view-movements',
            'create-movements',
            'view-materials',
            'manage-materials',
            'view-beneficiaries',
            'manage-beneficiaries',
            'view-destinations',
            'manage-destinations',
        ]);

        // Perfil Administrador (Gestão total)
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $roleAdmin->syncPermissions($permissions);

        // Usuário Administrador padrão
        $admin = User::firstOrCreate(
            ['email' => 'admin@ccb.org.br'],
            [
                'name' => 'Administrador Central',
                'password' => Hash::make('12345678'),
            ]
        );
        $admin->assignRole($roleAdmin);

        // Usuário Almoxarife padrão
        $almoxarife = User::firstOrCreate(
            ['email' => 'almoxarife@ccb.org.br'],
            [
                'name' => 'Almoxarife Principal',
                'password' => Hash::make('12345678'),
            ]
        );
        $almoxarife->assignRole($roleAlmoxarife);

        // Usuário Consulta padrão
        $consulta = User::firstOrCreate(
            ['email' => 'consulta@ccb.org.br'],
            [
                'name' => 'Usuário Consulta',
                'password' => Hash::make('12345678'),
            ]
        );
        $consulta->assignRole($roleConsulta);
    }
}
