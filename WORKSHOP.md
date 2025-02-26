## Preparation Before Workshop

1. Create a fresh Laravel project for your presentation:
   ```bash
   composer create-project --prefer-dist laravel/laravel laravel-presentation
   ```

2. Set up the database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_test
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Create the database:
   ```bash
   mysql -127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS laravel_workshop;"
   ```
## or you can just set it up manually using phpmyadmin

4. Have this completed project ready as a reference to copy code from.

## Presentation Flow

### 1. Creating the Post Model and Migration

1. Run the command to create the model, migration, and controller:
   ```bash
   php artisan make:model Post -mc
   ```

2. Modify the migration file to add title and content columns:
   ```php
   public function up(): void
   {
       Schema::create('posts', function (Blueprint $table) {
           $table->id();
           $table->string('title');
           $table->text('content');
           $table->timestamps();
       });
   }
   ```

3. Run the migration:
   ```bash
   php artisan migrate
   ```

4. Update the Post model to make fields fillable:
   ```php
   protected $fillable = ['title', 'content'];
   ```

### 3. Creating Routes

1. Open `routes/web.php` and add:
   ```php
   use App\Http\Controllers\PostController;
   
   Route::get('/', function () {
       return redirect()->route('posts.index');
   });
   
   Route::resource('posts', PostController::class);
   ```

2. You can check the routes list using the following command
   ```bash
   php artisan route:list
   ```

### 4. Creating the Layout

1. Create `resources/views/layouts/app.blade.php`:
   ```php
   <!DOCTYPE html>
   <html lang="en">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Laravel Workshop</title>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   </head>
   <body>
       <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
           <div class="container">
               <a class="navbar-brand" href="{{ route('posts.index') }}">Laravel Workshop</a>
           </div>
       </nav>

       <div class="container">
           @if(session('success'))
               <div class="alert alert-success">
                   {{ session('success') }}
               </div>
           @endif

           @yield('content')
       </div>

       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   </body>
   </html>
   ```

### 5. Implementing Read Functionality (10 minutes)

1. Add the index method to PostController:
   ```php
   use App\Models\Post;

   public function index()
   {
       $posts = Post::latest()->get();
       return view('posts.index', compact('posts'));
   }
   ```

2. Create `resources/views/posts/index.blade.php`:
   ```php
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
                           <a href="{{ route('posts.show', $post) }}" class="btn btn-info">Read More</a>
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
   ```

3. Add the show method to PostController:
   ```php
   public function show(Post $post)
   {
       return view('posts.show', compact('post'));
   }
   ```

4. Create `resources/views/posts/show.blade.php`:
   ```php
   @extends('layouts.app')

   @section('content')
       <div class="row">
           <div class="col-md-8 offset-md-2">
               <div class="mb-4">
                   <a href="{{ route('posts.index') }}" class="btn btn-secondary">&larr; Back to Posts</a>
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
   ```

5. Run the server and show the empty posts page:
   ```bash
   php artisan serve
   ```

### 6. Implementing Create Functionality (10 minutes)

1. Add the create and store methods to PostController:
   ```php
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
   ```

2. Create `resources/views/posts/create.blade.php`:
   ```php
   @extends('layouts.app')

   @section('content')
       <div class="row">
           <div class="col-md-8 offset-md-2">
               <h1 class="mb-4">Create New Post</h1>

               <form action="{{ route('posts.store') }}" method="POST">
                   @csrf
                   <div class="mb-3">
                       <label for="title" class="form-label">Title</label>
                       <input type="text" class="form-control @error('title') is-invalid @enderror" 
                              id="title" name="title" value="{{ old('title') }}">
                       @error('title')
                           <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <label for="content" class="form-label">Content</label>
                       <textarea class="form-control @error('content') is-invalid @enderror" 
                                 id="content" name="content" rows="5">{{ old('content') }}</textarea>
                       @error('content')
                           <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <button type="submit" class="btn btn-primary">Create Post</button>
                       <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
                   </div>
               </form>
           </div>
       </div>
   @endsection
   ```

### 7. Implementing Update Functionality (10 minutes)

1. Add the edit and update methods to PostController:
   ```php
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
   ```

2. Create `resources/views/posts/edit.blade.php`:
   ```php
   @extends('layouts.app')

   @section('content')
       <div class="row">
           <div class="col-md-8 offset-md-2">
               <h1 class="mb-4">Edit Post</h1>

               <form action="{{ route('posts.update', $post) }}" method="POST">
                   @csrf
                   @method('PUT')
                   
                   <div class="mb-3">
                       <label for="title" class="form-label">Title</label>
                       <input type="text" class="form-control @error('title') is-invalid @enderror" 
                              id="title" name="title" value="{{ old('title', $post->title) }}">
                       @error('title')
                           <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <label for="content" class="form-label">Content</label>
                       <textarea class="form-control @error('content') is-invalid @enderror" 
                                 id="content" name="content" rows="5">{{ old('content', $post->content) }}</textarea>
                       @error('content')
                           <div class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>

                   <div class="mb-3">
                       <button type="submit" class="btn btn-primary">Update Post</button>
                       <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Cancel</a>
                   </div>
               </form>
           </div>
       </div>
   @endsection
   ```

3. Update the show view to add an edit button:
   ```php
   <div class="mb-4 d-flex justify-content-between">
       <a href="{{ route('posts.index') }}" class="btn btn-secondary">&larr; Back to Posts</a>
       <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">Edit</a>
   </div>
   ```

### 8. Implementing Delete Functionality (5 minutes)

1. Add the destroy method to PostController:
   ```php
   public function destroy(Post $post)
   {
       $post->delete();
       
       return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
   }
   ```

2. Update the show view to add a delete button:
   ```php
   <div class="mb-4 d-flex justify-content-between">
       <a href="{{ route('posts.index') }}" class="btn btn-secondary">&larr; Back to Posts</a>
       <div>
           <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">Edit</a>
           <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
               @csrf
               @method('DELETE')
               <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
           </form>
       </div>
   </div>
   ```

3. Update the index view to add edit and delete buttons:
   ```php
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
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>
        </div>
    </div>
   ```
