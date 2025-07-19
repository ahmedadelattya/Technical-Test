<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $roles = $this->PaginateAllRoles([
            'search' => $search,
            'per_page' => 10
        ]);
        if ($request->ajax()) {
            return view('dashboard.roles.partials.table', compact('roles'))->render();
        }

        return view('dashboard.roles.index', compact('roles', 'search'));
    }

    public function create()
    {
        $permissions = PermissionGroup::with('permissions')->get();
        return view('dashboard.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->validated()['name'],
            'guard_name' => 'web',
        ]);
        $submittedPermissions = $request->permissions ?? [];
        $role->syncPermissions($submittedPermissions);

        return redirect()->route('dashboard.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = PermissionGroup::with('permissions')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('dashboard.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if ($role->id === 1) {
            return redirect()->route('dashboard.roles.edit', $role->id)
                ->with('error', 'Cannot update the super admin role.');
        }
        $role->update(['name' => $request->validated()['name']]);
        $submittedPermissions = $request->permissions ?? [];
        $role->syncPermissions($submittedPermissions);
        return redirect()->route('dashboard.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->id === 1) {
            return redirect()->route('dashboard.roles.index')->with('error', 'Cannot delete the super admin role.');
        }
        $role->delete();
        return redirect()->route('dashboard.roles.index')->with('success', 'Role deleted.');
    }

    private function PaginateAllRoles($data = [])
    {
        $perPage = $data['per_page'] ?? 10;
        $query = Role::withCount('permissions');

        if (isset($data['search']) && !empty($data['search'])) {
            $statement = $data['search'];
            $query->where('name', 'like', '%' . $statement . '%');
        }

        return $query->latest('id')->paginate($perPage);
    }
}
