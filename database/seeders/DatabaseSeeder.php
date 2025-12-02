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
        $adminPassword = env('ADMIN_PASSWORD') ?? 'password';

        // Create user
        $users = User::firstOrCreate(
            ['email' => 'samokhinteam@gmail.com'],
            ['name' => 'Aleksandr Samokhin', 'password' => Hash::make($adminPassword), 'email_verified_at' => now(), 'is_admin' => true]
        );

        // Create 5 categories
        $categories = Category::factory(5)->create();

        // Create 7 posts
        $posts = Post::factory(7)
            ->recycle($users)
            ->create();

        // Attach categories to posts (many-to-many relationship)
        $posts->each(function ($post) use ($categories) {
            $post->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        // // Create comments for posts
        // $posts->each(function ($post) {
        //     Comment::factory(rand(0, 5))
        //         ->recycle($post->user)
        //         ->create(['post_id' => $post->id]);
        // });
    }
}
