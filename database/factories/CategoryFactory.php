<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ['Marketing', 'Business', 'Technology', 'Health', 'Travel'];
        $randomName = fake()->unique()->randomElement($name);

        return [
            'name' => $randomName,
            'slug' => Str::slug($randomName),
        ];
    }
}
