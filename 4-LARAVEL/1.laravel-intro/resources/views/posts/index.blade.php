@extends('layouts.app')

@section('title', 'Daftar Post')

@section('container', 'max-w-6xl')

@section('content')
    {{-- tambah post --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    {{-- search --}}
    <form method="GET" action="/posts" class="mb-6 flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul post..."
            class="flex-1 border rounded px-3 py-2">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900">
            Cari
        </button>
        @if ($search)
            <a href="/posts" class="text-sm text-gray-500 px-3 py-2 hover:underline">Reset</a>
        @endif
    </form>

    <div class="flex justify-end mb-6">
        {{ $posts->links() }}
    </div>

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
                    <h2 class="font-semibold text-lg mb-1">
                        <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <p class="text-gray-600 text-sm line-clamp-3 flex-1">{{ $post->body }}</p>

                    <p class="text-xs text-gray-400 mt-3">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

                    @can('update', $post)
                        <div class="mt-3 flex gap-2">
                            <a href="/posts/{{ $post->id }}/edit"
                                class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">
                                Edit
                            </a>

                            <form method="POST" action="/posts/{{ $post->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                    class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm col-span-full">Gak ada post yang cocok dengan pencarian "{{ $search }}".
            </p>
        @endforelse
    </div>

    <div class="flex justify-end mt-6">
        {{ $posts->links() }}
    </div>
@endsection
