<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class PostContentGenerator extends Component
{
    public string $title = '';

    public string $content = '';

    public string $error = '';

    public function mount()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function generateContent()
    {
        if (! Auth::check()) {
            $this->error = 'You must be logged in to generate content.';

            return;
        }

        $this->validate([
            'title' => 'required|string|max:254',
        ]);

        $this->error = '';

        if (empty($this->title)) {
            $this->error = 'Please enter a title before generating content.';

            return;
        }

        try {
            // OpenAI
            // $response = Prism::text()
            //     ->using(Provider::OpenAI, 'gpt-4o-mini')
            //     ->withPrompt(view('prompts.post-content', ['title' => $this->title])->render())
            //     ->asText();

            // $this->content = $response->text;

            // Anthropic
            $response = Prism::text()
                ->using(Provider::Anthropic, 'claude-haiku-4-5-20251001')
                ->withPrompt(view('prompts.post-content', ['title' => $this->title])->render())
                ->asText();

            $this->content = $response->text;

            info('Content generated', [
                'prompt_tokens' => $response->usage->promptTokens,
                'completion_tokens' => $response->usage->completionTokens,
            ]);
        } catch (\Exception $e) {
            \Log::error('AI content generation error: '.$e->getMessage());
            $this->error = 'Failed to generate content. Please try again.';
        }
    }

    public function render()
    {
        return view('livewire.post-content-generator');
    }
}
