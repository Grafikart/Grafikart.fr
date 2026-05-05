<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Course\Course;
use App\Http\Cms\Data\Course\CourseFormData;
use App\Http\Cms\Data\Course\CourseRequestData;
use App\Http\Cms\Data\Course\CourseRowData;
use App\Http\Cms\Data\OptionItemData;
use App\Infrastructure\Youtube\YoutubeScopes;
use App\Infrastructure\Youtube\YoutubeUploaderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CourseController extends CmsController
{
    protected string $componentPath = 'courses';

    protected string $model = Course::class;

    protected string $rowData = CourseRowData::class;

    protected string $formData = CourseFormData::class;

    protected string $requestData = CourseRequestData::class;

    protected string $route = 'courses';

    public function index(Request $request): Response
    {
        $query = Course::query()
            ->with('technologies')
            ->orderByDesc('created_at');

        if ($request->has('q')) {
            $search = $request->string('q');
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
        if ($request->has('technology')) {
            $query->whereHas('technologies', function (Builder $builder) use ($request) {
                $builder->where('id', $request->query->getInt('technology'));
            });
        }

        return $this->cmsIndex(query: $query);
    }

    public function create(Request $request): Response
    {
        $clone = $request->query->getInt('clone', 0);
        // Create a clone to copy a course
        if ($clone) {
            $course = clone Course::findOrFail($clone);
            $course->load('technologies');
            $course->created_at = $course->created_at->addDay();
            $course->source = null;
            $course->youtube_id = null;
            $course->setAttribute('id', null);

            return $this->cmsCreate(['item' => CourseFormData::from($course)]);
        }

        return $this->cmsCreate();
    }

    public function store(CourseRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json(new OptionItemData(
            id: $course->id,
            name: $course->title
        ));
    }

    public function edit(Course $course): Response
    {
        $course->load(['attachment', 'youtubeThumbnail', 'technologies'])->loadCount('questions');

        return $this->cmsEdit($course);
    }

    public function update(Course $course, CourseRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $course, data: $data);
    }

    public function destroy(Course $course): RedirectResponse
    {
        return $this->cmsDestroy($course, "Le cours {$course->title} a été supprimé");
    }

    public function upload(\Google_Client $client, \Illuminate\Http\Request $request): RedirectResponse
    {
        // Use ?state to get course ID since this is the only parameter accepted by google Oauth
        $courseId = $request->get('state');
        $redirectUri = route('cms.courses.upload', absolute: true);
        $code = $request->get('code');
        $client->setRedirectUri($redirectUri);
        if (! $code) {
            return redirect($client->createAuthUrl(YoutubeScopes::UPLOAD, [
                'state' => $courseId,
            ]));
        }

        // We received an auth code
        $client->fetchAccessTokenWithAuthCode($code);
        $accessToken = $client->getAccessToken();
        if (! $accessToken) {
            return to_route('cms.courses.edit', $courseId)->with('error', 'Impossible de récupérer le token d\'accès');
        }
        dispatch(new YoutubeUploaderService($courseId, $client->getAccessToken()));

        return to_route('cms.courses.edit', $courseId)->with('success', "La vidéo est en cours d'upload sur YouTube");
    }
}
