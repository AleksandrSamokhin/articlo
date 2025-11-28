<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, Searchable;
    use InteractsWithMedia;

    protected $fillable = ['title', 'content', 'user_id', 'slug'];

    public static function generateUniqueSlug(string $title, ?int $postId = null): string
    {
        $slug = Str::slug($title);
        $count = 1;

        $query = static::where('slug', $slug);
        if ($postId) {
            $query->where('id', '!=', $postId);
        }

        while ($query->exists()) {
            $slug = Str::slug($title).'-'.$count;
            $count++;

            $query = static::where('slug', $slug);
            if ($postId) {
                $query->where('id', '!=', $postId);
            }
        }

        return $slug;
    }

    /**
     * Scope a query to filter by category when provided.
     */
    public function scopeByCategory(Builder $query): Builder
    {
        return $query->when(request('category_id'), function ($query) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', request('category_id'));
            });
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
            get: fn () => Str::limit(strip_tags(Str::markdown($this->content)), 100)
        );
    }

    protected function thumb(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? str_replace('-featured', '-thumb', $this->image) : null,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Register the conversions that should be performed.
     *
     * @return array
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb-1170')
            ->keepOriginalImageFormat()
            ->width(1170);

        $this
            ->addMediaConversion('thumb-128')
            ->keepOriginalImageFormat()
            ->width(128)
            ->height(128);

        $this
            ->addMediaConversion('thumb-564')
            ->keepOriginalImageFormat()
            ->width(564);
    }
}
