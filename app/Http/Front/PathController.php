<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use App\Domains\History\ProgressionService;
use App\Http\Controller;
use App\Http\Front\Data\CourseViewData;
use App\Http\Front\Data\FormationViewData;
use App\Http\Front\Data\PathViewData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PathController extends Controller
{
    public function index(): View
    {
        $paths = Path::query()
            ->published()
            ->with(['nodes.parents'])
            ->get();

        return view('paths.index', [
            'paths' => PathViewData::collect($paths),
        ]);
    }

    public function show(string $slug, Path $path, ProgressionService $progressionService): View
    {
        $cacheKey = 'path.show.'.cache_key($path);

        return view('paths.show', [
            'path' => cache()->remember($cacheKey, 3600, fn () => PathViewData::from($path->load('nodes.parents'))),
            'completedNodeIds' => $progressionService->completedNodeIds($path),
        ]);
    }

    public function node(Request $request, PathNode $node)
    {
        if ($node->content instanceof Formation) {
            $node->content->load('courses');

            return FormationViewData::from($node->content);
        }

        if ($node->content instanceof Course) {
            return CourseViewData::fromModel($node->content, $request->user());
        }
    }
}
