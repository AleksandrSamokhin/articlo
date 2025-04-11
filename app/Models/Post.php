<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['title', 'content', 'featured_image', 'category_id', 'user_id', 'slug'];

    /**
     * Scope a query to filter by category when provided.
     */
    public function scopeByCategory(Builder $query): Builder
    {
        return $query->when(request('category_id'), function ($query) {
            $query->where('category_id', request('category_id'));
        });
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => strip_tags($this->content),
        ];
    }

    public function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit(strip_tags(Str::markdown($this->content)), 150)
        );
    }

    protected function thumb(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->featured_image ? str_replace('-featured', '-thumb', $this->featured_image) : null,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }
}
