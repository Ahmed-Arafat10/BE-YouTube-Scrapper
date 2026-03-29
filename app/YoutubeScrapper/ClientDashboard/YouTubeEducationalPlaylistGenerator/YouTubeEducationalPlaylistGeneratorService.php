<?php

namespace App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator;

use App\Jobs\ProcessYoutubeScrapperJob;
use App\Models\YoutubeEducationalPlaylist;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class YouTubeEducationalPlaylistGeneratorService
{
    public function home(YouTubeEducationalPlaylistGeneratorRequest $request): Factory|View|\Illuminate\View\View
    {
        $categoryQueryParam = $request->input('category');
        $playlists = YoutubeEducationalPlaylist::query()
            ->when($categoryQueryParam && $categoryQueryParam !== 'all', fn($query) => $query->where('category', $categoryQueryParam))
            ->latest()
            ->paginate(12);
        $categories = YoutubeEducationalPlaylist::query()
            ->select('category')
            ->distinct()
            ->pluck('category');
        $totalCount = YoutubeEducationalPlaylist::query()->count();
        return view('home', [
            'playlists' => $playlists,
            'categories' => $categories,
            'totalCount' => $totalCount,
            'category' => $categoryQueryParam,
        ]);
    }


    /**
     * @throws Throwable
     */
    public function scrapper(YouTubeEducationalPlaylistGeneratorRequest $request): \Illuminate\Http\JsonResponse
    {
        $categoriesText = $request->input('categories');
        $categories = array_filter(
            array_map('trim', explode("\n", $categoriesText)
            )
        );
        Cache::forget('stop_youtube_scraper');
        Cache::forget('youtube_scraper_finished');

        ProcessYoutubeScrapperJob::dispatch($categories)
            ->onConnection('database')
            ->onQueue('youtube_scrapper');

        return response()->json(['success' => true]);
    }

    public function stop(): JsonResponse
    {
        Cache::put('stop_youtube_scraper', true, 300);
        return response()->json(['success' => true]);
    }

    public function checkStatus(): JsonResponse
    {
        $finished = Cache::has('youtube_scraper_finished');
        $stopped = Cache::has('stop_youtube_scraper');
        return response()->json([
            'finished' => $finished,
            'stopped' => $stopped
        ]);
    }
}
