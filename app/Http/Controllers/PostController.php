<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function search($term)
    {
        $posts = Post::search($term)->get();

        return $posts;
    }

    public function show(Post $post)
    {
        $post->loadCount('likes');
        
        if (Auth::check()) {
            // Eager load current user's likes to avoid N+1 queries
            $post->load(['likes' => function ($query) {
                $query->where('user_id', Auth::id());
            }]);
        }
        
        $post['content'] = Str::markdown($post->content);

        return view('posts.show', compact('post'));
    }
}
