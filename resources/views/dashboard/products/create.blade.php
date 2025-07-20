@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6 max-w-3xl mx-auto bg-white mt-5">

        <h1 class="text-xl font-bold mb-7">Create Product</h1>


        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('dashboard.products.store') }}" method="POST" enctype="multipart/form-data">
            @include('dashboard.products.form')
            @can(\App\Enums\PermissionEnum::PRODUCT_CREATE['name'])
                <div class="text-right">
                    <button type="submit"
                        class="bg-black text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black">
                        Create Product
                    </button>
                </div>
            @endcan
        </form>
    </div>
@endsection
