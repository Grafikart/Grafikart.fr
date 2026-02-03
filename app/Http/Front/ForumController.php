<?php

namespace App\Http\Front;

use App\Domains\Forum\Topic;
use App\Http\Controller;

class ForumController extends Controller
{
    public function show(Topic $topic)
    {
        return view('forum.topic', [
            'topic' => $topic,
        ]);
    }
}
