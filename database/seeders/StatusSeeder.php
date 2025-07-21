<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'shipped', 'delivered', 'cancelled'];

        foreach ($statuses as $status) {
            Status::create(['name' => $status]);
        }
    }
}
