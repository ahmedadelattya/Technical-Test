<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reflection = new \ReflectionClass(PermissionEnum::class);
        $permissions = $reflection->getConstants();
        $admin_role = Role::createOrFirst(['id' => 1, 'name' => 'admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        foreach ($permissions as $key => $permissionData) {
            if (is_array($permissionData) && isset($permissionData['group_id'])) {
                $admin_role->givePermissionTo($permissionData['name']);
                if (!in_array($permissionData['group_id'], [1, 2])) {
                    $employeeRole->givePermissionTo($permissionData['name']);
                }
            }
        }
    }
}
