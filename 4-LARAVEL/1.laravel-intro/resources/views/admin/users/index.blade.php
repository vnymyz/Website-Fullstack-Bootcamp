@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Kelola User</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-500">{{ $users->count() }} user terdaftar</span>
            <a href="/admin/users/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Tambah User
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="bg-red-50 text-red-600 text-sm rounded p-3 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-4 py-3 w-12">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 break-words">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500 break-words">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap justify-end gap-2 items-center">
                                <form method="POST" action="/admin/users/{{ $user->id }}"
                                    class="flex gap-2 items-center shrink-0">
                                    @csrf
                                    @method('PATCH')

                                    <select name="role"
                                        class="border rounded text-sm pl-2 pr-6 py-1.5 bg-white min-w-[90px]">
                                        <option value="user" @selected($user->role === 'user')>user</option>
                                        <option value="admin" @selected($user->role === 'admin')>admin</option>
                                    </select>

                                    <button type="submit"
                                        class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 shrink-0">
                                        Simpan
                                    </button>
                                </form>

                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="/admin/users/{{ $user->id }}" class="shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin hapus user ini? Semua post miliknya ikut kehapus.')"
                                            class="text-sm bg-red-100 text-red-700 px-3 py-1.5 rounded hover:bg-red-200 shrink-0">
                                            Hapus
                                        </button>
                                    </form>
                                @else
                                    <span
                                        class="text-xs text-gray-400 italic px-3 py-1.5 shrink-0 whitespace-nowrap">
                                        Akun kamu
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
