import { Button } from "@/components/ui/button";
import { Link } from "@inertiajs/react";
import type { ReactNode } from "react";
import type { PaginatedData } from "@/types";

type Props = { pagination: PaginatedData<unknown> };

export function Pagination({ pagination }: Props) {
  return (
    <div className="flex items-center justify-between">
      <div className="hidden flex-1 text-sm text-muted-foreground lg:flex">
        Page {pagination.page} sur {pagination.last}
      </div>
      <nav role="navigation" aria-label="pagination">
        <ul className="flex flex-row items-center gap-1">
          {pagination.links.map((link, index) => (
            <li key={index}>
              <Button
                disabled={link.url === null}
                aria-current={link.page === pagination.page ? "page" : undefined}
                data-active={link.page === pagination.page}
                variant={link.page === pagination.page ? "ghost" : "outline"}
                render={<Link href={link.url ?? "#"} />}
              >
                {label(link.page.toString())}
              </Button>
            </li>
          ))}
        </ul>
      </nav>
    </div>
  );
}

function label(s: string): ReactNode {
  return s;
}
