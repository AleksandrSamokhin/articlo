<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Whether to attach images when creating posts.
     */
    protected bool $withImage = true;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_featured' => fake()->randomElement([true, false]),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Post $post) {
            // Attach image if enabled (default: true)
            if ($this->withImage) {
                $this->attachImage($post);
            }
        });
    }

    /**
     * State: Create a post without an image.
     * Use this when you don't want images (e.g., in tests for speed).
     */
    public function withoutImage(): static
    {
        $this->withImage = false;

        return $this;
    }

    /**
     * Attach a placeholder image to the post.
     */
    protected function attachImage(Post $post): void
    {
        // Use Lorem Picsum for reliable placeholder images
        // Random seed ensures different images for each post
        $imageUrl = 'https://picsum.photos/1200/800?random='.fake()->numberBetween(1, 1000);

        $post->addMediaFromUrl($imageUrl)
            ->toMediaCollection('posts', 'posts');
    }
}
