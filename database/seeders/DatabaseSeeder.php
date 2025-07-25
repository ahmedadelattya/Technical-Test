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
        if (env('APP_ENV') == 'local') {
            $this->call([
                PermissionSeeder::class,
                RoleSeeder::class,
                StatusSeeder::class,
                UserSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
                OrderSeeder::class,
            ]);
        } else
            $this->call([
                PermissionSeeder::class,
                RoleSeeder::class,
                StatusSeeder::class,
                UserSeeder::class,
            ]);
    }
}
