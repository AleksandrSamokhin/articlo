<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;

class PostCreateCategory extends ModalComponent
{
    #[Validate('required|string|min:2|max:255|unique:categories,name')]
    public string $name = '';

    public function createCategory()
    {
        $this->validate();

        Category::create([
            'name' => trim($this->name),
            'slug' => Str::slug($this->name),
        ]);

        // Reset form
        $this->reset(['name']);
        $this->resetValidation();

        // Show success message
        session()->flash('category-success', 'Category successfully created.');

        // Emit event to refresh categories list
        $this->dispatch('category-created');

        $this->dispatch('close-modal-with-delay');
    }

    public function render()
    {
        return view('livewire.post-create-category');
    }
}
