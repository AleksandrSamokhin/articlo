<?php

use App\Mail\PostCreated;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\TemporaryFile;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Support\Facades\Mail;

/**
 * Helper function to create a minimal valid JPEG image for testing
 */
function createTestImage(string $path, string $filename): string
{
    if (! file_exists($path)) {
        mkdir($path, 0755, true);
    }

    $fullPath = "{$path}/{$filename}";

    // Create a minimal 1x1 pixel JPEG image
    $image = imagecreatetruecolor(1, 1);
    imagecolorallocate($image, 255, 255, 255); // White pixel
    imagejpeg($image, $fullPath);
    imagedestroy($image);

    return $fullPath;
}

// ============================================
// Store Method Tests
// ============================================

test('admin_can_create_post_successfully', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $categories = Category::factory()->count(2)->create();

    $postData = [
        'title' => 'Test Post Title',
        'content' => 'Test post content here.',
        'categories' => $categories->pluck('id')->toArray(),
    ];

    $response = $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), $postData);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard.posts.index'))
        ->assertSessionHas('success', 'Post created successfully');

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post Title',
        'content' => 'Test post content here.',
        'user_id' => $admin->id,
    ]);
});

test('post_creation_syncs_categories_correctly', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $categories = Category::factory()->count(3)->create();

    $postData = [
        'title' => 'Test Post with Categories',
        'content' => 'Test content.',
        'categories' => $categories->pluck('id')->toArray(),
    ];

    $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), $postData);

    $post = Post::where('title', 'Test Post with Categories')->first();

    expect($post->categories)->toHaveCount(3);
    expect($post->categories->pluck('id')->sort()->values()->toArray())
        ->toBe($categories->pluck('id')->sort()->values()->toArray());
});

test('post_creation_handles_temporary_file_upload', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    // Create temporary file in actual filesystem
    $folder = 'test-'.uniqid();
    $filename = 'test.jpg';
    $tmpPath = storage_path("app/public/posts/tmp/{$folder}");

    createTestImage($tmpPath, $filename);
    TemporaryFile::create(['folder' => $folder, 'filename' => $filename]);

    $postData = [
        'title' => 'Post with Image',
        'content' => 'Content here.',
        'categories' => [$category->id],
        'image' => $folder,
    ];

    $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), $postData);

    $post = Post::where('title', 'Post with Image')->first();

    // Verify media was attached
    expect($post->getFirstMediaUrl('posts'))->not->toBeEmpty();

    // Verify temp directory cleaned up
    expect(file_exists(storage_path("app/public/posts/tmp/{$folder}")))->toBeFalse();
    $this->assertDatabaseMissing('temporary_files', ['folder' => $folder]);
});

test('post_creation_queues_email_notification', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    $postData = [
        'title' => 'Test Post',
        'content' => 'Test content.',
        'categories' => [$category->id],
    ];

    $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), $postData);

    $post = Post::where('title', 'Test Post')->first();

    Mail::assertQueued(PostCreated::class, function ($mail) use ($post, $admin) {
        return $mail->post->id === $post->id && $mail->hasTo($admin->email);
    });
});

test('post_creation_generates_unique_slug', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    $postData = [
        'title' => 'Same Title',
        'content' => 'First post content.',
        'categories' => [$category->id],
    ];

    // Create first post
    $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), $postData);

    // Create second post with same title
    $postData['content'] = 'Second post content.';
    $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), $postData);

    $posts = Post::where('title', 'Same Title')->get();

    expect($posts)->toHaveCount(2);
    expect($posts[0]->slug)->toBe('same-title');
    expect($posts[1]->slug)->toBe('same-title-1');
});

test('guest_cannot_create_post', function () {
    $category = Category::factory()->create();

    $postData = [
        'title' => 'Test Post',
        'content' => 'Test content.',
        'categories' => [$category->id],
    ];

    $response = $this->post(route('dashboard.posts.store'), $postData);

    $response->assertRedirect(route('login'));
});

test('post_creation_validates_required_fields', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)
        ->post(route('dashboard.posts.store'), []);

    $response->assertSessionHasErrors(['title', 'content', 'categories']);
});

// ============================================
// Update Method Tests
// ============================================

test('admin_can_update_own_post', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();
    $category = Category::factory()->create();

    $updateData = [
        'title' => 'Updated Title',
        'content' => 'Updated content.',
        'categories' => [$category->id],
    ];

    $response = $this->actingAs($admin)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard.posts.index'))
        ->assertSessionHas('success', 'Post updated successfully');

    $post->refresh();

    $this->assertSame('Updated Title', $post->title);
    $this->assertSame('Updated content.', $post->content);
});

