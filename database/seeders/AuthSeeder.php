<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create default Admin user ────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@dubatt.com'],
            [
                'name'       => 'System Admin',
                'username'   => 'admin',
                'password'   => Hash::make('Admin@1234'),  // CHANGE THIS
                'role'       => 'admin',
                'is_active'  => true,
                'department' => 'IT',
            ]
        );

        // ── Create default Management user ────────────────────────
        User::updateOrCreate(
            ['email' => 'manager@dubatt.com'],
            [
                'name'       => 'Plant Manager',
                'username'   => 'manager',
                'password'   => Hash::make('Manager@1234'),  // CHANGE THIS
                'role'       => 'management',
                'is_active'  => true,
                'department' => 'Operations',
                'created_by' => $admin->id,
            ]
        );

        // ── Seed MES Modules ─────────────────────────────────────
        $modules = [
            ['name' => 'Receiving',                              'slug' => 'receiving',    'sort_order' => 1],
            ['name' => 'Acid Testing',                           'slug' => 'acid-testing', 'sort_order' => 2],
            ['name' => 'Battery Breaking & Separation (BBSU)',   'slug' => 'bbsu',         'sort_order' => 3],
            ['name' => 'Smelting',                               'slug' => 'smelting',     'sort_order' => 4],
            ['name' => 'Refining',                               'slug' => 'refining',     'sort_order' => 5],
            ['name' => 'Dashboards & Reports',                   'slug' => 'dashboards',   'sort_order' => 6],
            ['name' => 'Stock Balance Report',                   'slug' => 'stock-balance','sort_order' => 7],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['slug' => $module['slug']],
                array_merge($module, ['is_active' => true])
            );
        }

        $this->command->info('✅ Admin user: admin@dubatt.com / Admin@1234');
        $this->command->info('✅ Manager user: manager@dubatt.com / Manager@1234');
        $this->command->info('✅ All 7 MES modules seeded.');
        $this->command->warn('⚠️  Please change default passwords immediately!');
    }
}
