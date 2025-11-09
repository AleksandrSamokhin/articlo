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

        // Remove image from validated data (it's only used as temporary folder identifier)
        unset($validatedData['image']);

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

            // Remove image from validated data (it's only used as temporary folder identifier)
            unset($validatedData['image']);

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
