<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class PostLike extends Component
{
    public $post;
    public $count;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->count = $post->likes_count ?? $post->likes()->count();
    }

    public function like(): void
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        if ($this->post->isLiked()) {
            $this->post->removeLike();

            $this->count--;
        } elseif (auth()->user()) {
            $this->post->likes()->create([
                'user_id' => auth()->id(),
            ]);

            $this->count++;
        } elseif ($ip && $userAgent) {
            $this->post->likes()->create([
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

            $this->count++;
        }
    }

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        return view('livewire.post-like');
    }
}

