<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Basic roles
        DB::table('roles')->insertOrIgnore([
            ['name' => 'admin', 'guard_name' => 'web', 'description' => 'System administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lecturer', 'guard_name' => 'web', 'description' => 'Dosen / Pengajar', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Basic permissions
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_lecturers',
            'manage_subjects',
            'manage_classes',
            'manage_agendas',
            'manage_files',
            'view_agenda',
            'upload_files',
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $perm,
                'guard_name' => 'web',
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign broad permissions to admin
        $admin = DB::table('roles')->where('name', 'admin')->first();
        $allPerms = DB::table('permissions')->pluck('id')->toArray();

        if ($admin) {
            foreach ($allPerms as $pid) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $admin->id,
                    'permission_id' => $pid,
                ]);
            }
        }

        // Give lecturers a subset of permissions
        $lecturer = DB::table('roles')->where('name', 'lecturer')->first();
        $lecturerPerms = DB::table('permissions')->whereIn('name', ['view_dashboard','view_agenda','upload_files','manage_files'])->pluck('id')->toArray();

        if ($lecturer) {
            foreach ($lecturerPerms as $pid) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $lecturer->id,
                    'permission_id' => $pid,
                ]);
            }
        }
    }
}
