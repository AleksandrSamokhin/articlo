<?php

namespace App\Services;

use App\Models\Post;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Storage;

class PostService
{
    /**
     * Prepare post data for creation.
     *
     * @param  array  $validatedData
     * @param  int  $userId
     * @return array
     */
    public function preparePostData(array $validatedData, int $userId): array
    {
        $validatedData['user_id'] = $userId;
        $validatedData['slug'] = Post::generateUniqueSlug($validatedData['title']);

        unset($validatedData['categories']);
        unset($validatedData['image']);

        return $validatedData;
    }

    /**
     * Prepare post data for update.
     *
     * @param  array  $validatedData
     * @param  Post  $post
     * @return array
     */
    public function preparePostDataForUpdate(array $validatedData, Post $post): array
    {
        if ($post->title !== $validatedData['title']) {
            $validatedData['slug'] = Post::generateUniqueSlug($validatedData['title'], $post->id);
        } else {
            $validatedData['slug'] = $post->slug;
        }

        unset($validatedData['categories']);
        unset($validatedData['image']);

        return $validatedData;
    }

    /**
     * Handle temporary file upload and attach to post.
     *
     * @param  Post  $post
     * @param  string|null  $imageFolder
     * @param  bool  $clearExisting
     * @return void
     */
    public function handleTemporaryFileUpload(Post $post, ?string $imageFolder, bool $clearExisting = false): void
    {
        if (! $imageFolder) {
            return;
        }

        $temporaryFile = TemporaryFile::where('folder', $imageFolder)->first();

        if (! $temporaryFile) {
            return;
        }

        if ($clearExisting && $post->getFirstMediaUrl('posts')) {
            $post->clearMediaCollection('posts');
        }

        $post
            ->addMedia(storage_path('app/public/posts/tmp/'.$imageFolder.'/'.$temporaryFile->filename))
            ->toMediaCollection('posts', 'posts');

        Storage::deleteDirectory('posts/tmp/'.$imageFolder);
        $temporaryFile->delete();
    }

    /**
     * Check if a temporary file exists for the given folder.
     *
     * @param  string|null  $folder
     * @return bool
     */
    public function hasTemporaryFile(?string $folder): bool
    {
        if (! $folder) {
            return false;
        }

        return TemporaryFile::where('folder', $folder)->exists();
    }
}
