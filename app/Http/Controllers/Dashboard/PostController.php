<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Jobs\SendNewPostEmail;
use App\Mail\PostCreated;
use App\Models\Category;
use App\Models\Post;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['categories', 'media'])->latest()->paginate(10);

        return view('dashboard.posts.index', compact('posts'));
    }

    public function create(Post $post)
    {
        $categories = Category::all();

        return view('dashboard.posts.create', compact('categories', 'post'));
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
            $slug = Str::slug($validatedData['title']).'-'.$count;
            $count++;
        }

        $validatedData['slug'] = $slug;

        // Extract categories before creating the post
        $categories = $validatedData['categories'];
        unset($validatedData['categories']);

        // dd($request->all());

        $temporaryFile = TemporaryFile::where('folder', $request->image)->first();

        if ($temporaryFile) {
            $post = Post::create($validatedData);

            // Sync categories to the post
            $post->categories()->sync($categories);

            // Spatie Media Library
            $post
                ->addMedia(storage_path('app/public/posts/tmp/'.$request->image.'/'.$temporaryFile->filename))
                ->toMediaCollection('posts', 'posts');

            // Delete the temporary file record
            Storage::deleteDirectory('posts/tmp/'.$request->image);
            $temporaryFile->delete();

        } else {
            $post = Post::create($validatedData);
            $post->categories()->sync($categories);
        }

        Mail::to(auth()->user()->email)->queue(new PostCreated($post, auth()->user()));

        // dispatch(new SendNewPostEmail([
        //     'sendTo' => auth()->user()->email,
        //     'user' => auth()->user(),
        //     'post' => $post,
        // ]));

        return redirect()->route('dashboard.posts.index')->with('success', 'Post created successfully');

        // Handle image upload with nested folder structure (Intervention Image)
        // if ($request->hasFile('featured_image')) {
        //     $manager = new ImageManager(new Driver);
        //     $image = $manager->read($request->file('featured_image'));

        //     // Get original extension and filename
        //     $extension = $request->file('featured_image')->getClientOriginalExtension();
        //     $filename = $request->file('featured_image')->getClientOriginalName();

        //     // Generate featured image (1170px wide, maintaining aspect ratio)
        //     $featuredData = $image->scaleDown(width: 1170)->encodeByExtension($extension)->toString();
        //     $featuredPath = 'posts/'.$slug.'/'.$slug.'-featured.'.$extension;

        //     Storage::disk('s3')->put($featuredPath, $featuredData, $filename, ['visibility' => 'public']);

        //     // Generate thumbnail (128x128)
        //     $thumbnailData = $image->cover(128, 128)->encodeByExtension($extension)->toString();
        //     $thumbnailPath = 'posts/'.$slug.'/'.$slug.'-thumb.'.$extension;
        //     Storage::disk('s3')->put($thumbnailPath, $thumbnailData, $filename, ['visibility' => 'public']);

        //     // Store the featured image path in the database
        //     $validatedData['featured_image'] = $featuredPath;
        // }

        // Default image upload
        // if ($request->hasFile('featured_image')) {
        //     $path = $request->file('featured_image')->store('posts');
        //     $validatedData['featured_image'] = $path;
        // }

        // Post::create($validatedData);

        // return redirect()->route('dashboard.posts.index')->with('success', 'Post created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $categories = Category::all();

        return view('dashboard.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        // Check if the user is authorized to update the post
        if (auth()->user()->cannot('update', $post)) {
            abort(403);
        }

        try {
            $validatedData = $request->validated();

            // dd($request);

            // Update slug if title changed
            if ($post->title !== $validatedData['title']) {
                $slug = Str::slug($validatedData['title']);

                // Check if the slug exists and append a number if it does
                $count = 1;
                while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                    $slug = Str::slug($validatedData['title']).'-'.$count;
                    $count++;
                }

                $validatedData['slug'] = $slug;
            }

            $temporaryFile = TemporaryFile::where('folder', $request->image)->first();

            if ($temporaryFile) {
                // Delete old images if they exist
                if ($post->getFirstMediaUrl('posts')) {
                    $post->clearMediaCollection('posts');
                }

                $post
                    ->addMedia(storage_path('app/public/posts/tmp/'.$request->image.'/'.$temporaryFile->filename))
                    ->toMediaCollection('posts', 'posts');

                // Delete the temporary file record
                Storage::deleteDirectory('posts/tmp/'.$request->image);
                $temporaryFile->delete();
            }

            // Extract categories before updating the post
            $categories = $validatedData['categories'] ?? [];
            unset($validatedData['categories']);

            $post->update($validatedData);

            // Sync categories to the post
            $post->categories()->sync($categories);

            return redirect()->route('dashboard.posts.index')->with('success', 'Post updated successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Post update failed: '.$e->getMessage(), [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);

            // Redirect back with error message
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update post. Please try again.');
        }

        // Handle image upload if present
        // if ($request->hasFile('featured_image')) {
        //     // Delete old images if they exist
        //     if ($post->featured_image) {
        //         // Get the extension from the current featured image path
        //         $extension = pathinfo($post->featured_image, PATHINFO_EXTENSION);

        //         // Delete both featured and thumbnail images
        //         Storage::disk('s3')->delete($post->featured_image);
        //         Storage::disk('s3')->delete(str_replace('-featured.'.$extension, '-thumb.'.$extension, $post->featured_image));

        //         // Delete the old folder
        //         // Storage::disk('public')->deleteDirectory('posts/' . $post->slug);
        //     }

        //     $manager = new ImageManager(new Driver);
        //     $image = $manager->read($request->file('featured_image'));

        //     // Use the new slug if title changed, otherwise use the current post's slug
        //     $currentSlug = $validatedData['slug'] ?? $post->slug;

        //     $extension = $request->file('featured_image')->getClientOriginalExtension();
        //     $filename = $request->file('featured_image')->getClientOriginalName();

        //     // Generate featured image (1170px wide, maintaining aspect ratio)
        //     $featuredData = $image->scaleDown(width: 1170)->encodeByExtension($extension)->toString();
        //     $featuredPath = 'posts/'.$currentSlug.'/'.$currentSlug.'-featured.'.$extension;
        //     Storage::disk('s3')->put($featuredPath, $featuredData, $filename, ['visibility' => 'public']);

        //     // Generate thumbnail (128x128)
        //     $thumbnailData = $image->cover(128, 128)->encodeByExtension($extension)->toString();
        //     $thumbnailPath = 'posts/'.$currentSlug.'/'.$currentSlug.'-thumb.'.$extension;
        //     Storage::disk('s3')->put($thumbnailPath, $thumbnailData, $filename, ['visibility' => 'public']);

        //     $validatedData['featured_image'] = $featuredPath;
        // }

        // $post->update($validatedData);

        // return redirect()->route('dashboard.posts.index')->with('success', 'Post updated successfully');
    }

    public function destroy(Post $post)
    {
        // Check if the user is authorized to delete the post
        if (auth()->user()->cannot('delete', $post)) {
            abort(403);
        }

        // Delete old images if they exist
        // if ($post->featured_image) {
        //     // Get the extension from the current featured image path
        //     $extension = pathinfo($post->featured_image, PATHINFO_EXTENSION);

        //     // Delete both featured and thumbnail images
        //     Storage::disk('s3')->delete($post->featured_image);
        //     Storage::disk('s3')->delete(str_replace('-featured.'.$extension, '-thumb.'.$extension, $post->featured_image));

        //     // Delete the folder
        //     // Storage::disk('public')->deleteDirectory('posts/' . $post->slug);
        // }

        $post->delete();

        return redirect()->route('dashboard.posts.index')->with('success', 'Post deleted successfully');
    }
}
