import { Link } from "@inertiajs/react"
import {
  CheckCircle2Icon,
  CircleXIcon,
  EditIcon,
  LifeBuoyIcon,
  TrashIcon,
} from "lucide-react"
import route from "@/actions/App/Http/Cms/SupportController"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Badge } from "@/components/ui/badge.tsx"
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
import type { PaginatedData, SupportQuestionRowData } from "@/types"

type Props = {
  pagination: PaginatedData<SupportQuestionRowData>
}

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Support</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <LifeBuoyIcon className="text-primary" />
          Questions de support
        </h1>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">ID</TableHead>
              <TableHead>Question</TableHead>
              <TableHead>Cours</TableHead>
              <TableHead>Etat</TableHead>
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
    breadcrumb: [{ label: "Support", href: route.index() }],
  },
)

function Item({ item }: { item: SupportQuestionRowData }) {
  const href = route.edit(item.id)

  return (
    <TableRow className="group">
      <TableCell className="text-muted-foreground">{item.id}</TableCell>
      <TableCell>
        <Link href={href} className="font-medium">
          {item.title}
        </Link>
      </TableCell>
      <TableCell>{item.courseTitle}</TableCell>
      <TableCell>
        <ItemBadge item={item} />
      </TableCell>
      <TableCell className="text-muted-foreground">
        {formatDate(item.createdAt)}
      </TableCell>
      <TableCell className="text-right">
        <div className="flex justify-end">
          <ButtonGroup className="opacity-0 group-hover:opacity-100">
            <ButtonLink
              variant="destructive"
              href={route.destroy(item.id)}
              confirm="Voulez vous vraiment supprimer cette question ?"
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

function ItemBadge({ item }: { item: SupportQuestionRowData }) {
  if (item.online) {
    return <CheckCircle2Icon className="size-4 fill-success text-card" />
  }
  if (item.answered) {
    return <CircleXIcon className="size-4 fill-ring text-card" />
  }
  return <Badge variant="secondary">Sans réponse</Badge>
}
