<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $categories = Category::all();

        $posts = $category->posts()
            ->with('categories')
            ->withCount('comments')
            ->paginate(5);

        return view('categories.show', compact('categories', 'category', 'posts'));
    }
}
