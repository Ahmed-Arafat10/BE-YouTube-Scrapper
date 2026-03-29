<?php

namespace App\Jobs;

use App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator\GeminiChatBot;
use App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator\ScrapperHandler;
use App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator\YouTubeScrapper;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessYoutubeScrapperJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    private array $categories;
    private ScrapperHandler $scrapperHandler;

    public function __construct(array $categories, $min = 1, $max = 2)
    {
        $this->categories = $categories;
        $this->scrapperHandler = new ScrapperHandler(
            new GeminiChatBot(),
            new YouTubeScrapper(),
            min: $min,
            max: $max,
        );
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                $this->scrapperHandler->handle($this->categories);
            });
            Cache::put('youtube_scraper_finished', true, 60);
        } catch (Exception $e) {
            dump($e->getMessage());
            $this->failed($e);
        }
    }

    public function failed(Throwable $exception)
    {
        Log::emergency('Youtube Scrapper Job failed', ['exception' => $exception]);
    }
}
