<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StorePostRequest;

class PostController extends Controller
{

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function index()
    {
        $posts = Post::with('category')->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        
        // Generate the base slug from the title
        $slug = Str::slug($validatedData['title']);
        
        // Check if the slug exists and append a number if it does
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = Str::slug($validatedData['title']) . '-' . $count;
            $count++;
        }
        
        $validatedData['slug'] = $slug;

        // Handle image upload if present
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts');
            $validatedData['featured_image'] = $path;
        }

        Post::create($validatedData);

        return redirect()->route('posts.index')->with('success', 'Post created successfully');
    }

    public function show(Post $post)
    {
        $post['content'] = Str::markdown($post->content);
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        $validatedData = $request->validated();

        // Update slug if title changed
        if ($post->title !== $validatedData['title']) {
            $slug = Str::slug($validatedData['title']);
            
            // Check if the slug exists and append a number if it does
            $count = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = Str::slug($validatedData['title']) . '-' . $count;
                $count++;
            }
            
            $validatedData['slug'] = $slug;
        }

        // Handle image upload if present
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                Storage::delete('posts/' . $post->featured_image);
            }
            
            $path = $request->file('featured_image')->store('posts');
            $validatedData['featured_image'] = $path;
        }

        $post->update($validatedData);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }
}
