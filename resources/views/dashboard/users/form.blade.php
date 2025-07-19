@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Name --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200"
            placeholder="Enter Name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200"
            placeholder="Enter your email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div x-data="{ show: false }" class="relative">
        <label class="block mb-1 font-medium text-gray-700">Password</label>
        <input :type="show ? 'text' : 'password'" name="password"
            class="w-full border-gray-300 rounded p-2 pr-10 focus:ring focus:ring-blue-200" placeholder="Password"
            {{ isset($user) ? '' : 'required' }}>
        <button type="button" @click="show = !show" class="absolute right-2 top-9 text-gray-500 focus:outline-none">
            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
        @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div x-data="{ show: false }" class="relative">
        <label class="block mb-1 font-medium text-gray-700">Confirm Password</label>
        <input :type="show ? 'text' : 'password'" name="password_confirmation"
            class="w-full border-gray-300 rounded p-2 pr-10 focus:ring focus:ring-blue-200" placeholder="Password">
        <button type="button" @click="show = !show" class="absolute right-2 top-9 text-gray-500 focus:outline-none">
            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
        @error('password_confirmation')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>


    {{-- Role --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">Role</label>
        <select name="role_id" class="w-full border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
            <option value="">Select Role</option>
            @foreach ($roles as $id => $name)
                <option value="{{ $id }}" {{ old('role_id', $userRoleId ?? '') == $id ? 'selected' : '' }}>
                    {{ ucfirst($name) }}
                </option>
            @endforeach
        </select>
        @error('role_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
