import { EyeIcon } from "lucide-react"
import { show } from "@/actions/App/Http/Cms/RevisionController.ts"
import { Badge } from "@/components/ui/badge.tsx"
import { ButtonLink } from "@/components/ui/button-link.tsx"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table.tsx"
import { formatRelative } from "@/lib/date.ts"
import type { RevisionRowData, RevisionStatus } from "@/types"

const stateLabels: Record<
  RevisionStatus,
  { label: string; variant: "default" | "secondary" | "destructive" }
> = {
  0: { label: "En attente", variant: "default" },
  1: { label: "Acceptée", variant: "secondary" },
  [-1]: { label: "Rejetée", variant: "destructive" },
}

export function RevisionsTable({ items }: { items: RevisionRowData[] }) {
  return (
    <Table className="border">
      <TableHeader>
        <TableRow>
          <TableHead className="w-12">#</TableHead>
          <TableHead>Auteur</TableHead>
          <TableHead>Cible</TableHead>
          <TableHead>Type</TableHead>
          <TableHead>Statut</TableHead>
          <TableHead>Date</TableHead>
          <TableHead className="w-10" />
        </TableRow>
      </TableHeader>
      <TableBody>
        {items.map((item) => (
          <TableRow key={item.id}>
            <TableCell>{item.id}</TableCell>
            <TableCell>{item.authorName}</TableCell>
            <TableCell>{item.targetTitle}</TableCell>
            <TableCell className="capitalize">{item.targetType}</TableCell>
            <TableCell>
              <Badge variant={stateLabels[item.state].variant}>
                {stateLabels[item.state].label}
              </Badge>
            </TableCell>
            <TableCell className="text-muted-foreground">
              {formatRelative(item.createdAt)}
            </TableCell>
            <TableCell>
              <ButtonLink variant="secondary" size="icon" href={show(item.id)}>
                <EyeIcon />
              </ButtonLink>
            </TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  )
}
