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
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'username' => fake()->userName(),
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
            'is_admin' => false,
        ];
    }

    public function adminUser(): static
    {
        $adminPassword = config('app.admin_password');
        $adminEmail = config('app.admin_email');

        return $this->state(fn (array $attributes) => [
            'name' => 'Aleksandr Samokhin',
            'email' => $adminEmail,
            'email_verified_at' => now(),
            'username' => 'admin',
            'is_admin' => true,
            'password' => static::$password ??= Hash::make($adminPassword),
            'remember_token' => Str::random(10),
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
