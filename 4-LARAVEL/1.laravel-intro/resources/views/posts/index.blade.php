@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Post</h1>
        <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Post
        </a>
    </div>

    <div class="space-y-4">
        @foreach ($posts as $post)
            <div class="bg-white border rounded-lg p-4 shadow-sm flex gap-4">
                @if ($post->imageUrl())
                    <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}"
                        class="w-24 h-24 object-cover rounded shrink-0">
                @endif

                <div class="flex-1">
                    <h2 class="font-semibold text-lg">
                        <a href="/posts/{{ $post->slug }}" class="hover:underline hover:text-blue-600">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="text-gray-600">{{ $post->body }}</p>
                    <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

                    @if ($post->user_id === auth()->id() || auth()->user()->role === 'admin')
                        <div class="mt-2 flex gap-2">
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
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection
