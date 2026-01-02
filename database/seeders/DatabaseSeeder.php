<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Create a test user
        $test = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Give the seeded test user the admin role if it exists so it can access admin routes.
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $test->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
