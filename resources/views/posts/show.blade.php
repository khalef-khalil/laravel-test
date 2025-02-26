@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="mb-4 d-flex justify-content-between">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">&larr; Back to Posts</a>
                <div>
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h1 class="card-title">{{ $post->title }}</h1>
                    <p class="text-muted">Posted {{ $post->created_at->diffForHumans() }}</p>
                    <div class="card-text mt-4">
                        {{ $post->content }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection