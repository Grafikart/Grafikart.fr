import { Link } from "@inertiajs/react"
import { Button } from "@/components/ui/button"
import type { PaginatedData } from "@/types"

type Props = { pagination: PaginatedData<unknown> }

export function Pagination({ pagination }: Props) {
  if (pagination.last_page === 1) {
    return null
  }
  return (
    <div className="flex items-center justify-between">
      <div className="hidden flex-1 text-sm text-muted-foreground lg:flex">
        Page {pagination.current_page} sur {pagination.last_page}
      </div>
      <nav role="navigation" aria-label="pagination">
        <ul className="flex flex-row items-center gap-1">
          {pagination.links.map((link, index) => (
            <li key={index}>
              <Button
                disabled={link.url === null}
                aria-current={link.active ? "page" : undefined}
                data-active={link.active}
                variant={link.active ? "ghost" : "outline"}
                render={<Link href={link.url ?? "#"} />}
                nativeButton={false}
                dangerouslySetInnerHTML={{
                  __html: link.label.toString(),
                }}
              />
            </li>
          ))}
        </ul>
      </nav>
    </div>
  )
}
