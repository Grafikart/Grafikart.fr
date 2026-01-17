<?php

namespace App\Http\Cms;

use App\Domains\Attachment\Attachment;
use App\Domains\Attachment\AttachmentRepository;
use App\Http\Cms\Data\Attachment\AttachmentFileData;
use App\Http\Cms\Data\Attachment\AttachmentUploadData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AttachmentController
{
    public function __construct(
        private readonly AttachmentRepository $repository,
    ) {}

    public function folders(): Collection
    {
        return $this->repository->findYearsMonths();
    }

    public function index(Request $request): Collection
    {
        $path = $request->query('path');
        $q = $request->query('q');

        if ($path !== null && ! preg_match('/^2\d{3}\/(1[0-2]|0[1-9])$/', $path)) {
            throw ValidationException::withMessages([
                'path' => ['Le format du chemin est invalide (attendu: YYYY/MM)'],
            ]);
        }

        if ($q === 'orphan') {
            $attachments = $this->repository->orphaned();
        } elseif (! empty($q)) {
            $attachments = $this->repository->search($q);
        } elseif ($path === null) {
            $attachments = $this->repository->findLatest();
        } else {
            $attachments = $this->repository->findForPath($path);
        }

        return AttachmentFileData::collect($attachments);
    }

    public function store(AttachmentUploadData $data): AttachmentFileData
    {
        $attachment = new Attachment;
        $data->toModel($attachment);
        $attachment->save();

        return AttachmentFileData::from($attachment);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        $attachment->delete();

        return response()->json([]);
    }
}
