<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class PostCommentCount extends Component
{
    public int $count;

    #[On('comment-added')]
    public function refreshCount(): void
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.post-comment-count');
    }
}
