<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    /**
     * Display the feed of posts from followed users.
     */
    public function index()
    {
        $user = Auth::user();

        $posts = Post::with(['user.media', 'categories', 'media'])
            ->fromFollowedUsers($user->id)
            ->latest()
            ->paginate(10);

        return view('feed.index', compact('posts'));
    }
}
