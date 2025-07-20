@csrf
<div class="space-y-4" x-data="{ name: '{{ old('name', $product->name ?? '') }}', slug: '{{ old('slug', $product->slug ?? '') }}' }" x-init="$watch('name', value => slug = value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, ''))">
    {{-- Name + Slug --}}
    <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[250px]">
            <label class="block mb-1 font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" class="w-full border p-2" x-model="name" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex-1 min-w-[250px]">
            <label class="block mb-1 font-medium text-gray-700">Slug <span class="text-red-500">*</span></label>
            <input type="text" name="slug" class="w-full border p-2" x-model="slug" required>
            @error('slug')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Description --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">Description</label>
        <textarea name="description" class="w-full border p-2" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Meta Key + Meta Description --}}
    <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[250px]">
            <label class="block mb-1 font-medium text-gray-700">Meta Key</label>
            <input type="text" name="meta_key" class="w-full border p-2"
                value="{{ old('meta_key', $product->meta_key ?? '') }}">
            @error('meta_key')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex-1 min-w-[250px]">
            <label class="block mb-1 font-medium text-gray-700">Meta Description</label>
            <input type="text" name="meta_description" class="w-full border p-2"
                value="{{ old('meta_description', $product->meta_description ?? '') }}">
            @error('meta_description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Price + Stock Quantity --}}
    <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[250px]">
            <label class="block mb-1 font-medium text-gray-700">Price <span class="text-red-500">*</span></label>
            <input type="number" name="price" class="w-full border p-2"
                value="{{ old('price', $product->price ?? '') }}" required>
            @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex-1 min-w-[250px]">
            <label class="block mb-1 font-medium text-gray-700">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="w-full border p-2"
                value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}">
            @error('stock_quantity')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Categories --}}
    <div>
        <x-form.multi-select :field="[
            'name' => 'categories',
            'label' => 'Categories',
            'required' => true,
            'custom' => [
                'placeholder' => 'Select categories',
                'numberDisplayed' => 3,
                'options' => $categories
                    ->map(
                        fn($category) => [
                            'value' => $category->id,
                            'label' => $category->name,
                        ],
                    )
                    ->toArray(),
            ],
        ]" :form-data="[
            'categories' => old('categories', isset($product) ? $product->categories->pluck('id')->toArray() : []),
        ]" />
    </div>

    {{-- Featured Image --}}
    <div>
        <x-form.media-uploader name="image" label="Featured Image" :required="true" accept="image/*"
            :existing-files="isset($product) && $product->image ? [$product->image] : []" placeholder="Upload featured image" class-name="w-full" />
    </div>
</div>
