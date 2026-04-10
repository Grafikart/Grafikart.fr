import { Link } from "@inertiajs/react"
import { EditIcon, MedalIcon, PlusCircleIcon, TrashIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/BadgeController"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { ButtonGroup } from "@/components/ui/button-group.tsx"
import { ButtonLink } from "@/components/ui/button-link.tsx"
import { Pagination } from "@/components/ui/pagination.tsx"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table.tsx"
import type { BadgeRowData, PaginatedData } from "@/types"

type Props = {
  pagination: PaginatedData<BadgeRowData>
}

export default withLayout<Props>(
  ({ pagination }) => {
    return (
      <div className="space-y-4">
        <PageTitle>Badges</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <MedalIcon className="text-primary" />
          Badges
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nom</TableHead>
              <TableHead>Action</TableHead>
              <TableHead className="w-32">Count</TableHead>
              <TableHead className="text-end">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {pagination.data.map((item) => (
              <Item item={item} key={item.id} />
            ))}
          </TableBody>
        </Table>
        <Pagination pagination={pagination} />
      </div>
    )
  },
  {
    breadcrumb: [{ label: "Badges", href: route.index() }],
    top: (
      <ButtonLink href={route.create()}>
        <PlusCircleIcon />
        Créer un badge
      </ButtonLink>
    ),
  },
)

function Item({ item }: { item: BadgeRowData }) {
  const href = route.edit(item.id)

  return (
    <TableRow className="group">
      <TableCell>
        <Link href={href}>{item.name}</Link>
      </TableCell>
      <TableCell className="text-muted-foreground">{item.action}</TableCell>
      <TableCell>{item.actionCount}</TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            <ButtonLink
              variant="destructive"
              confirm="Voulez vous vraiment supprimer ce badge ?"
              href={route.destroy(item.id)}
            >
              <TrashIcon />
            </ButtonLink>
          </ButtonGroup>
          <ButtonLink href={href} variant="secondary">
            <EditIcon />
          </ButtonLink>
        </div>
      </TableCell>
    </TableRow>
  )
}
