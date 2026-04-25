import { Link } from "@inertiajs/react"
import {
  EditIcon,
  HandshakeIcon,
  PlusCircleIcon,
  TrashIcon,
} from "lucide-react"
import route from "@/actions/App/Http/Cms/SponsorController"
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
import type { PaginatedData, SponsorRowData } from "@/types"

type Props = {
  pagination: PaginatedData<SponsorRowData>
}

export default withLayout<Props>(
  ({ pagination }) => {
    return (
      <div className="space-y-4">
        <PageTitle>Sponsors</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <HandshakeIcon className="text-primary" />
          Sponsors
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>URL</TableHead>
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
    breadcrumb: [{ label: "Sponsors", href: route.index() }],
    top: (
      <ButtonLink href={route.create()}>
        <PlusCircleIcon />
        Créer un sponsor
      </ButtonLink>
    ),
  },
)

function Item({ item }: { item: SponsorRowData }) {
  const href = route.edit(item.id)

  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href}>{item.name}</Link>
      </TableCell>
      <TableCell>{item.type}</TableCell>
      <TableCell className="text-muted-foreground">
        <a href={item.url} target="_blank" rel="noreferrer">
          {item.url}
        </a>
      </TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            <ButtonLink
              variant="destructive"
              confirm="Voulez vous vraiment supprimer ce sponsor ?"
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
