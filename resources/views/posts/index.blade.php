@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Posts</h1>
        <a href="{{ route('posts.create') }}" class="btn btn-primary">Create New Post</a>
    </div>

    <div class="row">
        @forelse($posts as $post)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-info">Read More</a>
                            <div>
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">No posts found. Create your first post!</p>
            </div>
        @endforelse
    </div>
@endsection