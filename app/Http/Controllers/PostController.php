<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
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
        $post['content'] = Str::markdown($post->content);
        return view('posts.show', compact('post'));
    }
}