test('post_update_regenerates_slug_when_title_changes', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create(['title' => 'Original Title']);
    $category = Category::factory()->create();

    $originalSlug = $post->slug;

    $updateData = [
        'title' => 'New Title',
        'content' => $post->content,
        'categories' => [$category->id],
    ];

    $this->actingAs($admin)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $post->refresh();

    expect($post->slug)->not->toBe($originalSlug);
    expect($post->slug)->toBe('new-title');
});

test('post_update_keeps_slug_when_title_unchanged', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();
    $category = Category::factory()->create();

    $originalSlug = $post->slug;

    $updateData = [
        'title' => $post->title,
        'content' => 'New content only.',
        'categories' => [$category->id],
    ];

    $this->actingAs($admin)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $post->refresh();

    expect($post->slug)->toBe($originalSlug);
});

test('post_update_syncs_categories', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();

    $oldCategories = Category::factory()->count(2)->create();
    $post->categories()->sync($oldCategories->pluck('id'));

    $newCategories = Category::factory()->count(3)->create();

    $updateData = [
        'title' => $post->title,
        'content' => $post->content,
        'categories' => $newCategories->pluck('id')->toArray(),
    ];

    $this->actingAs($admin)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $post->refresh();

    expect($post->categories)->toHaveCount(3);
    expect($post->categories->pluck('id')->sort()->values()->toArray())
        ->toBe($newCategories->pluck('id')->sort()->values()->toArray());
});

test('post_update_replaces_image_with_clear_existing', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    // Create post directly without factory to avoid media hooks
    $post = Post::create([
        'user_id' => $admin->id,
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Test content',
    ]);

    // Add first image manually
    $folder1 = 'first-'.uniqid();
    $filename1 = 'first.jpg';
    $tmpPath1 = storage_path("app/public/posts/tmp/{$folder1}");

    createTestImage($tmpPath1, $filename1);
    TemporaryFile::create(['folder' => $folder1, 'filename' => $filename1]);

    $post->addMedia("{$tmpPath1}/{$filename1}")
        ->toMediaCollection('posts', 'posts');

    $post->refresh();
    expect($post->getMedia('posts'))->toHaveCount(1);

    // Now update with second image
    $folder2 = 'second-'.uniqid();
    $filename2 = 'second.jpg';
    $tmpPath2 = storage_path("app/public/posts/tmp/{$folder2}");

    createTestImage($tmpPath2, $filename2);
    TemporaryFile::create(['folder' => $folder2, 'filename' => $filename2]);

    $updateData = [
        'title' => $post->title,
        'content' => $post->content,
        'categories' => [$category->id],
        'image' => $folder2,
    ];

    $this->actingAs($admin)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $post->refresh();

    // Should only have 1 media item (old one cleared)
    expect($post->getMedia('posts'))->toHaveCount(1);
});

test('admin_cannot_update_other_users_post', function () {
    $admin1 = User::factory()->create(['is_admin' => true]);
    $admin2 = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin1)->create();
    $category = Category::factory()->create();

    $updateData = [
        'title' => 'Hacked Title',
        'content' => 'Hacked content.',
        'categories' => [$category->id],
    ];

    $response = $this->actingAs($admin2)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $response->assertStatus(403);
});

test('regular_user_cannot_access_update_endpoint', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();
    $category = Category::factory()->create();

    $updateData = [
        'title' => 'Updated Title',
        'content' => 'Updated content.',
        'categories' => [$category->id],
    ];

    $response = $this->actingAs($user)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $response->assertStatus(403);
});

test('post_update_handles_exception_gracefully', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();
    $category = Category::factory()->create();

    // Mock PostService to throw exception
    $this->mock(PostService::class, function ($mock) {
        $mock->shouldReceive('handleTemporaryFileUpload')
            ->andThrow(new \Exception('Upload failed'));
        $mock->shouldReceive('preparePostDataForUpdate')
            ->andReturn([]);
    });

    $updateData = [
        'title' => 'Will Fail',
        'content' => 'Will fail.',
        'categories' => [$category->id],
    ];

    $response = $this->actingAs($admin)
        ->from(route('dashboard.posts.edit', $post))
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $response
        ->assertRedirect(route('dashboard.posts.edit', $post))
        ->assertSessionHas('error', 'Failed to update post. Please try again.')
        ->assertSessionHasInput('title', 'Will Fail');
});

