<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Seed Permission Groups
        foreach (PermissionEnum::GROUPS as $groupData) {
            PermissionGroup::updateOrCreate($groupData);
        }

        // Use reflection to dynamically get all constants from PermissionEnum
        $reflection = new \ReflectionClass(PermissionEnum::class);
        $permissions = $reflection->getConstants();

        // Filter and insert permissions
        foreach ($permissions as $permissionData) {
            if (is_array($permissionData) && isset($permissionData['group_id'])) {
                Permission::updateOrCreate(
                    $permissionData
                );
            }
        }
    }
}
