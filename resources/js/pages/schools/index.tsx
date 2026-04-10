import { Link } from "@inertiajs/react"
import { Building2Icon, EditIcon, PlusCircleIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/SchoolController"
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
import type { PaginatedData, SchoolRowData } from "@/types"

type Props = {
  pagination: PaginatedData<SchoolRowData>
}

export default withLayout<Props>(
  ({ pagination }) => {
    return (
      <div className="space-y-4">
        <PageTitle>Écoles</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <Building2Icon className="text-primary" />
          Écoles
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom</TableHead>
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
    breadcrumb: [{ label: "Écoles", href: route.index() }],
    top: (
      <ButtonLink href={route.create.url()}>
        <PlusCircleIcon />
        Créer une école
      </ButtonLink>
    ),
  },
)

function Item({ item }: { item: SchoolRowData }) {
  const href = route.edit(item.id)

  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href}>{item.name}</Link>
      </TableCell>
      <TableCell className="text-right">
        <ButtonLink href={href} variant="secondary">
          <EditIcon />
        </ButtonLink>
      </TableCell>
    </TableRow>
  )
}
