<?php

namespace App\Infrastructure\Youtube;

class YoutubeScopes
{
    public const READONLY = [
        'https://www.googleapis.com/auth/youtube.readonly',
    ];

    public const UPLOAD = [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.upload',
    ];
}
