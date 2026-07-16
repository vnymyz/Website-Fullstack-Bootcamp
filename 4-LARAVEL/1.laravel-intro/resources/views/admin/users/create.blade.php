@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah User</h1>

    @if ($errors->any())
        <ul class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/admin/users" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Simpan
        </button>
    </form>
@endsection
