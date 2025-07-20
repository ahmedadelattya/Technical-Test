@props([
    'name' => 'image',
    'label' => 'Upload Image',
    'required' => false,
    'readOnly' => false,
    'placeholder' => null,
    'accept' => 'image/*',
    'existingFiles' => [],
    'className' => '',
])

@php
    $inputId = 'media-uploader-' . Str::random(8);
    $placeholderText = $placeholder ?? "Upload {$label}";
    $acceptTypes = $accept === 'image/*' ? 'image/*' : $accept;
    $hasError = $errors->has($name);
    $errorMessage = $hasError ? $errors->first($name) : '';
    $existingUrl = null;

    if (!empty($existingFiles)) {
        $file = $existingFiles[0];
        $existingUrl = isset($file->full_path) ? $file->full_path : (isset($file->path) ? asset($file->path) : null);
    }
@endphp

<div x-data="imageUploader('{{ $inputId }}', '{{ $existingUrl }}')" class="{{ $className }}">
    <label for="{{ $inputId }}" class=" leading-none capitalize mb-3 inline-block font-medium text-gray-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input type="file" id="{{ $inputId }}" name="{{ $name }}" accept="{{ $acceptTypes }}" class="hidden"
        x-ref="fileInput" :disabled="readOnly" @change="previewImage">

    <!-- Dropzone -->
    <div class="border-2 border-dashed rounded w-full h-48 flex items-center justify-center text-center transition cursor-pointer bg-white"
        :class="{
            'border-red-500': hasError,
            'border-gray-300 hover:border-gray-400': !hasError,
            'cursor-not-allowed opacity-50': readOnly
        }"
        @click.prevent="!readOnly && $refs.fileInput.click()" @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false" @drop.prevent="dropFile($event)"
        x-bind:class="{ 'border-blue-400 bg-blue-50': dragging }">
        <div class="flex flex-col items-center justify-center gap-2 p-4">
            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                </path>
            </svg>
            <p class="text-sm font-medium text-black">
                {{ $placeholderText }}<br>
                <span class="text-xs text-gray-400">Click to browse or drag and drop</span>
            </p>
        </div>
    </div>

    @if ($hasError)
        <p class="text-sm  text-red-500 mt-1">{{ $errorMessage }}</p>
    @endif

    <!-- Preview -->
    <div class="mt-3">
        <template x-if="imageUrl">
            <img :src="imageUrl" alt="Preview" class="h-20 w-20 object-cover rounded border" />
        </template>
    </div>
</div>

<script>
    function imageUploader(inputId, existingUrl = null) {
        return {
            imageUrl: existingUrl || null,
            dragging: false,
            hasError: {{ $hasError ? 'true' : 'false' }},
            readOnly: {{ $readOnly ? 'true' : 'false' }},

            previewImage(event) {
                const file = this.$refs.fileInput.files[0];
                this.readFile(file);
            },

            dropFile(event) {
                if (this.readOnly) return;
                this.dragging = false;
                const file = event.dataTransfer.files[0];
                this.$refs.fileInput.files = event.dataTransfer.files;
                this.readFile(file);
            },

            readFile(file) {
                if (!file || !file.type.startsWith('image/')) {
                    alert('Only image files are allowed.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imageUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            },
        };
    }
</script>
