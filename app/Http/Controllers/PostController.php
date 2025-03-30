<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePostRequest;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

        // Handle image upload with nested folder structure
        if ($request->hasFile('featured_image')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('featured_image'));
            
            // Get original extension
            $extension = $request->file('featured_image')->getClientOriginalExtension();
            
            // Generate featured image (1170px wide, maintaining aspect ratio)
            $featuredData = $image->scaleDown(width: 1170)->encodeByExtension($extension)->toString();
            $featuredPath = 'posts/' . $slug . '/' . $slug . '-featured.' . $extension;
            Storage::disk('public')->put($featuredPath, $featuredData);
            
            // Generate thumbnail (128x128)
            $thumbnailData = $image->cover(128, 128)->encodeByExtension($extension)->toString();
            $thumbnailPath = 'posts/' . $slug . '/' . $slug . '-thumb.' . $extension;
            Storage::disk('public')->put($thumbnailPath, $thumbnailData);           
            
            // Store the featured image path in the database
            $validatedData['featured_image'] = $featuredPath;
        }


        // Default image upload
        // if ($request->hasFile('featured_image')) {
        //     $path = $request->file('featured_image')->store('posts');
        //     $validatedData['featured_image'] = $path;
        // }

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
            // Delete old images if they exist
            if ($post->featured_image) {
                // Get the extension from the current featured image path
                $extension = pathinfo($post->featured_image, PATHINFO_EXTENSION);
                
                // Delete both featured and thumbnail images
                Storage::disk('public')->delete($post->featured_image);
                Storage::disk('public')->delete(str_replace('-featured.' . $extension, '-thumb.' . $extension, $post->featured_image));
                
                // Delete the old folder
                Storage::disk('public')->deleteDirectory('posts/' . $post->slug);
            }
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('featured_image'));

            // Use the new slug if title changed, otherwise use the current post's slug
            $currentSlug = $validatedData['slug'] ?? $post->slug;

            $extension = $request->file('featured_image')->getClientOriginalExtension();

            // Generate featured image (1170px wide, maintaining aspect ratio)
            $featuredData = $image->scaleDown(width: 1170)->encodeByExtension($extension)->toString();
            $featuredPath = 'posts/' . $currentSlug . '/' . $currentSlug . '-featured.' . $extension;
            Storage::disk('public')->put($featuredPath, $featuredData);
            
            // Generate thumbnail (128x128)
            $thumbnailData = $image->cover(128, 128)->encodeByExtension($extension)->toString();
            $thumbnailPath = 'posts/' . $currentSlug . '/' . $currentSlug . '-thumb.' . $extension;
            Storage::disk('public')->put($thumbnailPath, $thumbnailData);
            
            $validatedData['featured_image'] = $featuredPath;
        }

        $post->update($validatedData);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully');
    }

    public function destroy(Post $post)
    {
        // Delete old images if they exist
        if ($post->featured_image) {
            // Get the extension from the current featured image path
            $extension = pathinfo($post->featured_image, PATHINFO_EXTENSION);
            
            // Delete both featured and thumbnail images
            Storage::disk('public')->delete($post->featured_image);
            Storage::disk('public')->delete(str_replace('-featured.' . $extension, '-thumb.' . $extension, $post->featured_image));
            
            // Delete the folder
            Storage::disk('public')->deleteDirectory('posts/' . $post->slug);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }
}
