<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => 'P@ssw0rd',
            'email_verified_at' => Carbon::now(),
        ]);
        $user->assignRole('admin');
        User::factory(10)->create();
    }
}
