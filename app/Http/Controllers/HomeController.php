<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $posts = Post::with('category')
            ->where('is_featured', false)
            ->byCategory()
            ->latest()
            ->paginate(5);

        $featuredPosts = Post::with('category')
            ->where('is_featured', true)
            ->byCategory()
            ->latest()
            ->paginate(3);

        return view('home', compact('categories', 'posts', 'featuredPosts'));
    }
}