test('post_update_without_image_keeps_existing', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    // Create post directly without factory to avoid media hooks
    $post = Post::create([
        'user_id' => $admin->id,
        'title' => 'Test Post',
        'slug' => 'test-post-'.uniqid(),
        'content' => 'Test content',
    ]);

    // Add image
    $folder = 'existing-'.uniqid();
    $filename = 'existing.jpg';
    $tmpPath = storage_path("app/public/posts/tmp/{$folder}");

    createTestImage($tmpPath, $filename);
    TemporaryFile::create(['folder' => $folder, 'filename' => $filename]);

    $post->addMedia("{$tmpPath}/{$filename}")
        ->toMediaCollection('posts', 'posts');

    $post->refresh();
    expect($post->getMedia('posts'))->toHaveCount(1);

    // Update without image field
    $updateData = [
        'title' => 'Updated Title',
        'content' => 'Updated content.',
        'categories' => [$category->id],
    ];

    $this->actingAs($admin)
        ->patch(route('dashboard.posts.update', $post), $updateData);

    $post->refresh();

    // Image should still exist
    expect($post->getMedia('posts'))->toHaveCount(1);
});

// ============================================
// Destroy Method Tests
// ============================================

test('admin_can_delete_own_post', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();

    $response = $this->actingAs($admin)
        ->delete(route('dashboard.posts.destroy', $post));

    $response
        ->assertRedirect(route('dashboard.posts.index'))
        ->assertSessionHas('success', 'Post deleted successfully');

    $this->assertNull($post->fresh());
});

test('post_deletion_cascades_to_categories_pivot', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();
    $categories = Category::factory()->count(3)->create();

    $post->categories()->sync($categories->pluck('id'));

    $this->assertDatabaseHas('category_post', ['post_id' => $post->id]);

    $this->actingAs($admin)
        ->delete(route('dashboard.posts.destroy', $post));

    // Pivot records should be deleted
    $this->assertDatabaseMissing('category_post', ['post_id' => $post->id]);

    // Categories themselves should still exist
    foreach ($categories as $category) {
        $this->assertNotNull($category->fresh());
    }
});

test('post_deletion_cascades_to_likes', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();
    $users = User::factory()->count(3)->create();

    // Create likes for the post
    foreach ($users as $user) {
        PostLike::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    $this->assertDatabaseHas('post_likes', ['post_id' => $post->id]);

    $this->actingAs($admin)
        ->delete(route('dashboard.posts.destroy', $post));

    // Likes should be deleted
    $this->assertDatabaseMissing('post_likes', ['post_id' => $post->id]);
});

test('post_deletion_removes_media_files', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_admin' => true]);

    // Create post directly without factory to avoid media hooks
    $post = Post::create([
        'user_id' => $admin->id,
        'title' => 'Test Post',
        'slug' => 'test-post-'.uniqid(),
        'content' => 'Test content',
    ]);

    // Add media
    $folder = 'delete-test-'.uniqid();
    $filename = 'delete-test.jpg';
    $tmpPath = storage_path("app/public/posts/tmp/{$folder}");

    createTestImage($tmpPath, $filename);
    TemporaryFile::create(['folder' => $folder, 'filename' => $filename]);

    $post->addMedia("{$tmpPath}/{$filename}")
        ->toMediaCollection('posts', 'posts');

    $post->refresh();
    expect($post->getMedia('posts'))->toHaveCount(1);

    $this->actingAs($admin)
        ->delete(route('dashboard.posts.destroy', $post));

    // Media records should be deleted
    $this->assertDatabaseMissing('media', ['model_id' => $post->id, 'model_type' => Post::class]);
});

test('admin_cannot_delete_other_users_post', function () {
    $admin1 = User::factory()->create(['is_admin' => true]);
    $admin2 = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin1)->create();

    $response = $this->actingAs($admin2)
        ->delete(route('dashboard.posts.destroy', $post));

    $response->assertStatus(403);

    // Post should still exist
    $this->assertNotNull($post->fresh());
});

test('regular_user_cannot_delete_any_post', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();

    $response = $this->actingAs($user)
        ->delete(route('dashboard.posts.destroy', $post));

    $response->assertStatus(403);

    // Post should still exist
    $this->assertNotNull($post->fresh());
});

test('guest_cannot_delete_post', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::factory()->withoutImage()->for($admin)->create();

    $response = $this->delete(route('dashboard.posts.destroy', $post));

    $response->assertRedirect(route('login'));

    // Post should still exist
    $this->assertNotNull($post->fresh());
});
