<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
   public function index()
   {
       $posts = Post::latest()->get();
       return view('posts.index', compact('posts'));
   }

   public function show(Post $post)
   {
       return view('posts.show', compact('post'));
   }

   public function create()
   {
       return view('posts.create');
   }

   public function store(Request $request)
   {
       $validated = $request->validate([
           'title' => 'required|max:255',
           'content' => 'required'
       ]);

       Post::create($validated);

       return redirect()->route('posts.index')->with('success', 'Post created successfully!');
   }

   public function edit(Post $post)
   {
       return view('posts.edit', compact('post'));
   }

   public function update(Request $request, Post $post)
   {
       $validated = $request->validate([
           'title' => 'required|max:255',
           'content' => 'required'
       ]);

       $post->update($validated);

       return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
   }

   public function destroy(Post $post)
   {
       $post->delete();
       
       return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
   }
}
