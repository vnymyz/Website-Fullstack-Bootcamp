@extends('layouts.app')

@section('title', 'Tambah Post')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah Post</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/posts" class="space-y-4">
        @csrf

        {{-- ini judul --}}
        <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2">
        </div>

        {{-- ini isi post atau body --}}
        <div>
            <label class="block text-sm font-medium mb-1">Body</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2"></textarea>
        </div>

        {{-- upload image --}}
        <div>
            <label class="block text-sm font-medium mb-1">Upload Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
        </div>

        {{-- link image --}}
        <div>
            <label class="block text-sm font-medium mb-1">Atau isi Link Gambar (opsional)</label>
            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..."
                class="w-full border rounded px-3 py-2">
        </div>

        <p class="text-xs text-gray-400">Isi salah satu aja — upload file ATAU link. Kalau dua-duanya diisi, file upload
            yang dipakai.</p>

        <button type="submit" class="bg-blue-600 text-white 
        px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </form>
@endsection
