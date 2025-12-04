<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $postsQuery = Post::with('media', 'user.media')
            ->withCount('comments')
            ->where('is_featured', false)
            ->byCategory();

        // Filter by followed users if authenticated
        if (Auth::check()) {
            $postsQuery->fromFollowedUsers(Auth::id());
        }

        $posts = $postsQuery->latest()->paginate(6);

        $featuredPosts = Post::with('media', 'user.media')
            ->withCount('comments')
            ->where('is_featured', true)
            ->byCategory()
            ->latest()
            ->paginate(3);

        $users = User::with('media')
            ->latest()
            ->paginate(10);

        return view('home', compact('categories', 'posts', 'featuredPosts', 'users'));
    }
}
