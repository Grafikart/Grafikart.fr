<?php

namespace App\Http\Admin\Controller;

use App\Domain\Course\Entity\Technology;
use App\Http\Admin\Data\Technology\TechnologyFormData;
use App\Http\Admin\Data\Technology\TechnologyFormInput;
use App\Http\Admin\Data\Technology\TechnologyItemData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route(path: '/technologies', name: 'technology_')]
final class TechnologyController extends InertiaController
{
    protected string $entityClass = Technology::class;
    protected string $routePrefix = 'technology';
    protected string $componentDirectory = 'technologies';
    protected string $itemDataClass = TechnologyItemData::class;
    protected string $formDataClass = TechnologyFormData::class;
    protected string $inputDataClass = TechnologyFormInput::class;

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $query = $this->em->createQuery(sprintf(
            'SELECT NEW %s(row.id, row.name, row.image, COUNT(u.content))
            FROM %s row
            LEFT JOIN row.usages u
            GROUP BY row.id
            ORDER BY row.name ASC',
            TechnologyItemData::class,
            Technology::class
        ));

        $pagination = $this->paginator->paginate($query);

        return $this->renderComponent('technologies/index', [
            'pagination' => $pagination,
        ], [
            'item' => TechnologyItemData::class,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['GET'])]
    public function edit(Technology $technology): Response
    {
        return $this->crudEdit($technology);
    }

    #[Route(path: '/{id<\d+>}', name: 'update', methods: ['POST'])]
    public function update(
        Technology $technology,
        #[MapRequestPayload] TechnologyFormInput $data,
        #[MapUploadedFile] ?UploadedFile $imageFile,
    ): Response {
        $technology->setImageFile($imageFile);
        $technology->setUpdatedAt(new \DateTimeImmutable());

        return $this->crudUpdate($data, $technology);
    }

    #[Route(path: '/new', name: 'create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->crudCreate();
    }

    #[Route(path: '/new', name: 'store', methods: ['POST'])]
    public function store(
        #[MapRequestPayload] TechnologyFormInput $data,
        #[MapUploadedFile] ?UploadedFile $imageFile,
    ): Response {
        $technology = new Technology();
        $technology->setImageFile($imageFile);
        $technology->setUpdatedAt(new \DateTimeImmutable());

        return $this->crudStore($data, $technology);
    }
}
