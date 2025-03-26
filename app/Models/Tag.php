<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Post;

class Tag extends Model
{
    use HasFactory;
    
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
