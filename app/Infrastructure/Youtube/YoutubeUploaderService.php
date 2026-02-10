<?php

namespace App\Infrastructure\Youtube;

use App\Domains\Course\Course;
use App\Domains\Course\Technology;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

/**
 * Uploads a video on YouTube from the server
 *
 * @property array{access_token: string, token_type: string, expires_in: int, refresh_token?: string, created: int} $accessToken
 */
class YoutubeUploaderService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public readonly int $courseId,
        public readonly array $accessToken,
    ) {}

    public function handle(\Google_Client $googleClient): void
    {
        ini_set('memory_limit', '4G');
        $course = Course::findOrFail($this->courseId);
        $googleClient->setAccessToken($this->accessToken);
        $youtube = new \Google_Service_YouTube($googleClient);
        $youtubeId = $course->youtube_id;
        $video = $this->youtubeVideo($course);
        $parts = 'snippet,status';
        if ($youtubeId) {
            $video = $youtube->videos->update($parts, $video);
        } else {
            $video = $youtube->videos->insert($parts, $video, $this->videoData($course));
            $course->update(['youtube_id' => $video->getId()]);
        }
        $youtube->thumbnails->set($video->getId(), $this->thumbnailData($course));
    }

    private function youtubeVideo(Course $course): \Google_Service_YouTube_Video
    {
        // Compute the title
        $youtubeId = $course->youtube_id;
        $title = preg_replace('/[<>]/', '', $course->title ?: '');
        $formation = $course->formation;
        if ($formation) {
            $title = "{$formation->title} : {$title}";
        } else {
            $technologies = $course->mainTechnologies->map(fn (Technology $t) => $t->name)->join('/');
            $title = "Tutoriel {$technologies} : {$title}";
        }

        // Build the object to send
        $video = new \Google_Service_YouTube_Video;
        $snippet = new \Google_Service_YouTube_VideoSnippet;
        $snippet->setCategoryId('28');
        $snippet->setDescription(view('courses.youtube-description', ['course' => $course])->toHtml());
        $snippet->setTitle($title);
        $snippet->setDefaultAudioLanguage('fr');
        $snippet->setDefaultLanguage('fr');
        $video->setSnippet($snippet);
        $status = new \Google_Service_YouTube_VideoStatus;
        $status->setEmbeddable(true);
        $status->setPublicStatsViewable(false);
        $status->setSelfDeclaredMadeForKids(false);
        if ($course->created_at->isFuture()) {
            $status->setPrivacyStatus('private');
            $status->setPublishAt($course->created_at->toIso8601String());
        } else {
            $status->setPrivacyStatus('public');
        }
        $video->setStatus($status);
        if ($youtubeId) {
            $video->setId($youtubeId);
        }

        return $video;
    }

    private function videoData(Course $course): array
    {
        return [
            'data' => Storage::disk('downloads')->get('videos/'.$course->video_path),
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart',
        ];
    }

    public function thumbnailData(Course $course): array
    {
        $thumbnail = $course->youtubeThumbnail?->mediaPath('name');
        if ($thumbnail === null) {
            throw new \RuntimeException('Impossible de résoudre la miniature pour cette vidéo');
        }

        return [
            'data' => file_get_contents($thumbnail),
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart',
        ];
    }
}
