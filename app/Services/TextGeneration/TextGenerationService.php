<?php

namespace App\Services\TextGeneration;

use App\Enums\TextGenerationProviders;
use Exception;
use Illuminate\Support\Facades\Auth;

class TextGenerationService
{
    public function getContent(string $provider, string $title): string
    {
        throw_unless(Auth::check(), new Exception('Unauthorized access to content generation.'));

        return match ($provider) {
            TextGenerationProviders::OPENAI->value => (new OpenAi)->getContent($title),
            default => throw new Exception('Provider not supported'),
        };
    }
}
