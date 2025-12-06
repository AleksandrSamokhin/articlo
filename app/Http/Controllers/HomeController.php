<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $postsQuery = Post::with('media', 'likes', 'user.media')
            ->withCount('comments')
            // ->where('is_featured', false)
            ->byCategory();

        // Filter by followed users if authenticated
        if (auth()->check()) {
            $postsQuery->fromFollowedUsers(auth()->id());
        }

        $posts = $postsQuery->latest()->paginate(6);

        // $featuredPosts = Post::with('media', 'likes', 'user.media')
        //     ->withCount(['comments', 'likes'])
        //     ->where('is_featured', true)
        //     ->byCategory()
        //     ->latest()
        //     ->paginate(3);

        $users = User::with('media')
            ->latest()
            ->when(auth()->check(), fn($query) => $query->where('id', '!=', auth()->id()))
            ->take(5)
            ->get();

        $popularPosts = Post::with('media', 'likes', 'user.media')
            ->withCount(['comments', 'likes'])
            ->orderBy('likes_count', 'desc')
            ->take(5)
            ->get();

        return view('home', compact('categories', 'posts', 'users', 'popularPosts'));
    }
}
