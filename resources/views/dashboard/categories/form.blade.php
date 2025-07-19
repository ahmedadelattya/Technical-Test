@csrf
<div class="mb-4">
    <label class="block mb-2 font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
    <input type="text" name="name" class="w-full border p-2" value="{{ old('name', $category->name ?? '') }}"
        required>
    @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label class="block mb-1">Parent Category</label>
    <select name="parent_id" class="w-full border p-2">
        <option value="">-- No Parent (Main Category) --</option>
        @foreach ($parentCategories as $parentCategory)
            <option value="{{ $parentCategory->id }}"
                {{ old('parent_id', $category->parent_id ?? '') == $parentCategory->id ? 'selected' : '' }}>
                {{ $parentCategory->name }}
            </option>
        @endforeach
    </select>
    @error('parent_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
