<?php


use Illuminate\Support\Facades\Route;


Route::prefix('m3aaref')->group(function () {
    require __DIR__ . '/Custom/ClientDashboard/YouTubeEducationalPlaylistGenerator/Routes.php';
});

Route::get('/', function () {
    return redirect()->route('yt-playlist-generator.home');
});


Route::get('test',function (){
  $obj = new  \App\YoutubeScrapper\ClientDashboard\YouTubeEducationalPlaylistGenerator\GeminiChatBot();
  $txt = "Generate 10 to 20 educational course titles related to the category 'البرمجه'. Return ONLY a JSON array of strings containing the titles. Do not include any markdown or extra text.";
  echo $obj->sendRequest($txt);
});
