<?php

namespace App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class YouTubeScrapper
{
    private string $youtubeUrl = 'https://www.googleapis.com/youtube/v3/search';
    protected readonly string $youtubeApiKey;
    protected readonly string $maxResults;

    public function __construct($maxResults = 2)
    {
        $this->youtubeApiKey = config('api-keys.YOUTUBE_API_KEY');
        $this->maxResults = $maxResults;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function sentYoutubeRequest(string $title): array
    {
        $httpRequestBody = [
            'part' => 'snippet',
            'maxResults' => $this->maxResults,
            'q' => $title,
            'type' => 'playlist',
            'key' => $this->youtubeApiKey,
        ];
        $ytResponse = Http::get($this->youtubeUrl, $httpRequestBody);
        // dd($ytResponse->json()); #DEBUG
        if ($ytResponse->successful()) {
            $items = $ytResponse->json('items');
            if (!is_array($items))
                throw new Exception("data is not array in YouTube API, title {$title}");
            Log::info('YouTube playlist title: ' . $title);
            $items = array_slice($items, 0, $this->maxResults);
            Log::info('YouTube API Response', [
                'json' => $ytResponse->json(),
                'sliced' => $items
            ]);
            return $items;
        }
        throw new Exception("Failed to fetch data from YouTube API, title {$title} " .  json_encode($ytResponse->json()));
    }
}
