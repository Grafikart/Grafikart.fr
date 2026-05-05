import { Link } from "@inertiajs/react"
import { MailIcon, TrashIcon } from "lucide-react"
import {
  index,
  destroy,
  show,
} from "@/actions/App/Http/Cms/ContactRequestController"
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
import { formatDate } from "@/lib/date.ts"
import type { PaginatedData, ContactRequestRowData } from "@/types"

type Props = {
  pagination: PaginatedData<ContactRequestRowData>
}

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Demandes de contact</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <MailIcon className="text-primary" />
          Demandes de contact
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Nom</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Message</TableHead>
              <TableHead>Date</TableHead>
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
    breadcrumb: [{ label: "Demandes de contact", href: index() }],
  },
)

function Item({ item }: { item: ContactRequestRowData }) {
  const href = show(item.id)

  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href} className="font-medium hover:underline">
          {item.name}
        </Link>
      </TableCell>
      <TableCell>
        <a
          href={`mailto:${item.email}`}
          className="font-medium hover:underline"
        >
          {item.email}
        </a>
      </TableCell>
      <TableCell className="max-w-xs truncate text-muted-foreground">
        <Link href={href} className="hover:underline">
          {item.message}
        </Link>
      </TableCell>
      <TableCell className="text-muted-foreground">
        {formatDate(item.createdAt)}
      </TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            <ButtonLink
              variant="destructive"
              href={destroy(item.id)}
              confirm="Voulez vous vraiment supprimer cette demande de contact ?"
            >
              <TrashIcon />
            </ButtonLink>
          </ButtonGroup>
          <ButtonLink href={href} variant="secondary">
            <MailIcon />
          </ButtonLink>
        </div>
      </TableCell>
    </TableRow>
  )
}
