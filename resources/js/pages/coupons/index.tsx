import { Link } from "@inertiajs/react"
import { EditIcon, PlusCircleIcon, TicketIcon, TrashIcon } from "lucide-react"
import route from "@/actions/App/Http/Cms/CouponController"
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
import type { CouponRowData, PaginatedData } from "@/types"

type Props = {
  pagination: PaginatedData<CouponRowData>
}

export default withLayout<Props>(
  ({ pagination }) => {
    return (
      <div className="space-y-4">
        <PageTitle>Coupons</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <TicketIcon className="text-primary" />
          Coupons
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Code</TableHead>
              <TableHead>Email</TableHead>
              <TableHead className="w-24">Mois</TableHead>
              <TableHead>Réclamé</TableHead>
              <TableHead>Créé</TableHead>
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
    breadcrumb: [{ label: "Coupons", href: route.index() }],
    top: (
      <ButtonLink href={route.create()}>
        <PlusCircleIcon />
        Créer un coupon
      </ButtonLink>
    ),
  },
)

function Item({ item }: { item: CouponRowData }) {
  const href = route.edit(item.id)

  return (
    <TableRow className="group">
      <TableCell>
        <Link href={href}>{item.id}</Link>
      </TableCell>
      <TableCell className="text-muted-foreground">
        {item.email || "—"}
      </TableCell>
      <TableCell>{item.months}</TableCell>
      <TableCell>
        {item.claimedAt ? formatDate(item.claimedAt) : "Non réclamé"}
      </TableCell>
      <TableCell>{formatDate(item.createdAt)}</TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            <ButtonLink
              variant="destructive"
              confirm="Voulez vous vraiment supprimer ce coupon ?"
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
