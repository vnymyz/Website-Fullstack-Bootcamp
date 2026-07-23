@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <a href="/posts" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Daftar Post</a>

    <div class="bg-white border rounded-lg p-6 shadow-sm mt-4">
        @if ($post->imageUrl())
            <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full max-h-96 object-cover rounded mb-4">
        @endif

        <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
        <p class="text-xs text-gray-400 mt-1">Ditulis oleh {{ $post->user->name ?? 'Tidak diketahui' }}</p>

        <p class="text-gray-700 mt-4 whitespace-pre-line">{{ $post->body }}</p>

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
@endsection
