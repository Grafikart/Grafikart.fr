import { Autocomplete } from "@base-ui/react/autocomplete"
import { useDebounce } from "@uidotdev/usehooks"
import { type ComponentProps, useState } from "react"
import UserController from "@/actions/App/Http/Cms/UserController.ts"
import { Input } from "@/components/ui/input.tsx"
import { useApiFetch } from "@/hooks/use-api-fetch.ts"
import { cn } from "@/lib/utils"
import type { OptionItemData } from "@/types"
import { Card } from "../card"

type Props = {
  defaultValue?: number
  defaultName?: string
  name?: string
} & ComponentProps<"div">

export function UserSelector({
  className,
  defaultValue,
  defaultName,
  name = "userId",
  ...props
}: Props) {
  const [label, setLabel] = useState(name)
  const [value, setValue] = useState(defaultValue)
  const [search, setSearch] = useState(defaultName ?? "")
  const debouncedSearch = useDebounce(search, 300)
  const shouldSearch = Boolean(debouncedSearch && debouncedSearch !== label)

  const { data, isFetching } = useApiFetch<OptionItemData[]>(
    UserController.search({
      query: {
        q: debouncedSearch,
      },
    }).url,
    {
      enabled: shouldSearch,
      staleTime: 5_000,
    },
  )

  const users = data ?? []

  const selectUser = (u: OptionItemData) => {
    setLabel(u.name)
    setValue(u.id)
  }

  return (
    <div className={cn("relative", className)} {...props}>
      <input type="hidden" name={name} value={value} />

      <Autocomplete.Root
        items={users}
        value={search}
        onValueChange={(value) => {
          setSearch(value)
          console.log("onValueChange", value)
        }}
        itemToStringValue={(user) => user.name}
        filter={null}
      >
        <Autocomplete.Input
          render={<Input />}
          placeholder="Rechercher un utilisateur"
        />
        <Autocomplete.Portal>
          <Autocomplete.Positioner
            className="outline-hidden"
            sideOffset={4}
            align="start"
          >
            <Autocomplete.Popup
              aria-busy={isFetching || undefined}
              render={<Card className="p-2" />}
            >
              <Autocomplete.List>
                {(user: OptionItemData) => (
                  <Autocomplete.Item
                    key={user.id}
                    value={user}
                    onClick={() => {
                      selectUser(user)
                    }}
                    className="flex gap-4 items-center justify-between data-highlighted:bg-muted p-1 rounded-md cursor-pointer"
                  >
                    <div>{user.name}</div>
                    <div className="text-muted-foreground text-sm">
                      #{user.id}
                    </div>
                  </Autocomplete.Item>
                )}
              </Autocomplete.List>
            </Autocomplete.Popup>
          </Autocomplete.Positioner>
        </Autocomplete.Portal>
      </Autocomplete.Root>
    </div>
  )
}
