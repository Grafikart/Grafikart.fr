import { Autocomplete } from '@base-ui/react/autocomplete';
import { QueryClientProvider } from '@tanstack/react-query';
import { useDebounce } from '@uidotdev/usehooks';
import { SearchIcon } from 'lucide-react';
import { useState } from 'react';

import {
    Dialog,
    DialogContent,
    DialogTrigger,
} from '@/components/ui/dialog.tsx';
import { Spinner } from '@/components/ui/spinner.tsx';
import { queryClient, useApiFetch } from '@/hooks/use-api-fetch.ts';
import { useShortcut } from '@/hooks/use-shortcut.ts';
import type { APISearchItem, APISearchResponse } from '@/types';

type Props = { element: HTMLElement };

export function SiteSearch(props: Props) {
    return (
        <QueryClientProvider client={queryClient}>
            <SearchInputInner {...props} />
        </QueryClientProvider>
    );
}

function SearchInputInner({ element }: Props) {
    const [triggerCls] = useState<string>(() => {
        const cls = element.getAttribute('class');
        element.setAttribute('class', 'contents');
        return cls ?? '';
    });
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const debouncedSearch = useDebounce(search, 300);
    const { data, isFetching } = useApiFetch<APISearchResponse>(
        `/api/search?q=${encodeURIComponent(debouncedSearch)}`,
        {
            enabled: debouncedSearch.length > 1,
            staleTime: 5_000,
        },
    );

    const items = data?.items ?? [];
    const iconCls = 'absolute left-3 top-1/2 size-5 -translate-y-1/2';
    useShortcut('K', { ctrlKey: true }, () => {
        setOpen(true);
    });

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger className={triggerCls}>
                <SearchIcon className="size-4" />
            </DialogTrigger>
            <DialogContent
                className="bg-card sm:max-w-s2 w-150 max-w-9/10 top-5 translate-y-0 gap-2 p-2"
                showCloseButton={false}
            >
                <Autocomplete.Root
                    items={items}
                    value={search}
                    onValueChange={setSearch}
                    itemToStringValue={(item) => item.title}
                    filter={null}
                >
                    <div className="relative">
                        <Autocomplete.Input
                            placeholder="Rechercher un contenu"
                            className="input rounded-md pl-10"
                        />
                        {isFetching ? (
                            <Spinner className={iconCls} />
                        ) : (
                            <SearchIcon className={iconCls} />
                        )}
                    </div>

                    <Autocomplete.List hidden={items.length === 0}>
                        {(item: APISearchItem) => (
                            <Autocomplete.Item
                                key={item.url}
                                value={item}
                                className="data-highlighted:bg-background flex gap-4 p-2"
                                render={<a href={item.url} />}
                            >
                                <div className="text-muted w-15 flex-none text-end">
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
                            className="text-muted hover:bg-background py-2 text-center text-sm"
                            href={`/recherche?q=${encodeURIComponent(debouncedSearch)}`}
                        >
                            Voir les {data?.hits} résultats
                        </a>
                    )}
                </Autocomplete.Root>
            </DialogContent>
        </Dialog>
    );
}
