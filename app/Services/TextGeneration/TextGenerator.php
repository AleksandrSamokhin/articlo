<?php

namespace App\Services\TextGeneration;

interface TextGenerator
{
    public function getContent(string $title): string;
}
