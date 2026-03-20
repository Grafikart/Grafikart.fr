import { Link } from "@inertiajs/react"
import { EditIcon, MapIcon, PlusCircleIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/PathController"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
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
import type { PaginatedData, PathRowData } from "@/types"

type Props = {
  pagination: PaginatedData<PathRowData>
}

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Parcours</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <MapIcon className="text-primary" />
          Parcours
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Titre</TableHead>
              <TableHead>Description</TableHead>
              <TableHead>Tags</TableHead>
              <TableHead className="text-end">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {props.pagination.data.map((item) => (
              <Item item={item} key={item.id} />
            ))}
          </TableBody>
        </Table>
        <Pagination pagination={props.pagination} />
      </div>
    )
  },
  {
    breadcrumb: [{ label: "Parcours", href: route.index() }],
    top: (
      <ButtonLink href={route.create.url()}>
        <PlusCircleIcon />
        Créer un parcours
      </ButtonLink>
    ),
  },
)

function Item({ item }: { item: PathRowData }) {
  const href = route.edit(item.id)
  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href}>{item.title}</Link>
      </TableCell>
      <TableCell className="max-w-md truncate text-muted-foreground">
        {item.description}
      </TableCell>
      <TableCell className="max-w-md truncate text-muted-foreground">
        {item.tags}
      </TableCell>
      <TableCell className="text-right">
        <ButtonLink href={href} variant="secondary">
          <EditIcon />
        </ButtonLink>
      </TableCell>
    </TableRow>
  )
}
