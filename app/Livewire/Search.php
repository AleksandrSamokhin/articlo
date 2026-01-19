<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    // Reset pagination when search term changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function results()
    {
        if (strlen($this->search) > 0) {
            return Post::search($this->search)->paginate(10);
        }

        return [];
    }

    public function render()
    {
        return view('livewire.search');
    }
}
