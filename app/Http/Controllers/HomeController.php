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

        $posts = Post::with('media')
            ->where('is_featured', false)
            ->byCategory()
            ->latest()
            ->paginate(6);

        $featuredPosts = Post::with('media')
            ->where('is_featured', true)
            ->byCategory()
            ->latest()
            ->paginate(3);

        $users = User::select('id', 'name', 'username')
            ->latest()
            ->paginate(10);

        return view('home', compact('categories', 'posts', 'featuredPosts', 'users'));
    }
}
