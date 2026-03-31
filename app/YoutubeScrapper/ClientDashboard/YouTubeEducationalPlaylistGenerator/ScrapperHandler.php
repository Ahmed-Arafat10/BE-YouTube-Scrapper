<?php

namespace App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator;

use App\Models\YoutubeEducationalPlaylist;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ScrapperHandler
{
    private string $prompt;

    public function __construct(
        private readonly GeminiChatBot   $geminiChatBot,
        private readonly YouTubeScrapper $youtubeScrapper,
        private readonly int             $min = 10,
        private readonly int             $max = 20,
    )
    {
        $this->prompt = "Generate {$this->min} to {$this->max} educational course titles related to the category '#CATEGORY#'. Return ONLY a JSON array of strings containing the titles. Do not include any markdown or extra text.";
    }

    private function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }

    private function processAiTextResponse($aiTextResponse)
    {
        if (str_starts_with($aiTextResponse, '```')) {
            $aiTextResponse = preg_replace('/^```(?:json)?\s+/', '', $aiTextResponse);
            $aiTextResponse = preg_replace('/\s+```$/', '', $aiTextResponse);
        }
        return json_decode($aiTextResponse, true);
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function handle(array $categories): void
    {
        foreach ($categories as $category) {
            if (Cache::has('stop_youtube_scraper')) {
                break;
            }
            $aiTextResponse = trim(
                $this->geminiChatBot->sendRequest(
                    Str::replaceArray('#CATEGORY#', [$category], $this->prompt)
                )
            );
            $titles = $this->processAiTextResponse($aiTextResponse);
            //dd($titles); #DEBUG
            if (!is_array($titles)) throw new Exception("Invalid array response from AI");
            //$titles = array_slice($titles, 0, 10);
            foreach ($titles as $title) {
                if (Cache::has('stop_youtube_scraper')) {
                    break 2;
                }
                $youtubeItems = $this->youtubeScrapper->sentYoutubeRequest($title);
                //dd($youtubeItems); #DEBUG
                $this->insertNewPlaylist($youtubeItems, $category);
            }
        }
    }

    private function insertNewPlaylist(array $youtubeItems, $category): void
    {
        foreach ($youtubeItems as $item) {
            $playlistId = $item['id']['playlistId'] ?? null;
            if ($playlistId) {
                YoutubeEducationalPlaylist::query()->updateOrCreate([
                    'yt_playlist_id' => $playlistId
                ], [
                        'title' => html_entity_decode($item['snippet']['title'] ?? 'Unknown'),
                        'description' => html_entity_decode($item['snippet']['description'] ?? ''),
                        'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? ($item['snippet']['thumbnails']['default']['url'] ?? ''),
                        'channel_name' => html_entity_decode($item['snippet']['channelTitle'] ?? 'Unknown'),
                        'category' => $category,
                    ]
                );
            }
        }
    }
}
