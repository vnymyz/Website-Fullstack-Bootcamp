@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-500">Post Kamu</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['my_posts'] }}</p>
        </div>

        @if (isset($stats['total_posts']))
            <div class="bg-white border rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500">Total Post (Semua User)</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_posts'] }}</p>
            </div>

            <div class="bg-white border rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500">Total User Terdaftar</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
            </div>
        @endif
    </div>

    <div class="mt-6">
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>
@endsection
