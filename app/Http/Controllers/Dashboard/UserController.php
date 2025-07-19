<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = $this->PaginateAllUsers([
            'search' => $search,
            'per_page' => 10
        ]);

        if ($request->ajax()) {
            return view('dashboard.users.partials.table', compact('users'))->render();
        }

        return view('dashboard.users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id'); // id => name
        return view('dashboard.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);
        if (!empty($validated['role_id'])) {
            $role = Role::findById($validated['role_id']);
            $user->syncRoles([$role->name]);
        }
        return redirect()->route('dashboard.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id');
        $userRoleId = $user->roles->first()?->id;

        return view('dashboard.users.edit', compact('user', 'roles', 'userRoleId'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('dashboard.users.edit', $user->id)
                ->with('error', 'Cannot update the super admin.');
        }
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        // Update user data
        $user->update($data);
        // Sync roles if provided
        if (isset($data['role_id'])) {
            $role = Role::findById($data['role_id']);
            $user->syncRoles([$role->name]);
        }
        return redirect()->route('dashboard.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('dashboard.users.index')->with('error', 'Cannot delete the super admin user.');
        }
        $user->forceDelete();
        return redirect()->route('dashboard.users.index')->with('success', 'User deleted.');
    }

    private function PaginateAllUsers($data = [])
    {
        $perPage = $data['per_page'] ?? 10;
        $query = User::where('id', '!=', Auth::id());

        if (isset($data['search']) && !empty($data['search'])) {
            $statement = $data['search'];
            $query->where(function ($q) use ($statement) {
                $q->where('name', 'like', '%' . $statement . '%')
                    ->orWhere('email', 'like', '%' . $statement . '%');
            });
        }

        return $query->latest()->paginate($perPage);
    }
}
