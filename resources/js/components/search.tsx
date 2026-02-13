import { useDebounce } from "@uidotdev/usehooks"
import {
  BookOpenTextIcon,
  ListVideoIcon,
  MonitorPlayIcon,
  SearchIcon,
  UserIcon,
} from "lucide-react"
import { useState } from "react"
import SearchController from "@/actions/App/Http/Cms/SearchController.ts"
import {
  Command,
  CommandDialog,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command"
import { Spinner } from "@/components/ui/spinner.tsx"
import { useApiFetch } from "@/hooks/use-api-fetch.ts"
import { useShortcut } from "@/hooks/use-shortcut.ts"
import type { SearchResultData } from "@/types"

export function Search() {
  const [open, setOpen] = useState(false)
  useShortcut("K", { ctrlKey: true }, () => {
    setOpen(true)
  })
  useShortcut("Escape", { when: open }, () => {
    setOpen(false)
  })

  return (
    <CommandDialog
      open={open}
      onOpenChange={setOpen}
      className="top-10 w-full max-w-100 translate-y-0"
    >
      <SearchCommand />
    </CommandDialog>
  )
}

export function SearchCommand(props: {
  onSelect?: (r: SearchResultData) => void
}) {
  const [search, setSearch] = useState("")
  const debouncedSearch = useDebounce(search, 300)
  const { data, isFetching } = useApiFetch<SearchResultData[]>(
    SearchController.search({ query: { q: debouncedSearch } }).url,
    {
      enabled: debouncedSearch.length > 1 && !debouncedSearch.endsWith(":"),
      staleTime: 5_000,
    },
  )
  const items = data ?? []
  return (
    <Command shouldFilter={false} onSelect={console.log} className="relative">
      <CommandInput
        placeholder="Recherche un contenu"
        onValueChange={setSearch}
        autoFocus
      />
      <CommandList>
        {isFetching && (
          <div className="absolute top-1 right-3 flex justify-center py-2 text-muted-foreground">
            <Spinner />
          </div>
        )}
        {data && (
          <CommandGroup heading="Résultats">
            {items.map((item) => (
              <CommandItem
                className="cursor-pointer"
                key={item.id}
                value={item.id.toString()}
                onSelect={() => props.onSelect?.(item)}
              >
                {props.onSelect ? (
                  <>
                    <ResultIcon
                      type={item.type}
                      className="size-3 text-muted-foreground"
                    />
                    {item.name}
                  </>
                ) : (
                  <a href={item.url} className="flex items-center gap-2">
                    <ResultIcon
                      type={item.type}
                      className="size-3 text-muted-foreground"
                    />
                    {item.name}
                  </a>
                )}
              </CommandItem>
            ))}
          </CommandGroup>
        )}
      </CommandList>
    </Command>
  )
}

function ResultIcon(props: { type: string; className: string }) {
  switch (props.type) {
    case "course":
      return <MonitorPlayIcon className={props.className} />
    case "post":
      return <BookOpenTextIcon className={props.className} />
    case "formation":
      return <ListVideoIcon className={props.className} />
    case "user":
      return <UserIcon className={props.className} />
    default:
      return <SearchIcon className={props.className} />
  }
}
