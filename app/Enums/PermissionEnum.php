<?php

namespace App\Enums;

enum PermissionEnum
{
    const GROUPS = [
        ['id' => 1, 'name' => 'users'],
        ['id' => 2, 'name' => 'roles'],
    ];
    // User Permissions
    const USER_CREATE = [
        'group_id' => 1,
        'guard_name' => 'web',
        'name' => 'user_create'
    ];
    const USER_UPDATE = [
        'group_id' => 1,
        'guard_name' => 'web',
        'name' => 'user_update'
    ];
    const USER_READ = [
        'group_id' => 1,
        'guard_name' => 'web',
        'name' => 'user_read'
    ];
    const USER_DELETE = [
        'group_id' => 1,
        'guard_name' => 'web',
        'name' => 'user_delete'
    ];

    // Role Permissions
    const ROLE_CREATE = [
        'group_id' => 2,
        'guard_name' => 'web',
        'name' => 'role_create'
    ];
    const ROLE_UPDATE = [
        'group_id' => 2,
        'guard_name' => 'web',
        'name' => 'role_update'
    ];
    const ROLE_READ = [
        'group_id' => 2,
        'guard_name' => 'web',
        'name' => 'role_read'
    ];
    const ROLE_DELETE = [
        'group_id' => 2,
        'guard_name' => 'web',
        'name' => 'role_delete'
    ];
}
