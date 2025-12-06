<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = config('app.admin_password');

        // Create users
        $adminUser = User::firstOrCreate(
            ['email' => 'samokhinteam@gmail.com'],
            ['username' => 'alexadmin', 'name' => 'Aleksandr Samokhin', 'password' => Hash::make($adminPassword), 'email_verified_at' => now(), 'is_admin' => true]
        );

        $users = User::factory(10)->create();

        // Create random follow relationships between users
        $users->each(function ($user) use ($users) {
            // Each user follows 2-5 random other users
            $usersToFollow = $users->where('id', '!=', $user->id)->random(rand(2, 5));
            $usersToFollow->each(function ($userToFollow) use ($user) {
                $user->following()->attach($userToFollow->id);
            });
        });

        // Create 5 categories
        $categories = Category::factory(5)->create();

        // Create 20 posts
        $posts = Post::factory(20)
            ->recycle($users)
            ->create();

        // Attach categories to posts (many-to-many relationship)
        $posts->each(function ($post) use ($categories) {
            $post->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        // Add comments to posts from different users
        $posts->each(function ($post) use ($users) {
            // Each post gets 2-5 comments from random users
            $commentCount = rand(2, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                $post->comments()->create([
                    'user_id' => $users->random()->id,
                    'body' => fake()->paragraph(),
                ]);
            }
        });

        // Add post likes from different users
        $posts->each(function ($post) use ($users) {
            // Each post gets 3-8 likes from random users
            $likingUsers = $users->random(rand(3, min(8, $users->count())));
            $likingUsers->each(function ($user) use ($post) {
                $post->likes()->create([
                    'user_id' => $user->id,
                ]);
            });
        });

    }
}
