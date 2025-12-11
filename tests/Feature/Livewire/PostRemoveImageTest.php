<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PostRemoveImage;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('renders successfully', function () {
    Livewire::test(PostRemoveImage::class)
        ->assertStatus(200);
});

test('component exists on the page', function () {
    $post = Post::factory()->create();

    get('/dashboard/posts/'.$post->id.'/edit')
        ->assertSeeLivewire(PostRemoveImage::class);
});

test('image can be removed', function () {
    $post = Post::factory()->create();

    Livewire::test(PostRemoveImage::class, ['post' => $post])
        ->call('removeImage')
        ->assertSet('removed', true)
        ->assertSee('Image removed successfully.');

    $post->refresh();

    $this->assertEmpty($post->getFirstMediaUrl('posts'));
});
