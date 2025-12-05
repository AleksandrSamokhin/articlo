<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $adminPassword = config('app.admin_password');

        return [
            'name' => 'Aleksandr Samokhin',
            'email' => 'samokhinteam@gmail.com',
            'email_verified_at' => now(),
            'is_admin' => true,
            'password' => static::$password ??= Hash::make($adminPassword),
            'remember_token' => Str::random(10),
        ];
    }

    public function normalUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'John Doe',
            'email' => fake()->email(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
            'is_admin' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
