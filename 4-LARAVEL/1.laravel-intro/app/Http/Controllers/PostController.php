<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // method untuk menampilkan semua post
    public function index()
    {
        $posts = Post::all();

        return view('posts.index', ['posts' => $posts]);
    }

    // method untuk buat post atau create post baru
    public function create()
    {
        return view('posts.create');
    }

    // method untuk simpen data atau store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect('/posts');
    }

    // method untuk edit post
    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('posts.edit', ['post' => $post]);
    }

    // method untuk update post
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update($validated);

        return redirect('/posts');
    }

    // method untuk delete post
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $post->delete();

        return redirect('/posts');
    }
}