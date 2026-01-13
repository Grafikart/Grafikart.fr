import type { OptionResource, TechnologyFormData } from "@/types";
import { withLayout } from "@/components/layout.tsx";
import { adminPath } from "@/lib/url.ts";
import { Button } from "@/components/ui/button.tsx";
import { Form } from "@/components/form.tsx";
import { Card, CardContent } from "@/components/ui/card.tsx";
import { Check, LoaderCircle, SaveIcon, UnlinkIcon } from "lucide-react";
import { PageTitle } from "@/components/page-title.tsx";
import { ImageInput } from "@/components/ui/form/image-input.tsx";
import { SlugInput } from "@/components/ui/form/slug-input.tsx";
import { Command, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command";
import { useApiFetch } from "@/hooks/use-api-fetch";
import { useList } from "@/hooks/use-list";
import { useDebounce } from "@uidotdev/usehooks";
import { useMemo, useState } from "react";
import { cn } from "@/lib/utils";

type Props = {
  item: TechnologyFormData;
};

export default withLayout<Props>(
  ({ item }) => {
    const url = item.slug ? `/tutoriels/${item.slug}` : undefined;
    return (
      <Form className="grid grid-cols-[1fr_300px] gap-4" id="form" method="post" encType="multipart/form-data">
        <PageTitle>{item.name || "Nouvelle technologie"}</PageTitle>
        <main>
          <input
            name="name"
            defaultValue={item.name}
            className="text-2xl font-semibold outline-none block mb-1"
            placeholder="Nom de la technologie"
          />
          <SlugInput defaultValue={item.slug} prefix="grafikart.fr/tutoriels/" url={url} />

          <textarea
            className="w-full min-h-100 outline-none"
            placeholder="Description"
            defaultValue={item.content}
            name="content"
          />
        </main>
        <aside className="space-y-4">
          <Card className="pt-0">
            <ImageInput
              defaultValue={item.image ? `/uploads/icons/${item.image}` : undefined}
              name="imageFile"
              className="aspect-video"
            />
            <CardContent>
              <RequirementsSelector defaultValue={item.requirements} />
            </CardContent>
          </Card>
        </aside>
      </Form>
    );
  },
  {
    breadcrumb: (props) => [
      { label: "Technologies", href: adminPath("/technologies") },
      { label: props.item.name || "Nouvelle technologie" },
    ],
    top: (
      <Button form="form" type="submit">
        <SaveIcon /> Enregistrer
      </Button>
    ),
  },
);

type RequirementsSelectorProps = {
  defaultValue: OptionResource[];
};

function RequirementsSelector({ defaultValue }: RequirementsSelectorProps) {
  const [items, toggleItem] = useList(defaultValue);
  const [search, setSearch] = useState("");
  const debouncedSearch = useDebounce(search, 300);
  const selectedItemsSet = useMemo(() => new Set(items.map((item) => item.id)), [items]);

  const { data, isFetching } = useApiFetch<OptionResource[]>(`/api/technologies?q=${debouncedSearch}`, {
    enabled: debouncedSearch.length > 1,
    staleTime: 5_000,
  });

  return (
    <div className="space-y-3">
      <div className="flex flex-wrap gap-2">
        {items.map((item, k) => (
          <div key={item.id} className="flex items-center gap-1 bg-muted px-2 py-1 rounded-md text-sm">
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
            <input type="hidden" name={`requirements[${k}]`} value={item.id} />
          </div>
        ))}
      </div>

      <Command shouldFilter={false} className="relative">
        <CommandList className="absolute top-full left-0 w-full bg-card z-10">
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
                    toggleItem(item);
                    setSearch("");
                  }}
                >
                  {item.name}
                  <Check className={cn("ml-auto", selectedItemsSet.has(item.id) ? "opacity-100" : "opacity-0")} />
                </CommandItem>
              ))}
          </CommandGroup>
        </CommandList>
        <CommandInput placeholder="Rechercher une technologie" onValueChange={setSearch} value={search} />
      </Command>
    </div>
  );
}
