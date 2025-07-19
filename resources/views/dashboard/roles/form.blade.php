<div class="max-w-4xl mx-auto p-6 bg-white">

    <form action="{{ isset($role) ? route('dashboard.roles.update', $role) : route('dashboard.roles.store') }}"
        method="POST">
        @csrf
        @if (isset($role))
            @method('PUT')
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <label class="block font-medium text-gray-700 mb-2">Role Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}"
                class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <h1 class="text-3xl font-bold text-center mb-8">Role Permissions</h1>

        <div class="mb-8">
            @foreach ($permissions as $group)
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">{{ ucfirst($group->name) }}</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($group->permissions as $permission)
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    class="sr-only peer"
                                    {{ in_array($permission->name, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }}>
                                <div
                                    class="px-4 py-2 rounded-full border-2 border-gray-300 bg-gray-100 text-gray-700 font-semibold
                                    peer-checked:bg-gray-700 peer-checked:text-white peer-checked:border-gray-700
                                    transition-all duration-200 hover:border-gray-400">
                                    {{ str_replace('_', ':', $permission->name) }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @error('permissions')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            @can(\App\Enums\PermissionEnum::ROLE_UPDATE['name'])
                @if (isset($role))
                    <button type="submit"
                        class="bg-black hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Update Role
                    </button>
                @endif
            @endcan

            @can(\App\Enums\PermissionEnum::ROLE_CREATE['name'])
                @if (!isset($role))
                    <button type="submit"
                        class="bg-black hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Create Role
                    </button>
                @endif
            @endcan
        </div>
    </form>
</div>
