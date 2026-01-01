import { Check, LoaderCircle, PlusIcon, UnlinkIcon } from "lucide-react";

import { Button } from "@/components/ui/button";
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
  CommandLoading,
} from "@/components/ui/command";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { useApiFetch } from "@/hooks/use-api-fetch";
import { useList } from "@/hooks/use-list";
import { cn } from "@/lib/utils";
import { useDebounce } from "@uidotdev/usehooks";
import { useMemo, useState } from "react";
import type { TechnologyData } from "@/types";
import { Input } from "@base-ui/react";

type Props = {
  defaultValue: TechnologyData[];
};

export function TechnologySelector(props: Props) {
  const [items, toggleItem] = useList(props.defaultValue);
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState("");
  const debouncedSearch = useDebounce(search, 300);
  const selectedItemsSet = useMemo(() => new Set(items.map((item) => item.id)), [items]);

  const { data, isFetching } = useApiFetch<TechnologyData[]>(`/api/technologies?q=${debouncedSearch}`, {
    enabled: open,
    staleTime: 5_000,
  });

  return (
    <div className="space-y-3">
      <div className="grid items-center" style={{ gridTemplateColumns: "1fr 80px max-content" }}>
        {items.map((item, k) => (
          <Item k={k} item={item} key={item.id} onToggle={toggleItem} />
        ))}
      </div>
      <Popover open={open} onOpenChange={setOpen}>
        <PopoverTrigger render={<Button variant="secondary" role="combobox" aria-expanded={open} className="w-full" />}>
          <PlusIcon />
          Ajouter une technologie
        </PopoverTrigger>
        <PopoverContent className="w-[200px] p-0">
          <Command shouldFilter={false}>
            <CommandInput placeholder="Rechercher un véhicule" className="h-9" onValueChange={setSearch} />
            <CommandList>
              {isFetching ? (
                <CommandLoading>Recherche...</CommandLoading>
              ) : (
                <CommandEmpty>Aucun véhicule trouvé</CommandEmpty>
              )}
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
                        setOpen(false);
                      }}
                    >
                      {item.name}
                      <Check className={cn("ml-auto", selectedItemsSet.has(item.id) ? "opacity-100" : "opacity-0")} />
                    </CommandItem>
                  ))}
              </CommandGroup>
            </CommandList>
          </Command>
        </PopoverContent>
      </Popover>
    </div>
  );
}

type ItemProps = { item: TechnologyData; k: number; onToggle: (item: TechnologyData) => void };

function Item({ item, onToggle, k }: ItemProps) {
  return (
    <>
      <div className="flex items-center gap-2">{item.name}</div>
      <Input
        type="text"
        className="w-18"
        placeholder="version"
        name={`technologies[${k}][version]`}
        defaultValue={item.version ?? ""}
      />
      <Button
        variant="secondary"
        type="button"
        className="ml-auto"
        onClick={() => onToggle(item)}
        aria-label="Supprimer"
        size="icon"
      >
        <UnlinkIcon />
      </Button>
      <input type="hidden" name={`technologies[${k}][id]`} value={item.id} />
    </>
  );
}
