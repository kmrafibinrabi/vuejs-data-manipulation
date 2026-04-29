<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageResearchController extends Controller
{
    public function analyze(Request $request)
    {
        try {
            $validated = $request->validate([
    'question' => 'required|string|max:500',
    'image' => 'required|image|max:5120' // max 5MB
]);

            $question = $validated['question'];

            // Handle base64 image
            if ($request->has('image_base64')) {
                $imageData = base64_decode($request->input('image_base64'));
                $tempPath = storage_path('app/temp_image_' . time() . '.jpg');
                file_put_contents($tempPath, $imageData);
                $imageBase64 = base64_encode($imageData);
                $mimeType = 'image/jpeg';
            }
            // Handle file upload - Use Laravel's store method instead
            elseif ($request->hasFile('image')) {
                $image = $request->file('image');

                // Store the file in Laravel's storage
                $storedPath = $image->store('temp_uploads', 'local');
                $fullPath = Storage::disk('local')->path($storedPath);

                // Read the file from Laravel storage
                $imageContent = Storage::disk('local')->get($storedPath);
                $imageBase64 = base64_encode($imageContent);
                $mimeType = $image->getMimeType();

                // Delete the temporary file
                Storage::disk('local')->delete($storedPath);
            }
            else {
                return response()->json([
                    'error' => 'No image provided. Please upload an image file.'
                ], 422);
            }

            // Call Google API
            $apiKey = config('services.google.api_key');
            if (!$apiKey) {
                return response()->json(['error' => 'Google API key not configured'], 500);
            }

            $endpoint = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent';

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $imageBase64
                                ]
                            ],
                            [
                                'text' => $question
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

            $response = Http::timeout(60)->post("{$endpoint}?key={$apiKey}", $payload);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to analyze image',
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
                'success' => true,
                'answer' => $outputText ?: 'No answer generated.'
            ]);

        } catch (\Exception $e) {
            Log::error('ImageResearch error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
