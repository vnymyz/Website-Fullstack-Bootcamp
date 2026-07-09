@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Kelola User</h1>
        <span class="text-sm text-gray-500">{{ $users->count() }} user terdaftar</span>
    </div>

    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="/admin/users/{{ $user->id }}"
                                class="flex justify-end gap-2 items-center">
                                @csrf
                                @method('PATCH')

                                <select name="role"
                                    class="border rounded text-sm pl-2 pr-6 py-1.5 bg-white min-w-[90px]">
                                    <option value="user" @selected($user->role === 'user')>user</option>
                                    <option value="admin" @selected($user->role === 'admin')>admin</option>
                                </select>

                                <button type="submit"
                                    class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
