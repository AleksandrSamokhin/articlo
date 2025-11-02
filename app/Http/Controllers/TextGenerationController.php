<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextGenerationRequest;
use App\Services\TextGeneration\TextGenerationService;
use Exception;
use Illuminate\Http\JsonResponse;

class TextGenerationController extends Controller
{
    public function __invoke(TextGenerationRequest $request, TextGenerationService $textGenerationService): JsonResponse
    {
        return response()->json([
            'text' => match ($request->input('type')) {
                'content' => $textGenerationService->getContent($request->string('provider'), $request->string('title')),
                default => throw new Exception('Provider not supported'), // Required for phpstan.
            },
        ]);
    }
}
