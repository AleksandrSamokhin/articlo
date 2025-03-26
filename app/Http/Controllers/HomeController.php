<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $posts = Post::with('category')->when(request('category_id'), function ($query) {
            $query->where('category_id', request('category_id'));
        })->latest()->paginate(5);

        return view('home', compact('categories', 'posts'));
    }
}
