# Instruction pour créer un nouveau panneau d'administration

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

function Item({ item }: { item: CourseListItemData }) {
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
