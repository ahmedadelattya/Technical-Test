@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6 max-w-3xl mx-auto bg-white mt-5">
        <h1 class="text-xl font-bold mb-7">Create User</h1>
        <form action="{{ route('dashboard.users.store') }}" method="POST">
            @include('dashboard.users.form')
            @can(\App\Enums\PermissionEnum::USER_CREATE['name'])
                <div class="text-right">
                    <button type="submit"
                        class="bg-black text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black">
                        Create User
                    </button>
                </div>
            @endcan
        </form>
    </div>
@endsection
