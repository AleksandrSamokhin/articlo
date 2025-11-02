<?php

namespace App\Services\TextGeneration;

use Exception;
use Illuminate\Support\Facades\Http;

class OpenAi implements TextGenerator
{
    public function getContent(string $title): string
    {
        $prompt = view('text-generation.openai.content', ['title' => $title])->render();

        return $this->callOpenAi($prompt);
    }

    private function callOpenAi(string $prompt): string
    {
        try {
            $response = Http::withToken(config('services.ai.openai.key'))
                ->asJson()
                ->acceptJson()
                ->post(config('services.ai.openai.url'), [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ])->json('choices.0.message.content');

            return $response;
        } catch (Exception $e) {
            \Log::error('OpenAI API Error: '.$e->getMessage());

            return 'We were unable to generate the content at this time. Please try again later.';
        }
    }
}
