<?php

namespace App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Throwable;

class YouTubeEducationalPlaylistGeneratorController extends Controller
{
    public function __construct(
        private readonly YouTubeEducationalPlaylistGeneratorService $service
    )
    {
    }

    /**
     * @param YouTubeEducationalPlaylistGeneratorRequest $request
     * @return View|Factory|\Illuminate\View\View
     */
    public function home(YouTubeEducationalPlaylistGeneratorRequest $request): View|Factory|\Illuminate\View\View
    {
        return $this->service->home($request);
    }

    /**
     * @param YouTubeEducationalPlaylistGeneratorRequest $request
     * @return JsonResponse
     * @throws ConnectionException
     * @throws Throwable
     */
    public function scrapper(YouTubeEducationalPlaylistGeneratorRequest $request): JsonResponse
    {
        return $this->service->scrapper($request);
    }

    public function checkStatus(): JsonResponse
    {
        return $this->service->checkStatus();
    }

    public function stop(): JsonResponse
    {
        return $this->service->stop();
    }

}
