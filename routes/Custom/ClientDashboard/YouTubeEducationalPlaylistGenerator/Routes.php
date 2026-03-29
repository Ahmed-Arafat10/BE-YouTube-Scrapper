<?php

use App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator\YouTubeEducationalPlaylistGeneratorController;
use \Illuminate\Support\Facades\Route;

Route::prefix('yt-playlist-generator')
    ->name('yt-playlist-generator.')
    ->group(function () {
        Route::get('home', [YouTubeEducationalPlaylistGeneratorController::class, 'home'])
            ->name('home');
        Route::post('scrapper', [YouTubeEducationalPlaylistGeneratorController::class, 'scrapper'])
            ->name('scrapper');
        Route::post('stop', [YouTubeEducationalPlaylistGeneratorController::class, 'stop'])
            ->name('stop');
        Route::get('check-status', [YouTubeEducationalPlaylistGeneratorController::class, 'checkStatus'])
            ->name('check-status');
    });
