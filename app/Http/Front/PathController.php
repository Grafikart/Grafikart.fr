<?php

namespace App\Http\Front;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use App\Http\Controller;
use App\Http\Front\Data\CourseViewData;
use App\Http\Front\Data\FormationViewData;
use App\Http\Front\Data\PathViewData;
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

    public function show(string $slug, Path $path): View
    {
        $path->load(['nodes.parents']);

        return view('paths.show', [
            'path' => PathViewData::from($path),
        ]);
    }

    public function node(PathNode $node)
    {
        if ($node->content instanceof Formation) {
            $node->content->load('courses');

            return FormationViewData::from($node->content);
        }

        if ($node->content instanceof Course) {
            return CourseViewData::from($node->content);
        }
    }
}
