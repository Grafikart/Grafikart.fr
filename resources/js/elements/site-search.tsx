import { Autocomplete } from "@base-ui/react/autocomplete"
import { QueryClientProvider } from "@tanstack/react-query"
import { useDebounce } from "@uidotdev/usehooks"
import { SearchIcon } from "lucide-react"
import { useState } from "react"
import {
  Dialog,
  DialogContent,
  DialogTrigger,
} from "@/components/ui/dialog.tsx"
import { Input } from "@/components/ui/input.tsx"
import { Spinner } from "@/components/ui/spinner.tsx"
import { queryClient, useApiFetch } from "@/hooks/use-api-fetch.ts"
import { useShortcut } from "@/hooks/use-shortcut.ts"
import type { APISearchItem, APISearchResponse } from "@/types"

export function SiteSearch() {
  return (
    <QueryClientProvider client={queryClient}>
      <SearchInputInner />
    </QueryClientProvider>
  )
}

function SearchInputInner() {
  const [open, setOpen] = useState(false)
  const [search, setSearch] = useState("")
  const debouncedSearch = useDebounce(search, 300)
  const { data, isFetching } = useApiFetch<APISearchResponse>(
    `/api/search?q=${encodeURIComponent(debouncedSearch)}`,
    {
      enabled: debouncedSearch.length > 1,
      staleTime: 5_000,
    },
  )

  const items = data?.items ?? []
  const iconCls =
    "absolute left-3 top-1/2 size-5 -translate-y-1/2 peer-focus:text-primary"
  useShortcut("K", { ctrlKey: true }, () => {
    setOpen(true)
  })

  const onOpenChange = (o: boolean) => {
    setOpen(o)
    if (o) {
      setSearch("")
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogTrigger
        className="grid size-6 place-items-center"
        title="Rechercher un contenu"
      >
        <SearchIcon className="size-4" />
      </DialogTrigger>
      <DialogContent
        className="w-150 max-w-9/10 bg-card sm:max-w-s2 mt-5 translate-y-0 gap-2 p-2"
        showCloseButton={false}
      >
        <Autocomplete.Root
          items={items}
          value={search}
          onValueChange={setSearch}
          itemToStringValue={(item) => item.title}
          filter={null}
        >
          <form className="relative" method="get" action="/recherche">
            <Autocomplete.Input
              placeholder="Rechercher un contenu"
              className="rounded-md pl-10 peer"
              name="q"
              render={<Input value={search} />}
            />
            {isFetching ? (
              <Spinner className={iconCls} />
            ) : (
              <SearchIcon className={iconCls} />
            )}
          </form>

          <Autocomplete.List hidden={items.length === 0}>
            {(item: APISearchItem) => (
              <Autocomplete.Item
                key={item.url}
                value={item}
                className="data-highlighted:bg-list-hover hover:bg-list-hover flex gap-4 p-2"
                render={<a href={item.url} />}
              >
                <div className="w-15 text-muted flex-none text-end">
                  {item.type}
                </div>
                <div
                  dangerouslySetInnerHTML={{
                    __html: item.title,
                  }}
                />
              </Autocomplete.Item>
            )}
          </Autocomplete.List>
          {data?.hits && (
            <a
              className="text-muted hover:bg-list-hover py-2 text-center text-sm"
              href={`/recherche?q=${encodeURIComponent(debouncedSearch)}`}
            >
              Voir les {data?.hits} résultats
            </a>
          )}
        </Autocomplete.Root>
      </DialogContent>
    </Dialog>
  )
}
