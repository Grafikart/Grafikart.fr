---
name: admin-crud
description: Create admin panel CRUD interfaces with Inertia.js. Use when creating new admin pages, admin listings, admin forms, or admin controllers for managing entities.
---

# Instructions pour créer un nouveau panneau d'administration

Voici les instructions à suivre pour créer une interface d'administration.

## Pour le listing

Commence par créer un objet Data qui va représenter les données qui seront envoyées au listing (ex: CourseItemData). Cet objet est placé dans le namespace `App\Http\Admin\Data` et ne contient aucune logique, juste les champs nécessaires

```php
<?php

namespace App\Http\Admin\Data\Course;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Transform\MapCollectionTransformer;
use App\Component\ObjectMapper\Transform\UrlTransformer;
use DateTimeImmutable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class CourseItemData
{

    public function __construct(
        public int    $id,
        public string $title,
    )
    {
    }

}
```

Ensuite crée le controller correspondant

```php
<?php

namespace App\Http\Admin\Controller;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Repository\CourseRepository;
use App\Http\Admin\Data\Course\CourseItemData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method getRepository() App\Domain\Course\Repository\CourseRepository\CourseRepository
 */
#[Route(path: '/courses', name: 'course_')]
final class CourseController extends InertiaController
{
    protected string $entityClass = Course::class;
    protected string $routePrefix = 'course';
    protected string $componentDirectory = 'courses';
    protected string $itemDataClass = CourseItemData::class;

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->addSelect('tu', 't')
            ->leftJoin('row.technologyUsages', 'tu')
            ->leftJoin('tu.technology', 't')
            ->orderBy('row.createdAt', 'DESC');

        return $this->crudIndex($query);
    }}
}
```

A cette étape là lance la commande `docker compose exec php php bin/console app:ts` pour générer les types.

Ensuite crée le composant inertia correspondant, tu peux t'inspirer du composant courses/index.tsx mais voila une structure de base

```tsx
import type { CourseItemData, PaginatedData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { PageTitle } from "@/components/page-title.tsx";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table.tsx";
import { ButtonLink } from "@/components/ui/button.tsx";
import { CheckCircle2Icon, CircleXIcon, CopyIcon, EditIcon, TrashIcon } from "lucide-react";
import { ButtonGroup } from "@/components/ui/button-group.tsx";
import { adminPath } from "@/lib/url.ts";
import { Pagination } from "@/components/ui/pagination.tsx";
import { formatDate } from "@/lib/date.ts";
import { Fragment } from "react";

type Props = {
  pagination: PaginatedData<CourseItemData>;
};

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Tutoriels</PageTitle>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom</TableHead>
              <TableHead>Publication</TableHead>
              <TableHead className="text-end">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {props.pagination.items.map((item) => (
              <Item item={item} key={item.id} />
            ))}
          </TableBody>
        </Table>
        <Pagination pagination={props.pagination} />
      </div>
    );
  },
  {
    breadcrumb: [{ label: "Tutoriel", href: adminPath("courses") }],
  },
);

function Item({ item }: { item: CourseItemData }) {
  const href = adminPath(`/courses/${item.id}`);
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>{item.title}</TableCell>
      <TableCell>{formatDate(item.createdAt)}</TableCell>
      <TableCell className="text-right">
        <ButtonLink href={href} variant="secondary">
          <EditIcon />
        </ButtonLink>
      </TableCell>
    </TableRow>
  );
}
```

## Pour l'édition / création

Pour la partie formulaire on sépare les données qui servent à l'affichage (FormData) des données qui sont reçues par le serveur (FormInput).

### 1. Création du DTO d'affichage (FormData)

Cet objet représente les données envoyées au composant React pour remplir le formulaire. Il doit être marqué avec l'attribut `#[TypeScript]`.

```php
<?php

namespace App\Http\Admin\Data\Course;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class CourseFormData
{
    public function __construct(
        public string $title = '',
        public string $content = '',
        public bool $online = false,
    ) {
    }
}
```

### 2. Création du DTO de réception (FormInput)

Cet objet représente les données envoyées par le formulaire. Il contient les contraintes de validation et les attributs de mapping.

```php
<?php

namespace App\Http\Admin\Data\Course;

use App\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\NotBlank;

readonly class CourseFormInput
{
    public function __construct(
        #[Map]
        #[NotBlank]
        public string $title,
        #[Map]
        #[NotBlank]
        public string $content,
        #[Map]
        public bool $online,
    ) {
    }
}
```

### 3. Mise à jour du Controller

Ajoute les propriétés `$formDataClass` et `$inputDataClass` ainsi que les méthodes `edit`, `update`, `create` et `store`.

```php
final class CourseController extends InertiaController
{
    // ...
    protected string $formDataClass = CourseFormData::class;
    protected string $inputDataClass = CourseFormInput::class;

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['GET'])]
    public function edit(Course $course): Response
    {
        return $this->crudEdit($course);
    }

    #[Route(path: '/{id<\d+>}', name: 'update', methods: ['POST'])]
    public function update(Course $course, #[MapRequestPayload] CourseFormInput $data): Response
    {
        return $this->crudUpdate($data, $course);
    }

    #[Route(path: '/new', name: 'create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->crudCreate();
    }

    #[Route(path: '/new', name: 'store', methods: ['POST'])]
    public function store(#[MapRequestPayload] CourseFormInput $data): Response
    {
        return $this->crudStore($data, new Course());
    }
}
```

N'oublie pas de relancer `docker compose exec php php bin/console app:ts` pour générer les types.

### 4. Création de la vue React

Crée le fichier `form.tsx` dans le dossier correspondant (ex: `assets/pages/courses/form.tsx`).

```tsx
import type { CourseFormData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { PageTitle } from "@/components/page-title.tsx";
import { adminPath } from "@/lib/url.ts";
import { Button } from "@/components/ui/button.tsx";
import { Form } from "@/components/form.tsx";
import { FormField } from "@/components/form-field.tsx";
import { Card, CardContent } from "@/components/ui/card.tsx";
import { SaveIcon } from "lucide-react";
import { Input } from "@/components/ui/input.tsx";
import { Switch } from "@/components/ui/switch.tsx";

type Props = {
  item: CourseFormData;
};

export default withLayout<Props>(
  ({ item }) => {
    return (
      <Form className="grid grid-cols-[1fr_300px] gap-4" id="form" method="post">
        <PageTitle>{item.name}</PageTitle>
        <Card>
          <CardContent className="space-y-4 pt-4">
             <FormField label="Titre" name="title" defaultValue={item.title} />
             <FormField label="Contenu" name="content" defaultValue={item.content} type="textarea" />
          </CardContent>
        </Card>
        <aside className="space-y-4">
           <Card>
             <CardContent className="pt-4">
                <div className="flex items-center space-x-2">
                  <Switch id="online" name="online" defaultChecked={item.online} />
                  <label htmlFor="online">En ligne</label>
                </div>
             </CardContent>
           </Card>
        </aside>
      </Form>
    );
  },
  {
    breadcrumb: (props) => [
      { label: "Tutoriels", href: adminPath("/courses") },
      { label: props.item.title || "Nouveau tutoriel" }
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
);
```
