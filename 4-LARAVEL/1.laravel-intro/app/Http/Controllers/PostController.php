<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    // method untuk menampilkan semua post dan searching post
    public function index(Request $request)
    {
        $posts = Post::when($request->search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->latest()->paginate(6)->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $request->search,
        ]);
    }

    // slug post
    public function show(Post $post)
    {
        return view('posts.show', ['post' => $post]);
    }

    // method untuk buat post atau create post baru
    public function create()
    {
        return view('posts.create');
    }

    // method untuk simpen data atau store
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        }

        unset($validated['image_url']);
        $validated['user_id'] = auth()->id();

        Post::create($validated);

        return redirect('/posts');
    }

    // method untuk edit post
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', ['post' => $post]);
    }

    // method untuk update post
    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $request->image_url;
        } else {
            unset($validated['image']);
        }

        unset($validated['image_url']);

        $post->update($validated);

        return redirect('/posts');
    }

    // method untuk delete post
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect('/posts');
    }
}