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
        foreach ($permissions as $key => $permissionData) {
            if (is_array($permissionData) && isset($permissionData['group_id'])) {
                $admin_role->givePermissionTo($permissionData['name']);
            }
        }
    }
}
