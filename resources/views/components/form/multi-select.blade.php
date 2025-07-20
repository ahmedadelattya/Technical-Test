@props(['field', 'formData' => [], 'readOnly' => false])

@php
    // Normalize old values and formData to strings, and filter invalid values
    $validOptionValues = array_column($field['custom']['options'] ?? [], 'value');
    $selectedValues = array_filter(
        array_map('strval', old($field['name'], $formData[$field['name']] ?? [])),
        fn($value) => in_array($value, $validOptionValues),
    );
    $options = array_map(function ($option) {
        return [
            'value' => (string) $option['value'],
            'label' => $option['label'],
        ];
    }, $field['custom']['options'] ?? []);
    $displayLimit = $field['custom']['numberDisplayed'] ?? 3;
@endphp

<div x-data="{
    open: false,
    query: '',
    selected: @js($selectedValues),
    options: @js($options),
    placeholder: '{{ $field['custom']['placeholder'] ?? 'Select options' }}',
    get filteredOptions() {
        if (!this.query) return this.options;
        return this.options.filter(option =>
            option.label.toLowerCase().includes(this.query.toLowerCase())
        );
    },
    toggle(val) {
        if (this.selected.includes(val)) {
            this.selected = this.selected.filter(v => v !== val);
        } else {
            this.selected.push(val);
        }
    },
    toggleAll() {
        if (this.selected.length === this.options.length) {
            this.selected = [];
        } else {
            this.selected = this.options.map(o => o.value);
        }
    },
    clear() {
        this.selected = [];
    },
    clearExtra() {
        this.selected = this.selected.slice(0, {{ $displayLimit }});
    }
}" @click.outside="open = false" class="w-full">
    <label class="block mb-1 font-medium text-gray-700">
        {{ $field['label'] }}
        @if ($field['required'])
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <button type="button" x-on:click="open = !open"
            class="flex w-full p-2 border rounded min-h-[2.5rem] items-center justify-between bg-white"
            :disabled="{{ $readOnly ? 'true' : 'false' }}">
            <template x-if="selected.length > 0">
                <div class="flex flex-wrap gap-2 items-center w-full">
                    <template x-for="(val, idx) in selected.slice(0, {{ $displayLimit }})" :key="val">
                        <span
                            class="bg-black text-white font-medium rounded-full px-3 py-1 text-sm flex items-center gap-2">
                            <span x-text="options.find(o => o.value === val)?.label" class="whitespace-nowrap"></span>
                            <button type="button" @click.stop="toggle(val)">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template x-if="selected.length > {{ $displayLimit }}">
                        <span
                            class="bg-black text-white font-medium rounded-full px-3 py-1 text-sm flex items-center gap-2">
                            +<span x-text="selected.length - {{ $displayLimit }}"></span> more
                            <button type="button" @click.stop="clearExtra">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <button type="button" @click.stop="clear" class="ml-auto">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>


            <template x-if="selected.length === 0">
                <span class="text-gray-400">{{ $field['custom']['placeholder'] ?? 'Select options' }}</span>
            </template>

            <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" x-cloak
            class="absolute z-50 mt-1 w-full bg-white border rounded shadow-md max-h-64 overflow-y-auto">
            <div class="p-2">
                <input type="text" placeholder="Search..." class="w-full px-2 py-1 border rounded mb-2"
                    x-model="query" />
                <div class="flex items-center mb-2 cursor-pointer" @click="toggleAll">
                    <input type="checkbox" class="mr-2"
                        :checked="selected.length === filteredOptions.length && filteredOptions.length > 0">
                    <span>Select All</span>
                </div>
                <template x-for="option in filteredOptions" :key="option.value">
                    <div class="flex items-center cursor-pointer px-2 py-1 hover:bg-gray-100"
                        @click="toggle(option.value)">
                        <input type="checkbox" class="mr-2" :checked="selected.includes(option.value)">
                        <span x-text="option.label"></span>
                    </div>
                </template>

                <div class="flex justify-end items-center border-t mt-2 pt-2 gap-2">
                    <button type="button" @click="clear" class="text-sm text-red-500 hover:underline">Clear</button>
                    <button type="button" @click="open = false"
                        class="text-sm text-blue-500 hover:underline">Close</button>
                </div>
            </div>
        </div>
    </div>

    <template x-for="(value, index) in selected" :key="index">
        <input type="hidden" :name="`{{ $field['name'] }}[]`" :value="value" />
    </template>

    @error($field['name'])
        <span class="text-sm text-red-500">{{ $message }}</span>
    @enderror
</div>
