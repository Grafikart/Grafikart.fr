import { GitCompareArrowsIcon } from "lucide-react"
import { Link } from "@inertiajs/react"
import { index } from "@/actions/App/Http/Cms/RevisionController.ts"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Pagination } from "@/components/ui/pagination.tsx"
import { RevisionsTable } from "@/components/revisions/revisions-table.tsx"
import { cn } from "@/lib/utils.ts"
import type { PaginatedData, RevisionRowData } from "@/types"

type Props = {
  pagination: PaginatedData<RevisionRowData>
  state: string
}

const filters = [
  { label: "En attente", value: "pending" },
  { label: "Toutes", value: "all" },
  { label: "Acceptées", value: "accepted" },
  { label: "Rejetées", value: "rejected" },
]

export default withLayout<Props>(
  (props) => {
    return (
      <div className="space-y-4">
        <PageTitle>Révisions</PageTitle>
        <h1 className="flex items-center gap-2 font-semibold text-xl">
          <GitCompareArrowsIcon className="text-primary" />
          Révisions
        </h1>
        <div className="flex gap-1">
          {filters.map((filter) => (
            <Link
              key={filter.value}
              href={index.url({ query: { state: filter.value } })}
              className={cn(
                "rounded-md px-3 py-1.5 text-sm font-medium transition-colors",
                props.state === filter.value
                  ? "bg-primary text-primary-foreground"
                  : "bg-muted text-muted-foreground hover:text-foreground",
              )}
            >
              {filter.label}
            </Link>
          ))}
        </div>
        <RevisionsTable items={props.pagination.data} />
        <Pagination pagination={props.pagination} />
      </div>
    )
  },
  {
    breadcrumb: [{ label: "Révisions", href: index() }],
  },
)
