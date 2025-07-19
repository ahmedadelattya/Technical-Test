@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6 max-w-3xl mx-auto bg-white mt-5">
        <h1 class="text-xl font-bold mb-7">Edit Category</h1>

        <form action="{{ route('dashboard.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('dashboard.categories.form')
            @can(\App\Enums\PermissionEnum::CATEGORY_UPDATE['name'])
                <div class="text-right">
                    <button type="submit"
                        class="bg-black text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black">
                        Update Category
                    </button>
                </div>
            @endcan
        </form>
    </div>
@endsection
