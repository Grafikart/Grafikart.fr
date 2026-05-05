<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Support\ContactRequest;
use App\Http\Cms\Data\ContactRequest\ContactRequestRowData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ContactRequestController extends CmsController
{
    protected string $componentPath = 'contact-requests';

    protected string $model = ContactRequest::class;

    protected string $rowData = ContactRequestRowData::class;

    protected string $searchField = 'email';

    protected string $route = 'contact_requests';

    public function index(): Response
    {
        $query = ContactRequest::query()->orderByDesc('id');

        return $this->cmsIndex(query: $query);
    }

    public function show(ContactRequest $contactRequest): Response
    {
        return \Inertia\Inertia::render('contact-requests/show', [
            'item' => ContactRequestRowData::fromModel($contactRequest),
        ]);
    }

    public function destroy(ContactRequest $contactRequest): RedirectResponse
    {
        return $this->cmsDestroy($contactRequest, 'La demande de contact a bien été supprimée');
    }
}
