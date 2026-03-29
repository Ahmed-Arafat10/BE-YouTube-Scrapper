<?php

namespace App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator;

interface ThirdPartyAIServiceProvider
{
    public function sendRequest(string $prompt);
}
