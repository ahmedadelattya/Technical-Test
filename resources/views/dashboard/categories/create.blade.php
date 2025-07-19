@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6 max-w-3xl mx-auto bg-white mt-5">
        <h1 class="text-xl font-bold mb-7">Create Category</h1>

        <form action="{{ route('dashboard.categories.store') }}" method="POST">
            @include('dashboard.categories.form')
            @can(\App\Enums\PermissionEnum::CATEGORY_CREATE['name'])
                <div class="text-right">
                    <button type="submit"
                        class="bg-black text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black">
                        Create Category
                    </button>
                </div>
            @endcan
        </form>
    </div>
@endsection
