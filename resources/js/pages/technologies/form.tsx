import { useDebounce } from "@uidotdev/usehooks"
import { Check, LoaderCircle, SaveIcon, UnlinkIcon } from "lucide-react"
import { useMemo, useState } from "react"
import route from "@/actions/App/Http/Cms/TechnologyController"
import { Form } from "@/components/form.tsx"
import { withLayout } from "@/components/layout.tsx"
import { PageTitle } from "@/components/page-title.tsx"
import { Button } from "@/components/ui/button.tsx"
import { Card, CardContent } from "@/components/ui/card.tsx"
import {
  Command,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command"
import { ImageInput } from "@/components/ui/form/image-input.tsx"
import { SlugInput } from "@/components/ui/form/slug-input.tsx"
import { useApiFetch } from "@/hooks/use-api-fetch"
import { useList } from "@/hooks/use-list"
import { cn } from "@/lib/utils"
import type { OptionItemData, TechnologyFormData } from "@/types"
import { Label } from "@/components/ui/label.tsx"
import {
  Combobox,
  ComboboxContent,
  ComboboxEmpty,
  ComboboxInput,
  ComboboxItem,
  ComboboxList,
} from "@/components/ui/combobox"
import { useDebounceValue } from "usehooks-ts"

type Props = {
  item: TechnologyFormData
}

export default withLayout<Props>(
  ({ item }) => {
    const url = item.slug ? `/tutoriels/${item.slug}` : undefined
    const formAction = item.id ? route.update.form(item.id) : route.store.form()

    return (
      <Form
        className="grid lg:grid-cols-[1fr_300px] gap-4"
        id="form"
        {...formAction}
        encType="multipart/form-data"
      >
        <PageTitle>{item.name || "Nouvelle technologie"}</PageTitle>
        <main>
          <input
            name="name"
            defaultValue={item.name}
            className="mb-1 block w-full font-semibold text-2xl outline-none"
            placeholder="Nom de la technologie"
          />
          <SlugInput
            defaultValue={item.slug}
            prefix="grafikart.fr/tutoriels/"
            url={url}
            className="mb-3"
          />

          <textarea
            className="min-h-100 w-full outline-none"
            placeholder="Description"
            defaultValue={item.content}
            name="content"
          />
        </main>
        <aside className="space-y-4">
          <Card className="pt-0">
            <ImageInput
              defaultValue={item.image ?? undefined}
              name="imageFile"
              className="aspect-video"
            />
            <CardContent className="space-y-4">
              <DeprecatedBySelector defaultValue={item.deprecatedBy ?? null} />
              <RequirementsSelector defaultValue={item.requirements ?? []} />
            </CardContent>
          </Card>
        </aside>
      </Form>
    )
  },
  {
    breadcrumb: (props) => [
      { label: "Technologies", href: route.index() },
      { label: props.item.name || "Nouvelle technologie" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
)

type DeprecatedBySelectorProps = {
  defaultValue: OptionItemData | null
}

function DeprecatedBySelector({ defaultValue }: DeprecatedBySelectorProps) {
  const [search, setSearch] = useDebounceValue("", 300)
  const shouldSearch = search !== ""
  const { data } = useApiFetch<OptionItemData[]>(
    route.index({ query: { q: search } }).url,
    { enabled: shouldSearch, staleTime: 5_000 },
  )

  const [value, setValue] = useState<OptionItemData | null>(defaultValue)

  const items = data ?? []
  return (
    <div className="space-y-2">
      <input type="hidden" name="deprecatedById" value={value?.id ?? ""} />
      <Label className="text-xs font-medium text-muted-foreground uppercase">
        Déprécié par
      </Label>
      <Combobox
        itemToStringLabel={(item: OptionItemData) => item.name}
        items={items}
        defaultValue={value}
        onInputValueChange={setSearch}
        onValueChange={setValue}
      >
        <ComboboxInput placeholder="Select a framework" />
        <ComboboxContent>
          <ComboboxEmpty>Rechercher</ComboboxEmpty>
          <ComboboxList>
            {(item: OptionItemData) => (
              <ComboboxItem key={item.id} value={item}>
                {item.name}
              </ComboboxItem>
            )}
          </ComboboxList>
        </ComboboxContent>
      </Combobox>
    </div>
  )
}

type RequirementsSelectorProps = {
  defaultValue: OptionItemData[]
}

function RequirementsSelector({ defaultValue }: RequirementsSelectorProps) {
  const [items, toggleItem] = useList(defaultValue)
  const [search, setSearch] = useState("")
  const debouncedSearch = useDebounce(search, 300)
  const selectedItemsSet = useMemo(
    () => new Set(items.map((item) => item.id)),
    [items],
  )

  const { data, isFetching } = useApiFetch<OptionItemData[]>(
    route.index({ query: { q: debouncedSearch } }).url,
    {
      enabled: debouncedSearch.length > 1,
      staleTime: 5_000,
    },
  )

  return (
    <div className="space-y-2">
      <Label className="text-xs font-medium text-muted-foreground uppercase">
        Prérequis
      </Label>
      <div className="space-y-3">
        <div className="flex flex-wrap gap-2">
          {items.map((item, k) => (
            <div
              key={item.id}
              className="flex items-center gap-1 rounded-md bg-muted px-2 py-1 text-sm"
            >
              {item.name}
              <Button
                variant="ghost"
                type="button"
                size="icon-xs"
                onClick={() => toggleItem(item)}
                aria-label="Supprimer"
              >
                <UnlinkIcon className="size-3" />
              </Button>
              <input
                type="hidden"
                name={`requirements[${k}]`}
                value={item.id}
              />
            </div>
          ))}
        </div>

        <Command shouldFilter={false} className="relative">
          <CommandList className="absolute top-full left-0 z-10 w-full bg-card">
            <CommandGroup>
              {isFetching && (
                <div className="flex justify-center py-2 text-muted-foreground">
                  <LoaderCircle className="animate-spin" size={16} />
                </div>
              )}
              {data
                ?.filter((item) => !selectedItemsSet.has(item.id))
                .map((item) => (
                  <CommandItem
                    key={item.id}
                    value={item.name}
                    onSelect={() => {
                      toggleItem(item)
                      setSearch("")
                    }}
                  >
                    {item.name}
                    <Check
                      className={cn(
                        "ml-auto",
                        selectedItemsSet.has(item.id)
                          ? "opacity-100"
                          : "opacity-0",
                      )}
                    />
                  </CommandItem>
                ))}
            </CommandGroup>
          </CommandList>
          <CommandInput
            placeholder="Rechercher une technologie"
            onValueChange={setSearch}
            value={search}
          />
        </Command>
      </div>
    </div>
  )
}
