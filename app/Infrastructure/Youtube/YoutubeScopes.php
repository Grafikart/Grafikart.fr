<?php

namespace App\Infrastructure\Youtube;

class YoutubeScopes
{
    final public const READONLY = [
        'https://www.googleapis.com/auth/youtube.readonly',
    ];

    final public const UPLOAD = [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.upload',
    ];
}
