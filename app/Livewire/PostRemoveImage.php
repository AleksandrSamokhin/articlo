<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class PostRemoveImage extends Component
{
    public Post $post;

    public bool $removed = false;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function removeImage(): void
    {
        $this->post->clearMediaCollection('posts');
        $this->removed = true;

        session()->flash('image-removed-success', 'Image removed successfully.');
    }

    public function render()
    {
        return view('livewire.post-remove-image');
    }
}
