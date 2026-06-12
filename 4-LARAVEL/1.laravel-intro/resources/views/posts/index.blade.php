@extends('layouts.app')

@section('title', 'Daftar Post')

@section('content')
    <h1>Daftar Post</h1>

    <ul>
        @foreach ($posts as $post)
            <li>
                <strong>{{ $post->title }}</strong>
                <p>{{ $post->body }}</p>
            </li>
        @endforeach
    </ul>
@endsection
