<?php

namespace App\Http\Front;

use App\Domains\Forum\Topic;
use App\Domains\Forum\TopicTag;
use App\Http\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function topic(Topic $topic)
    {
        return view('forum.topic', [
            'topic' => $topic,
        ]);
    }

    public function index(Request $request)
    {
        return $this->renderForumIndex(null, $request);
    }

    public function tag(string $slug, TopicTag $tag, Request $request)
    {
        return $this->renderForumIndex($tag, $request);
    }

    private function renderForumIndex(?TopicTag $selectedTag, Request $request)
    {
        $query = Topic::orderByDesc('created_at')->where('messages_count', '>', 0);

        if ($selectedTag) {
            $query->whereHas('tags', fn (Builder $builder) => $builder->where('id', $selectedTag->id));
        }

        $topics = $query->paginate(20);
        $tags = TopicTag::whereNull('parent_id')->where('visible', true)->get();

        return view('forum.index', [
            'topics' => $topics,
            'tags' => $tags,
            'selectedTag' => $selectedTag,
            'page' => $request->integer('page', 1),
        ]);
    }
}
