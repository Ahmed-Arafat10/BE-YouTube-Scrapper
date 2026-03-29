<?php

namespace App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatBot implements ThirdPartyAIServiceProvider
{
    protected readonly string $apiKey;
    protected ?string $fullUrl = null;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    protected string $model = 'gemini-2.5-flash'; # gemini-3.1-pro-preview
    protected string $type = 'generateContent'; # gemini-3.1-pro-preview

    public function __construct($baseUrl = null)
    {
        $this->apiKey = config('api-keys.GEMINI_API_KEY');
        $this->baseUrl = $baseUrl ?? $this->baseUrl;
        $this->fullUrl = $this->baseUrl . '/' . $this->model . ':' . $this->type;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function sendRequest($prompt)
    {
        $httpRequestBody = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey
        ])->post($this->fullUrl, $httpRequestBody);
        //dd($response->successful(), $response->json()); #DEBUG
        if ($response->successful()) {
            Log::info('Gemini prompt' . $prompt);
            Log::info('Gemini API Response', [
                'json' => $response->json()
            ]);
            $json = $response->json();
            return $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        } else throw new Exception('Failed to scrapper data from Gemini API: ' . $response->json()['error']['message']);
    }
}
