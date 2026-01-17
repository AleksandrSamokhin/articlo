<?php

use App\Models\Post;
use App\Models\TemporaryFile;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->postService = new PostService;
});

/**
 * Helper function to create a minimal valid JPEG image for testing
 */
function createTestImageForUnit(string $path, string $filename): string
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

describe('preparePostData', function () {
    it('adds user_id to post data', function () {
        $validatedData = [
            'title' => 'Test Post Title',
            'content' => 'Test content',
            'categories' => [1, 2],
            'image' => 'temp-folder',
        ];

        $result = $this->postService->preparePostData($validatedData, 123);

        expect($result['user_id'])->toBe(123);
    });

    it('generates a unique slug from title', function () {
        $validatedData = [
            'title' => 'My Test Post',
            'content' => 'Test content',
            'categories' => [1],
            'image' => null,
        ];

        $result = $this->postService->preparePostData($validatedData, 1);

        expect($result['slug'])->toBe('my-test-post');
    });

    it('removes categories from post data', function () {
        $validatedData = [
            'title' => 'Test Post',
            'content' => 'Content',
            'categories' => [1, 2, 3],
            'image' => null,
        ];

        $result = $this->postService->preparePostData($validatedData, 1);

        expect($result)->not->toHaveKey('categories');
    });

    it('removes image from post data', function () {
        $validatedData = [
            'title' => 'Test Post',
            'content' => 'Content',
            'categories' => [1],
            'image' => 'temp-folder-name',
        ];

        $result = $this->postService->preparePostData($validatedData, 1);

        expect($result)->not->toHaveKey('image');
    });
});

describe('preparePostDataForUpdate', function () {
    it('keeps existing slug when title unchanged', function () {
        $user = User::factory()->create();
        $post = Post::factory()->withoutImage()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'slug' => 'original-title',
        ]);

        $validatedData = [
            'title' => 'Original Title',
            'content' => 'Updated content',
            'categories' => [1],
        ];

        $result = $this->postService->preparePostDataForUpdate($validatedData, $post);

        expect($result['slug'])->toBe('original-title');
    });

    it('generates new slug when title changes', function () {
        $user = User::factory()->create();
        $post = Post::factory()->withoutImage()->create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'slug' => 'original-title',
        ]);

        $validatedData = [
            'title' => 'New Title',
            'content' => 'Content',
            'categories' => [1],
        ];

        $result = $this->postService->preparePostDataForUpdate($validatedData, $post);

        expect($result['slug'])->toBe('new-title');
    });

    it('removes categories from update data', function () {
        $user = User::factory()->create();
        $post = Post::factory()->withoutImage()->create(['user_id' => $user->id]);

        $validatedData = [
            'title' => $post->title,
            'content' => 'Content',
            'categories' => [1, 2, 3],
        ];

        $result = $this->postService->preparePostDataForUpdate($validatedData, $post);

        expect($result)->not->toHaveKey('categories');
    });

    it('removes image from update data', function () {
        $user = User::factory()->create();
        $post = Post::factory()->withoutImage()->create(['user_id' => $user->id]);

        $validatedData = [
            'title' => $post->title,
            'content' => 'Content',
            'categories' => [1],
            'image' => 'temp-folder',
        ];

        $result = $this->postService->preparePostDataForUpdate($validatedData, $post);

        expect($result)->not->toHaveKey('image');
    });
});

describe('hasTemporaryFile', function () {
    it('returns false when folder is null', function () {
        $result = $this->postService->hasTemporaryFile(null);

        expect($result)->toBeFalse();
    });

    it('returns false when temporary file does not exist', function () {
        $result = $this->postService->hasTemporaryFile('non-existent-folder');

        expect($result)->toBeFalse();
    });

    it('returns true when temporary file exists', function () {
        TemporaryFile::create([
            'folder' => 'test-folder',
            'filename' => 'test.jpg',
        ]);

        $result = $this->postService->hasTemporaryFile('test-folder');

        expect($result)->toBeTrue();
    });
});

