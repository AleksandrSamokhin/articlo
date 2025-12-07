<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Follow a user.
     */
    public function store(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        // Can't follow yourself
        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        // Check if already following
        if ($currentUser->isFollowing($user)) {
            return back()->with('error', 'You are already following this user.');
        }

        $currentUser->follow($user);

        return back()->with('success', "You are now following {$user->name}.");
    }

    /**
     * Unfollow a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        // Check if not following
        if (! $currentUser->isFollowing($user)) {
            return back()->with('error', 'You are not following this user.');
        }

        $currentUser->unfollow($user);

        return back()->with('success', "You have unfollowed {$user->name}.");
    }
}
