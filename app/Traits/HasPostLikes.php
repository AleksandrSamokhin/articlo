<?php

namespace App\Traits;

use App\Models\PostLike;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

trait HasPostLikes
{
    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * @return bool
     */
    public function isLiked(): bool
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        
        if (Auth::check()) {
            $userId = Auth::id();
            // Check if we have eager-loaded likes for the current user
            if ($this->relationLoaded('likes')) {
                return $this->likes->contains('user_id', $userId);
            }
            
            return $this->likes()->where('user_id', $userId)->exists();
        }

        if ($ip && $userAgent) {
            // Check if we have eager-loaded likes
            if ($this->relationLoaded('likes')) {
                return $this->likes->filter(function ($like) use ($ip, $userAgent) {
                    return $like->ip === $ip && $like->user_agent === $userAgent;
                })->isNotEmpty();
            }
            
            return $this->likes()->forIp($ip)->forUserAgent($userAgent)->exists();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function removeLike(): bool
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        if (Auth::check()) {
            return $this->likes()->where('user_id', Auth::id())->where('post_id', $this->id)->delete();
        }

        if ($ip && $userAgent) {
            return $this->likes()->forIp($ip)->forUserAgent($userAgent)->delete();
        }

        return false;
    }
}