describe('handleTemporaryFileUpload', function () {
    it('does nothing when image folder is null', function () {
        $user = User::factory()->create();
        // Create post directly without factory to avoid media hooks
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'slug' => 'test-post-'.uniqid(),
            'content' => 'Test content',
        ]);

        $this->postService->handleTemporaryFileUpload($post, null);

        expect($post->getFirstMediaUrl('posts'))->toBe('');
    });

    it('does nothing when temporary file does not exist', function () {
        $user = User::factory()->create();
        // Create post directly without factory to avoid media hooks
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'slug' => 'test-post-'.uniqid(),
            'content' => 'Test content',
        ]);

        $this->postService->handleTemporaryFileUpload($post, 'non-existent-folder');

        expect($post->getFirstMediaUrl('posts'))->toBe('');
    });

    it('attaches media and cleans up temporary files', function () {
        Storage::fake('public');

        $user = User::factory()->create();
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'slug' => 'test-post-'.uniqid(),
            'content' => 'Test content',
        ]);

        $folder = 'test-folder-'.uniqid();
        $filename = 'test-image.jpg';
        $tmpPath = storage_path("app/public/posts/tmp/{$folder}");

        // Create actual valid JPEG file for Spatie Media Library
        createTestImageForUnit($tmpPath, $filename);

        // Also register with Storage::fake for cleanup verification
        Storage::disk('public')->put("posts/tmp/{$folder}/{$filename}", 'fake image content');

        // Create temporary file record
        TemporaryFile::create([
            'folder' => $folder,
            'filename' => $filename,
        ]);

        $this->postService->handleTemporaryFileUpload($post, $folder);

        // Verify media was attached
        expect($post->getFirstMediaUrl('posts'))->not->toBeEmpty();

        // Verify temporary directory was deleted
        expect(Storage::disk('public')->exists("posts/tmp/{$folder}"))->toBeFalse();

        // Verify temporary file record was deleted
        $this->assertDatabaseMissing('temporary_files', ['folder' => $folder]);
    });

    it('clears existing media when clearExisting is true', function () {
        Storage::fake('public');

        $user = User::factory()->create();
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Test Post',
            'slug' => 'test-post-'.uniqid(),
            'content' => 'Test content',
        ]);

        // Add initial media
        $folder1 = 'first-'.uniqid();
        $filename1 = 'first.jpg';
        $tmpPath1 = storage_path("app/public/posts/tmp/{$folder1}");

        createTestImageForUnit($tmpPath1, $filename1);
        Storage::disk('public')->put("posts/tmp/{$folder1}/{$filename1}", 'first image');
        TemporaryFile::create(['folder' => $folder1, 'filename' => $filename1]);

        $post->addMedia("{$tmpPath1}/{$filename1}")
            ->toMediaCollection('posts', 'posts');

        expect($post->getMedia('posts'))->toHaveCount(1);

        // Add second media with clearExisting=true
        $folder2 = 'second-'.uniqid();
        $filename2 = 'second.jpg';
        $tmpPath2 = storage_path("app/public/posts/tmp/{$folder2}");

        createTestImageForUnit($tmpPath2, $filename2);
        Storage::disk('public')->put("posts/tmp/{$folder2}/{$filename2}", 'second image');
        TemporaryFile::create(['folder' => $folder2, 'filename' => $filename2]);

        $this->postService->handleTemporaryFileUpload($post, $folder2, true);

        $post->refresh();

        // Should only have 1 media item (old one cleared)
        expect($post->getMedia('posts'))->toHaveCount(1);

        // Verify cleanup
        expect(Storage::disk('public')->exists("posts/tmp/{$folder2}"))->toBeFalse();
        $this->assertDatabaseMissing('temporary_files', ['folder' => $folder2]);
    });
});
