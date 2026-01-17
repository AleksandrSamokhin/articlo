<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Mail\PostCreated;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts()->with(['categories', 'media'])->latest()->paginate(10);

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
    public function store(StorePostRequest $request, PostService $postService)
    {
        $validatedData = $request->validated();
        $categories = $validatedData['categories'];

        $postData = $postService->preparePostData($validatedData, auth()->id());

        $post = Post::create($postData);
        $post->categories()->sync($categories);

        $postService->handleTemporaryFileUpload($post, $request->image);

        Mail::to(auth()->user()->email)->queue(new PostCreated($post, auth()->user()));

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
    public function update(StorePostRequest $request, Post $post, PostService $postService)
    {
        if (auth()->user()->cannot('update', $post)) {
            abort(403);
        }

        try {
            $validatedData = $request->validated();
            $categories = $validatedData['categories'] ?? [];

            $postService->handleTemporaryFileUpload($post, $request->image, clearExisting: true);

            $postData = $postService->preparePostDataForUpdate($validatedData, $post);

            $post->update($postData);
            $post->categories()->sync($categories);

            return redirect()->route('dashboard.posts.index')->with('success', 'Post updated successfully');
        } catch (\Exception $e) {
            \Log::error('Post update failed: '.$e->getMessage(), [
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'exception' => $e,
            ]);

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

        $post->delete();

        return redirect()->route('dashboard.posts.index')->with('success', 'Post deleted successfully');
    }
}
