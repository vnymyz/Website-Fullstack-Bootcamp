<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // get all users
    public function index()
    {
        $users = User::all();

        return view('admin.users.index', ['users' => $users]);
    }

    // create new user
    public function create()
    {
        return view('admin.users.create');
    }

    // store new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect('/admin/users');
    }

    // update user role
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect('/admin/users');
    }

    // destroy user
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect('/admin/users')->with('error', 'Gak bisa hapus akun sendiri.');
        }

        $user->delete();

        return redirect('/admin/users');
    }
}