<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FutureIdeaController extends Controller
{
    public function generate(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'idea' => 'required|string|max:5000'
            ]);

            // Get API key from config
            $apiKey = config('services.google.api_key');

            if (!$apiKey) {
                return response()->json([
                    'error' => 'Google API key is not configured'
                ], 500);
            }

            $endpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent';

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "You are a future-predicting assistant. For the idea: \"{$request->idea}\", respond with a single confident and deterministic future outcome. Never give multiple options. Keep it under 200 words."
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000,
                    'topP' => 0.9,
                    'topK' => 40
                ]
            ];

            // Make the API call
            $response = Http::timeout(30)->post("{$endpoint}?key={$apiKey}", $payload);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to generate prediction',
                    'details' => $response->json()
                ], 500);
            }

            $data = $response->json();
            $outputText = '';

            if (isset($data['candidates'][0]['content']['parts'])) {
                foreach ($data['candidates'][0]['content']['parts'] as $part) {
                    $outputText .= $part['text'] ?? '';
                }
            }

            return response()->json([
                'result' => $outputText ?: 'No prediction received.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
