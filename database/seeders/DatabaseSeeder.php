<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Create user
        $users = User::factory()->create();

        // Create 5 categories
        $categories = Category::factory(5)->create();

        // Create 7 posts
        $posts = Post::factory(7)
            ->recycle($users)
            ->recycle($categories)
            ->create();

        $posts->each(function ($post) use ($categories) {
            $post->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        // Create comments for posts
        $posts->each(function ($post) {
            Comment::factory(rand(0, 5))
                ->recycle($post->user)
                ->create(['post_id' => $post->id]);
        });

        // Create tags and attach to posts
        // $tags = Tag::factory(20)->create();

        // $posts->each(function ($post) use ($tags) {
        //     $post->tags()->attach(
        //         $tags->random(rand(1, 3))->pluck('id')->toArray()
        //     );
        // });
    }
}
