<?php

namespace App\Enums;

enum TextGenerationProviders: string
{
    case OPENAI = 'openai';
    case CLAUDE = 'claude';
}
