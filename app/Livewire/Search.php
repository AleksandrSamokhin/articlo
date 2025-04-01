<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class Search extends Component
{
    use WithPagination;

    public $searchTerm = '';

    // Reset pagination when search term changes
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $results = [];

        if ( strlen($this->searchTerm) > 0 ) {
            $results = Post::search($this->searchTerm)->paginate(10);
        }

        return view('livewire.search', compact('results'));
    }
}
