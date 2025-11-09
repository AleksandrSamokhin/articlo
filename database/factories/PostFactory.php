<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'The Future of Technology',
            'Health and Wellness Tips',
            'The Rise of Biophilic Design: Bringing Nature Indoors',
            'Marketing Strategies for 2023',
            'Smart Homes 2.0: The Future of Residential Architecture',
            'The Impact of AI on Society',
            '5 Sustainable Materials Reshaping Modern Architecture',
        ];
        $title = fake()->unique()->randomElement($titles);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_featured' => fake()->randomElement([true, false]),
            'image' => 'posts/post_'.fake()->numberBetween(1, 7).'.webp',
        ];
    }
}
