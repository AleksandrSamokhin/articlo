<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class PostComments extends Component
{
    public Post $post;

    public string $comment = '';

    public function saveComment(): void
    {
        $this->validate([
            'comment' => 'required|string|max:255',
        ]);

        $this->post->comments()->create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => $this->comment,
        ]);

        $this->dispatch('comment-added');

        $this->reset('comment');
    }
}
