@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <div class="text-center py-12">
        <h1 class="text-3xl font-bold text-gray-800">Selamat Datang di Laravel Intro</h1>
        <p class="text-gray-500 mt-2">Website latihan belajar Laravel — CRUD Post, Auth, Role, dan lainnya.</p>
    </div>

    <h2 class="text-xl font-semibold mb-4">Post Terbaru</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($posts as $post)
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden flex flex-col">
                @if ($post->imageUrl())
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                        Tanpa gambar
                    </div>
                @endif

                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-semibold text-lg mb-1">{{ $post->title }}</h3>
                    <p class="text-gray-600 text-sm line-clamp-3 flex-1">{{ $post->body }}</p>
                    <p class="text-xs text-gray-400 mt-3">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm col-span-full">Belum ada post.</p>
        @endforelse
    </div>
@endsection
