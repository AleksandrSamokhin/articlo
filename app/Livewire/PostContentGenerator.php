<?php

namespace App\Livewire;

use App\Services\TextGeneration\TextGenerationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostContentGenerator extends Component
{
    public $title = '';

    public $content = '';

    public $error = '';

    public function mount()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function generateContent(TextGenerationService $textGenerationService)
    {
        if (! Auth::check()) {
            $this->error = 'You must be logged in to generate content.';

            return;
        }

        $this->error = '';

        if (empty($this->title)) {
            $this->error = 'Please enter a title before generating content.';

            return;
        }

        try {
            $this->content = $textGenerationService->getContent('openai', $this->title);
        } catch (\Exception $e) {
            \Log::error('Livewire AI content generation error: '.$e->getMessage());
            $this->error = 'Failed to generate content. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.post-content-generator');
    }
}
