<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeEducationalPlaylist extends Model
{
    protected $fillable = [
        'yt_playlist_id',
        'title',
        'description',
        'thumbnail',
        'channel_name',
        'category'
    ];
}
