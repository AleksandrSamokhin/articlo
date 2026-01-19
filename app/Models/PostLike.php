<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'post_likes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'ip',
        'user_agent',
    ];

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forIp($query, string $ip): mixed
    {
        return $query->where('ip', $ip);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function forUserAgent($query, string $userAgent): mixed
    {
        return $query->where('user_agent', $userAgent);
    }
}
