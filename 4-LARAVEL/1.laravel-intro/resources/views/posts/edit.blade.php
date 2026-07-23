@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Post</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts/{{ $post->id }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" value="{{ $post->title }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2">{{ $post->body }}</textarea>
        </div>

        {{-- upload image --}}
        @if ($post->imageUrl())
            <div>
                <label class="block text-sm font-medium mb-1">Gambar saat ini</label>
                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-40 rounded border">
            </div>
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Ganti Upload Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>
        {{-- link image --}}
        <div>
            <label class="block text-sm font-medium mb-1">Atau ganti Link Gambar (opsional)</label>
            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                class="w-full border rounded px-3 py-2">
        </div>

        <p class="text-xs text-gray-400">Kosongin dua-duanya kalau gambar lama gak mau diganti.</p>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Update
        </button>
    </form>
@endsection
