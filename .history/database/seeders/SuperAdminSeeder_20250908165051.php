<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Super Admin için özel tenant oluştur (eğer yoksa)
        $superAdminTenant = Tenant::firstOrCreate([
            'username' => 'fibermedia.com',
            'firma_slug' => 'superadmin'
        ], [
            'firma_adi' => 'Super Admin Panel',
             'name'       => 'Super Admin', // ← zorunlu alan eklendi
            'eposta' => 'superadmin@fibermedia.com',
            //'tel1' => '0000000000',
            'status' => 1,
            //'musteriTipi' => 2, // Kurumsal
            'adres' => 'Super Admin Address',
            'il' => 1,
            'ilce' => 1
        ]);

        // Super Admin rolü oluştur
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Super Admin için gerekli izinleri oluştur
        $permissions = [
            'access-all-tenants',
            'manage-all-users', 
            'impersonate-any-user',
            'view-system-stats',
            'manage-system-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin rolüne tüm izinleri ver
        $superAdminRole->syncPermissions($permissions);

        // Super Admin kullanıcısı oluştur
        $superAdmin = User::firstOrCreate([
            'username' => 'fibermedia'
        ], [
            'name' => 'Super Administrator',
            'password' => Hash::make('Fiber155.'),
            'eposta' => 'superadmin@fibermedia.com',
            'tenant_id' => $superAdminTenant->id,
            'status' => 1,
            'il' => 1,
            'ilce' => 1,
            //'tel1' => '0000000000'
        ]);

        // Super Admin rolünü kullanıcıya ata
        $superAdmin->assignRole($superAdminRole);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Username: fibermedia');
        $this->command->info('Password: Fiber155.');
        $this->command->info('Email: superadmin@fibermedia.com');
    }
}