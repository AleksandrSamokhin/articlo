<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $posts = Post::with(['category', 'media'])
            ->where('is_featured', false)
            ->byCategory()
            ->latest()
            ->paginate(5);

        $featuredPosts = Post::with(['category', 'media'])
            ->where('is_featured', true)
            ->byCategory()
            ->latest()
            ->paginate(3);

        return view('home', compact('categories', 'posts', 'featuredPosts'));
    }
}
