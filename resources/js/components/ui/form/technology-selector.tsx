import { useDebounce } from "@uidotdev/usehooks";
import { Check, LoaderCircle, UnlinkIcon } from "lucide-react";
import { useMemo, useState } from "react";

import TechnologyController from '@/actions/App/Http/Cms/TechnologyController.ts';
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox.tsx";
import { Command, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command";
import { Input } from "@/components/ui/input.tsx";
import { useApiFetch } from "@/hooks/use-api-fetch";
import { useList } from "@/hooks/use-list";
import { cn } from "@/lib/utils";
import type { TechnologyUsageData } from '@/types';

type Props = {
  defaultValue: TechnologyUsageData[];
  id?: string;
};

export function TechnologySelector(props: Props) {
  const [items, toggleItem] = useList(props.defaultValue);
  const [search, setSearch] = useState("");
  const debouncedSearch = useDebounce(search, 300);
  const selectedItemsSet = useMemo(() => new Set(items.map((item) => item.id)), [items]);

  const { data, isFetching } = useApiFetch<TechnologyUsageData[]>(TechnologyController.index({query: {q: debouncedSearch}}).url, {
    enabled: debouncedSearch.length > 1,
    staleTime: 5_000,
  });

  return (
    <fieldset className="space-y-3" id={props.id}>
      <div className="grid items-center gap-y-2" style={{ gridTemplateColumns: "1fr 80px 2rem max-content" }}>
        {items.map((item, k) => (
          <Item k={k} item={item} key={item.id} onToggle={toggleItem} />
        ))}
      </div>

      <Command shouldFilter={false} className="relative">
        <CommandInput placeholder="Rechercher un langage" onValueChange={setSearch} value={search} />
        <CommandList className="absolute top-full left-0 w-full bg-card">
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
      </Command>
    </fieldset>
  );
}

type ItemProps = { item: TechnologyUsageData; k: number; onToggle: (item: TechnologyUsageData) => void };

function Item({ item, onToggle, k }: ItemProps) {
  return (
    <>
      <div className="flex items-center gap-2">{item.name}</div>
      <Input
        type="text"
        autoFocus
        className="w-18"
        placeholder="version"
        name={`technologies[${k}][version]`}
        defaultValue={item.version ?? ""}
      />
      <Checkbox name={`technologies[${k}][primary]`} value="1" defaultChecked={item.primary} />
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
